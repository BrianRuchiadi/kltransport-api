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
use App\Models\Line;

class StationController extends Controller {

    function getStations(Request $request) {
        $nodes = Line::where('is_active', 1)->with('nodes')->get();

        return $nodes;
    }
}