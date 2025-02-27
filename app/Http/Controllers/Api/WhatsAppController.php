<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function sendMessage(Request $request)
    {
        $response = Http::post('http://localhost:3001/send-message', [
            'number' => $request->number,
            'message' => $request->message,
        ]);

        return $response->json();
    }
}
