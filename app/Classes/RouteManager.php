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
            $paths = $this->pathReorder($paths, $isFromNodeTwo);            

            return $paths;
        } else {    
            $pathsList = $this->retrieveMultipleTransitPaths($nodeFrom, $nodeTo);
            // multiple transit logic
        }
    }

    function retrieveDirectPath(Node $nodeFrom, Node $nodeTo) {
        $nodes = $this->retrieveSurroundingNodes($nodeFrom, $nodeTo);

        return $nodes;
    }

    function retrieveDirectTransitPaths($directTransits, Node $nodeFrom, Node $nodeTo) {
        $pathsList = [];

        foreach ($directTransits as $transit) {
            $nodeFromTransit = Node::where('id', $transit->node_from_id)->first();
            $nodeFromSurroundingNodes = $this->retrieveSurroundingNodes($nodeFrom, $nodeFromTransit);
            
            $nodeToTransit = Node::where('id', $transit->node_to_id)
                ->first();
            $nodeToSurroundingNodes = $this->retrieveSurroundingNodes($nodeToTransit, $nodeTo);

            // merge to form one full paths
            $paths = $nodeFromSurroundingNodes->concat($nodeToSurroundingNodes);

            array_push($pathsList, $paths);
        }

        return $pathsList;
    }

    function retrieveMultipleTransitPaths($nodeFrom, $nodeTo) {
        // retrieve all nodeFrom node interchanges      
        dd(["complete the multiple transit logic"]);  
        $interchanges = $this->retrieveLineInterchanges($nodeFrom->line_id);

        // loop 1 :
        foreach ($interchanges as $interchange) {
            $lineId = ($interchange->line_from_id == $nodeFrom->line_id)? $interchange->line_to_id : $interchange->line_from_id;
            $nextInterchanges = $this->retrieveLineInterchanges($lineId);

            // loop 2
            foreach ($nextInterchanges as $nextInterchange) {

            }

        }
    }

    function retrieveLineInterchanges($lineId) {
        $nodeInterchangesCaseOne = NodeInterchange::where('line_from_id', $lineId)->get();
        $nodeInterchangesCaseTwo = NodeInterchange::where('line_to_id', $lineId)->get();

        $nodeInterchangesAll = $nodeInterchangesCaseOne->merge($nodeInterchangesCaseTwo);

        return $nodeInterchangesAll;
    }

    function retrieveShortestPath($pathsList) {
        if (count($pathsList) == 1) {
            return $pathsList[0];
        } else {
            $shortestPathKey = min(array_keys($pathsList, min($pathsList)));

            return $pathsList[$shortestPathKey];
        }
    }

    function retrieveSurroundingNodes(Node $nodeFrom, Node $nodeTo) {
        $higherSequence = ($nodeFrom->sequence > $nodeTo->sequence) ? $nodeFrom->sequence : $nodeTo->sequence;
        $lowerSequence = ($nodeFrom->sequence > $nodeTo->sequence) ? $nodeTo->sequence : $nodeFrom->sequence;

        $nodes = Node::where('line_id', $nodeFrom->line_id)
            ->where('sequence', '>=' , $lowerSequence)
            ->where('sequence', '<=', $higherSequence)
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

    function pathReorder($paths, $isReorder) {
        if (!$isReorder){ return $paths; }

        return $paths->reverse();
    }
}