<?php
namespace App\Classes;

use DB;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Line;
use App\Models\Node;
use App\Models\NodeInterchange;
use App\Models\Route;
use App\Models\RouteFare;
use App\Models\RouteIndirectInterchange;
use App\Models\RouteTransitPoint;

class RouteManager {
    
    function __construct() {

    }

    function retrieveBestRoute(Node $nodeOne, Node $nodeTwo) {
        $isFromNodeTwo = false;

        // if nodeOne is higher than nodeTwo, swap their position, and specify that isFromNodeTwo = true
        if ($nodeOne->id > $nodeTwo->id){
            [$nodeOne, $nodeTwo] = [$nodeTwo, $nodeOne];
            $isFromNodeTwo = true;
        }

        $nodeFrom = ($nodeOne->id <= $nodeTwo->id) ? $nodeOne : $nodeTwo;
        $nodeTo = ($nodeOne->id > $nodeTwo->id) ? $nodeOne : $nodeTwo;

        // case 1, no line interchange
        if ($nodeFrom->line_id == $nodeTo->line_id) {
            $paths = $this->retrieveDirectPath($nodeFrom, $nodeTo);
            $paths = $this->pathReorder($paths, $isFromNodeTwo);

            return $paths;
        }

        $directInterchanges = $this->retrieveDirectInterchangeOptions($nodeFrom->line_id, $nodeTo->line_id);
        
        // case 2,  direct interchange (only transit once option is available)
        if (count($directInterchanges)) {
            $pathsList = $this->retrieveDirectTransitPaths($directInterchanges, $nodeFrom, $nodeTo);
            $paths = $this->retrieveShortestPath($pathsList);
            $paths = array_values($this->pathReorder($paths->toArray(), $isFromNodeTwo)); 

            return $paths;
        } 

        // case 3, multiple transit interchanges
        $paths = $this->retrieveMultipleTransitPaths($nodeFrom, $nodeTo);
        $paths = $this->pathReorder($paths, $isFromNodeTwo); 
        return $paths;
    }

    function retrieveDirectPath(Node $nodeFrom, Node $nodeTo) {
        $nodes = $this->retrieveSurroundingSameLineNodes($nodeFrom, $nodeTo);

        return $nodes;
    }

    function retrieveDirectTransitPaths($directTransits, Node $nodeFrom, Node $nodeTo) {
        $pathsList = [];

        foreach ($directTransits as $transit) {
            $nodeFromTransit = Node::where('id', $transit->node_from_id)->first();
            $nodeFromSurroundingNodes = $this->retrieveSurroundingSameLineNodes($nodeFrom, $nodeFromTransit);
            
            $nodeToTransit = Node::where('id', $transit->node_to_id)
                ->first();
            $nodeToSurroundingNodes = $this->retrieveSurroundingSameLineNodes($nodeToTransit, $nodeTo);

            // merge to form one full paths
            $paths = $nodeFromSurroundingNodes->concat($nodeToSurroundingNodes);

            array_push($pathsList, $paths);
        }

        return $pathsList;
    }

    function retrieveMultipleTransitPaths($nodeFrom, $nodeTo) {
        $routeTransitPoint = RouteTransitPoint::where('node_from_id', $nodeFrom->id)
            ->where('node_to_id', $nodeTo->id)
            ->first();

        DB::transaction(function() use(&$nodeFrom, &$nodeTo, &$pathsList, &$routeTransitPoint) {
            for ($i = 1; $i <= $routeTransitPoint->total_interchanges; $i++) {
                $interchangeId = "interchange_{$i}_id";
                $interchange = NodeInterchange::find($routeTransitPoint->$interchangeId);

                if ($i == 1) {
                    $nodeDestinationNowId = ($nodeFrom->line_id == $interchange->line_from_id) ? $interchange->node_from_id : $interchange->node_to_id;
                    $nodeDestinationNow = Node::find($nodeDestinationNowId);

                    $pathsList = $this->retrieveSurroundingSameLineNodes($nodeFrom, $nodeDestinationNow);

                    $nodeDestinationNextId = ($nodeFrom->line_id != $interchange->line_from_id) ? $interchange->node_from_id : $interchange->node_to_id;
                    $nodeDestinationNext = Node::find($nodeDestinationNextId);
                    continue;
                }

                // need to detect reverse ordering.
                $nodeDestinationNowId = ($interchange->line_from_id == $nodeDestinationNext->line_id) ? $interchange->node_from_id : $interchange->node_to_id;
                $nodeDestinationNow = Node::find($nodeDestinationNowId);

                $paths = $this->retrieveSurroundingSameLineNodes($nodeDestinationNext, $nodeDestinationNow);
                $pathsList = $pathsList->concat($paths);
                
                $nodeDestinationNextId = ($interchange->line_from_id != $nodeDestinationNext->line_id) ? $interchange->node_from_id : $interchange->node_to_id;
                $nodeDestinationNext = Node::find($nodeDestinationNextId);

                if ($i == $routeTransitPoint->total_interchanges) {
                    $paths = $this->retrieveSurroundingSameLineNodes($nodeDestinationNext, $nodeTo);
                    $pathsList = $pathsList->concat($paths);
                    
                    continue;
                }
            }

        });

        return $pathsList;        
    }

    function retrieveShortestPath($pathsList) {
        if (count($pathsList) == 1) {
            return $pathsList[0];
        } else {
            $shortestPathKey = min(array_keys($pathsList, min($pathsList)));

            return $pathsList[$shortestPathKey];
        }
    }

    function retrieveSurroundingSameLineNodes(Node $nodeFrom, Node $nodeTo) {
        $higherSequence = ($nodeFrom->sequence > $nodeTo->sequence) ? $nodeFrom->sequence : $nodeTo->sequence;
        $lowerSequence = ($nodeFrom->sequence > $nodeTo->sequence) ? $nodeTo->sequence : $nodeFrom->sequence;

        $nodes = Node::where('line_id', $nodeFrom->line_id)
            ->where('sequence', '>=' , $lowerSequence)
            ->where('sequence', '<=', $higherSequence)
            ->with('line')
            ->get();
            // if first node of the nodes is not nodeFrom, then reverse the entire order
        if ($nodes[0]->id != $nodeFrom->id) {
            $nodes = $nodes->reverse();
        }

        return $nodes;
    }

    function retrieveDirectInterchangeOptions($lineOne, $lineTwo) {
        $nodeInterchangesCaseOne = NodeInterchange::where('line_from_id', $lineOne)
            ->where('line_to_id', $lineTwo)
            ->get();
        
        $nodeInterchangesCaseTwo = NodeInterchange::where('line_from_id', $lineTwo)
            ->where('line_to_id', $lineOne)
            ->get();

        $nodeInterchangesAll = $nodeInterchangesCaseOne->merge($nodeInterchangesCaseTwo);

        return $nodeInterchangesAll;
    }

    function generateTransitPoint() {
        // retrieve all route indirect interchanges records  
        RouteTransitPoint::query()->delete();        

        $indirectInterchanges = RouteIndirectInterchange::all();

        foreach ($indirectInterchanges as $indirectInterchange) {
            $lineFromNodes = Node::where('line_id', $indirectInterchange->line_id_from)
                ->where('sequence', '>=', $indirectInterchange->sequence_from_start)
                ->where('sequence', '<=', $indirectInterchange->sequence_from_end)
                ->get();
            $lineToNodes = Node::where('line_id', $indirectInterchange->line_id_to)
                ->where('sequence', '>=', $indirectInterchange->sequence_to_start)
                ->where('sequence', '<=', $indirectInterchange->sequence_to_end)
                ->get();

            $this->updateTransitRoutePaths($lineFromNodes, $lineToNodes, $indirectInterchange);
        }

        // loop through all route indirect interchanges records
            // based on total transit get the first and last interchange
    }

    // update routetransitpoint table data with reference to a routeindirectinterchange record
    function updateTransitRoutePaths($fromNodes, $toNodes, $interchangeRef) {
        DB::transaction(function() use(&$fromNodes, &$toNodes, &$interchangeRef) {
            foreach ($fromNodes as $fromNode) {
                foreach ($toNodes as $toNode) {
                    $smallerNode = ($fromNode->id <= $toNode->id) ? $fromNode : $toNode; 
                    $biggerNode = ($fromNode->id > $toNode->id) ? $fromNode : $toNode; 

                    if ($smallerNode->line_id == $interchangeRef->line_id) {
                        RouteTransitPoint::create([
                            'node_from_id' => $smallerNode->id,
                            'node_to_id' => $biggerNode->id,
                            'line_from_id' => $smallerNode->line_id,
                            'line_to_id' => $biggerNode->line_id,
                            'total_interchanges' => $interchangeRef->total_transit,
                            'interchange_1_id' => $interchangeRef->transit_1_interchange_id,
                            'interchange_2_id' => $interchangeRef->transit_2_interchange_id,
                            'interchange_3_id' => $interchangeRef->transit_3_interchange_id,
                            'interchange_4_id' => $interchangeRef->transit_4_interchange_id,
                            'interchange_5_id' => $interchangeRef->transit_5_interchange_id,
                            'interchange_6_id' => $interchangeRef->transit_6_interchange_id,
                            'interchange_7_id' => $interchangeRef->transit_7_interchange_id,
                            'interchange_8_id' => $interchangeRef->transit_8_interchange_id,
                            'interchange_9_id' => $interchangeRef->transit_9_interchange_id,
                            'interchange_10_id' => $interchangeRef->transit_10_interchange_id,                        
                        ]);
                        continue;
                    }

                    $interchangeIds = [];
                    $x = 1;                    

                    for ($i = $interchangeRef->total_transit; $i >= 1; $i--) {
                        $target = "transit_{$i}_interchange_id";
                        $interchangeIds["interchange_{$x}_id"] = $interchangeRef->$target;

                        $x++;
                    }
                    $data = [
                        'node_from_id' => $smallerNode->id,
                        'node_to_id' => $biggerNode->id,
                        'line_from_id' => $smallerNode->line_id,
                        'line_to_id' => $biggerNode->line_id,
                        'total_interchanges' => $interchangeRef->total_transit
                    ];
                    $data = array_merge($data, $interchangeIds);

                    RouteTransitPoint::create($data);
                }
            }
        });
    }

    function pathReorder($paths, $isReorder) {
        if (!$isReorder){ return $paths; }

        return array_reverse($paths->toArray());
    }
}