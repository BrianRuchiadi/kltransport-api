<?php 

namespace App\Http\Controllers\User\Api;

use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Classes\OrderManager;
use NoProtocol\Encryption\MySQL\AES\Crypter;
use MySQLHandler\MySQLHandler;
use Monolog\Logger;

class OrderController extends Controller {

    function logger(Request $request) {
        $pdo = DB::connection()->getPdo();
        $logErrMaster = new Logger('OrderController');

        $logErrMaster->pushHandler(new MySQLHandler($pdo, "t9901_log_error_master", [], Logger::DEBUG));
    }

    function dummy(Request $request) {
        return 'dummy routes on order controller';
    }
}