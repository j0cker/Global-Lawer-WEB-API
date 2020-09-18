<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use App\Http\Requests;
use Illuminate\Http\Request;
use Stripe\Error\Card;
use Stripe\Stripe;
use Stripe\Charge;

class MyStripeController extends Controller
{

    public function chargeStripe(Request $request)
    {
        Log::info('[MyStripeController][chargeStripe]');
        Log::info("[MyStripeController][chargeStripe] Método Recibido: ". $request->getMethod());
        
        if($request->isMethod('POST')) {
            
            Log::info('[MyStripeController][chargeStripe]');
            
            //header('Access-Control-Allow-Origin: *');
            Stripe::setApiKey(env('STRIPE_SECRET'));
     
            //$token = request('stripeToken');
     
            $charge = Charge::create([
                'source' => $request->get('id'),
                'currency' => 'MXN',
                'amount' => 500*100
            ]);
    
            return $charge;
            
        }
    }
    
}
  