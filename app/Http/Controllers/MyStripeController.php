<?php

namespace App\Http\Controllers;
use Stripe\Stripe;
use Stripe\Charge;
use Illuminate\Support\Facades\Log;

class MyStripeController extends Controller
{

  /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        
        Log::info('[MyStripeController][store]');
        Log::info("[APILawyer][registar] Método Recibido: ". $request->getMethod());

        if($request->isMethod('GET')) {

            header('Access-Control-Allow-Origin: *');

            $tipo_usuario = $request->input('tipo_usuario');
            $id_despacho = $request->input('id_despacho');
            
            if ($tipo_usuario == '0') {

                $stripe = Stripe::charges()->create([
                    'source' => $request->get('id'),
                    'currency' => 'MXN',
                    'amount' => '500'
                ]);
    
                return $stripe;
                
            } else if ($id_despacho != '0'){

                $stripe = Stripe::charges()->create([
                    'source' => $request->get('id'),
                    'currency' => 'MXN',
                    'amount' => '1500'
                ]);
    
                return $stripe;

            }


        }

    }
}
  