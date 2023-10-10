<?php
namespace GoogleOneTap\Services;

use Illuminate\Http\Request;

class GoogleOneTapService
{
    public static function getToken(Request $request){
        if ($_COOKIE['g_csrf_token'] !== $request->input('g_csrf_token')) {
            return back();
        }

        $idToken = $request->input('credential');

        return $idToken;
    }
}
