<?php 

namespace App\Http\Controllers\Simulation;

use DB;
use Exception;
use View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Classes\RouteManager;
use App\Models\Line;
use App\Models\Node;
use App\Models\Route;
use App\Models\NodeInterchange;

class RouteController extends Controller {


    function displayRoutes(Request $request, Node $nodeOne, Node $nodeTwo) {
        $routesM = new RouteManager();
        $routes = $routesM->retrieveBestRoute($nodeOne, $nodeTwo);

        return view('simulation.routes', [
            'routes' => $routes,
            'nodeFrom' => $nodeOne,
            'nodeTo' => $nodeTwo
        ]);
    }

    function generateRouteByNodeId(Node $node) {
        $nodes = Node::all();
        $nextNode = $node->id + 1;
        
        DB::transaction(function () use (&$nodes, &$node){
            foreach ($nodes as $currentNode) {
                $nodeFrom = ($node->id <= $currentNode->id) ? $node->id : $currentNode->id;
                $nodeTo = ($node->id > $currentNode->id) ? $node->id : $currentNode->id;

                $route = Route::where('node_from_id', $nodeFrom)
                    ->where('node_to_id', $nodeTo)
                    ->first();
                
                if (!$route) {
                    Route::create([
                        'node_from_id' => $nodeFrom,
                        'node_to_id' => $nodeTo
                    ]);
                }
            }
        });

        // return back();
        return redirect("simulation/fares/$nextNode/cashless");
        
    }

}