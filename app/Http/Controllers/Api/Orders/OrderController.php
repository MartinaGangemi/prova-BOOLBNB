<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\OrderRequest;
use App\Models\Sponsorship;
use Braintree\Gateway;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function generate(Request $request,Gateway $gateway){
        $token = $gateway->clientToken()->generate();


        $data = [
            'success' => true,
            'token' => $token
        ];

        return response()->json($data,200);
    }

    public function makePayment(OrderRequest $request,Gateway $gateway){

        $sponsorship = Sponsorship::find($request->sponsorship);

        $result = $gateway->transaction()->sale([
            'amount' => $sponsorship->price,
            'paymentMethodNonce' => $request->token,
            'options' => [
                'submitForSettlement' => true
            ]
        ]);

        if($result->success){
            $data = [
                'success' => true,
                'message' => "transazione eseguita con successo!"
            ];
            return response()->json($data,200);
        }else{
            $data = [
                'success' => false,
                'message' => "transazione Fallita"
            ];
            return response()->json($data,401);
        }
    }
}