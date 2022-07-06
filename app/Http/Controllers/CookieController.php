<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CookieController extends Controller
{
    public function setCookie(Request $request) {
        $minutes = 100000;
        $response = new Response('Cookie set Successfully.');
        $response->withCookie(cookie('admin', rand().rand(), $minutes));
        return $response;
     }
     public function getCookie(Request $request) {
        $value = $request->cookie('admin');
        echo $value;
}
}
