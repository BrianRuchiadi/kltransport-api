<?php
namespace App\Classes;

use DB;
use Exception;
use Illuminate\Http\Request;
use Carbon\Carbon;

// use App\Models\XXX;

class PaymentManager {
    
    function __construct() {

    }

    // Process action after payment success status received from payment gateway
    public function paymentSuccess(Request $request, $ticket) {
        DB::transaction( function() use($request, &$ticket){
            return $ticket;
        });

        return $ticket;
    }

    // Process action after payment failed status received from payment gateway
    public function paymentFailed(Request $request) {
        $payment = null;

        DB::transaction( function() use($request, &$payment){
            return $payment;
        });

        return $payment;
    }
}