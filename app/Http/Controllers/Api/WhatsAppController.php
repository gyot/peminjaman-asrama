<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    
    public function sendMessage(Request $request)
    {
        $response = Http::post('https://2a2e-36-92-8-106.ngrok-free.app', [
            'number' => $request->number,
            'message' => $request->message,
        ]);

        return $response->json();
    }
}
