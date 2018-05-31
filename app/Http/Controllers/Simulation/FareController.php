<?php 

namespace App\Http\Controllers\Simulation;

use DB;
use Exception;
use View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\Line;
use App\Models\Node;
use App\Models\Route;
use App\Models\RouteFare;

class FareController extends Controller {

    function getCashlessFaresByNodeId(Request $request, Node $node) {
        $routesAvailable = $this->checkRoutesAvailability($node->id);
        $incompleteRoute = ($this->countRoutesByNodeId($node->id) !== Node::count())? true: false;

        $fares = $this->getCashlessFaresByNodeIdSql($node->id);
        $fares = array_filter($fares, function($value){
            return ($value->node_from_line_id != 7 && $value->node_from_line_id != 8 && 
                $value->node_to_line_id != 7 && $value->node_to_line_id != 8);
        });
        
        return view('simulation.fares.cashless', [
            'node' => $node,
            'fares' => $fares,
            'routesAvailable' => $routesAvailable,
            'incompleteRoute' => $incompleteRoute
        ]);
    }

    // TO BE UPDATED
    // function createOrUpdateCashlessFares(Request $request) {
    //     $nodeFromId = ($request->node_from_id <= $request->node_to_id) ? $request->node_from_id : $request->node_to_id;
    //     $nodeToId = ($request->node_from_id > $request->node_to_id) ? $request->node_from_id : $request->node_to_id;

    //     $route = Route::where('node_from_id', $nodeFromId)
    //         ->where('node_to_id', $nodeToId)
    //         ->first();
    //     if (!$route) { return; }

    //     $type = '';

    //     switch ($request->fare_type) {
    //         case 'cash': $type = 'cash'; break;
    //         case 'cashless': $type = 'cashless'; break;
    //         case 'concession': $type = 'concession'; break;
    //         case 'monthly': $type = 'monthly'; break;
    //         case 'weekly': $type = 'weekly'; break;
    //         default : $type = 'cash';
    //     }
    //     $existingFare = $this->getFare($route->id, $type);
    //     if ($existingFare) {
    //         $existingFare->update(['fare' => (float)$request->fare]);
    //     } else {
    //         RouteFare::create([
    //             'route_id' => $route->id,
    //             'fare_type' => $type,
    //             'fare' => (float)$request->fare
    //         ]);
    //     }

    //     return back();
    // }

    // function getFare($routeId, $type) {
    //     $fare = RouteFare::where('route_id' , $routeId)
    //         ->where('fare_type', $type)
    //         ->first();
        
    //     return $fare;
    // }

    function generateRouteFare() {
        $routes = Route::all();

        DB::transaction(function() use(&$routes){
            foreach ($routes as $route) {
                RouteFare::create([
                    'route_id' => $route->id
                ]);
            }
        });
    }

    function checkRoutesAvailability($nodeId) {
        $routes = $this->getRoutesByNodeIdSql($nodeId);

        if (!count($routes)) { return false; }
        return true;
    }

    function countRoutesByNodeId($nodeId) {
        $routes = $this->getRoutesByNodeIdSql($nodeId);
        
        return count($routes);
    }

    function getRoutesByNodeIdSql($nodeId) {
        $sql = "
            SELECT r.*
                FROM `t0204_route` r
                    WHERE r.`node_from_id` = :nodeId1
                        OR r.`node_to_id` = :nodeId2
        ";

        return DB::select($sql, [
            'nodeId1' => $nodeId,
            'nodeId2' => $nodeId
        ]);
    }

    function getCashlessFaresByNodeIdSql($nodeId) {
        $sql = "
            SELECT r.`id` AS 'route_id',
                r.`node_from_id`, r.`node_to_id`,
                nf.`name` AS 'node_from_name', 
                nf.`name_ref` AS 'node_from_name_ref',
                nf.`line_id` AS 'node_from_line_id',
                lf.`name` AS 'node_from_line_name',
                lf.`reference` AS 'node_from_ref',
                nt.`name` AS 'node_to_name', 
                nt.`name_ref` AS 'node_to_name_ref',
                nt.`line_id` AS 'node_to_line_id',
                lt.`name` AS 'node_to_line_name',
                lt.`reference` AS 'node_to_ref',
                rf.`fare`          

                FROM `t0204_route` r
                    LEFT JOIN `t0202_node` nf
                    ON nf.`id` = r.`node_from_id`

                    LEFT JOIN `t0202_node` nt
                    ON nt.`id` = r.`node_to_id`

                    LEFT JOIN `t0201_line` lf
                    ON nf.`line_id` = lf.`id`

                    LEFT JOIN `t0201_line` lt
                    ON nt.`line_id` = lt.`id`
                    
                    LEFT JOIN `t0205_route_fare` rf
                    ON r.`id` = rf.`route_id`
                    AND rf.`fare_type` = 'cashless'

                WHERE r.`node_from_id` = :nodeId1
                OR r.`node_to_id` = :nodeId2

                ORDER BY nt.`id` DESC
        ";

        return DB::select($sql,[
            'nodeId1' => $nodeId,
            'nodeId2' => $nodeId
        ]);
    }

    // function getFaresByNodeId(Request $request, Node $node) {
    //     $nodesFares = $this->getNodeFaresSql($node);

    //     return $node;
    // }

    // function getNodeFaresSql($node) {
    //     $sql = "
    //         SELECT 
    //             n.`id`, n.`line_id, n.`name`, n.`name_ref`,
    //             FROM `t0202_node` n
                    
    //     ";

    //     return DB::select($sql, [
    //         // 'nodeId1' => $node->id
    //     ]);
    // }
}