<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TTTTController extends Controller
{
    public function envApi(){
        $envapidata = ['ACCESS_TOKEN_INSTALL_SPEND'=>env('ACCESS_TOKEN_INSTALL_SPEND'),'ACCESS_TOKEN_PAGE'=>env('ACCESS_TOKEN_PAGE'),'ACCESS_TOKEN_CLOSECAMPAIGN'=>env('ACCESS_TOKEN_CLOSECAMPAIGN'),'ACCESS_TOKEN_FAN'=>env('ACCESS_TOKEN_FAN')];
        return json_encode($envapidata);
    }
}
