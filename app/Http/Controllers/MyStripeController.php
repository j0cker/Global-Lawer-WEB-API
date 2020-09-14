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

    public function stripe()

    {

        return view('stripe');

    }


    public function pay(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
 
        $token = request('stripeToken');
 
        $charge = Charge::create([
            'amount' => 500,
            'currency' => 'mxn',
            'description' => 'Test Book',
            'source' => $token,
        ]);
 
        return 'Payment Success!';
    }
    
}
  