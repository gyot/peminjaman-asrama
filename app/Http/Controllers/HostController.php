<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Host;

class HostController extends Controller
{
    //
    public function getHost() {
        $host=Host::all();
        return json_encode($host);
    }
}
