<?php 

namespace App\Http\Controllers\User\Api;

use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use NoProtocol\Encryption\MySQL\AES\Crypter;
use MySQLHandler\MySQLHandler;
use Monolog\Logger;

use App\Classes\RouteManager;

use App\Models\Node;
use App\Models\RouteFare;
use App\Models\Route;

class RouteController extends Controller {

    // function logger(Request $request) {
    //     $pdo = DB::connection()->getPdo();
    //     $logErrMaster = new Logger('OrderController');

    //     $logErrMaster->pushHandler(new MySQLHandler($pdo, "t9901_log_error_master", [], Logger::DEBUG));
    // }

    function getRouteDetails(Request $request, Node $nodeOne, Node $nodeTwo) {
        $nodeFrom = ($nodeOne->id <= $nodeTwo->id)? $nodeOne : $nodeTwo;
        $nodeTo = ($nodeOne->id > $nodeTwo->id)? $nodeOne : $nodeTwo;

        $bestRoute = null;
        $fare = null;

        DB::transaction(function() use(&$nodeFrom, &$nodeTo, &$nodeOne, &$nodeTwo, &$bestRoute, &$fare) {
            $routeM = new RouteManager();
            $route = Route::where('node_from_id', $nodeFrom->id)
                ->where('node_to_id', $nodeTo->id)
                ->first();

            if (!$route){ return; } 

            $bestRoute = $routeM->retrieveBestRoute($nodeOne, $nodeTwo);
            // dd(['best route now', $bestRoute]);
            $fare = RouteFare::where('node_from_id', $nodeFrom->id)
                ->where('node_to_id', $nodeTo->id)
                ->first();;
        });

        return response()->json([
            'bestRoute' => $bestRoute,
            'fare' => $fare
        ]);
    }
}