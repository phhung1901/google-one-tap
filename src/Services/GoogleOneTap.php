<?php
namespace GoogleOneTap\Services;

use Google_Client;
use Illuminate\Http\Request;

class GoogleOneTap
{
    public function index(Request $request){
        if ($_COOKIE['g_csrf_token'] !== $request->input('g_csrf_token')) {
            // Invalid CSRF token
            return back();
        }

        $idToken = $request->input('credential');

        $client = new Google_Client([
            'client_id' => config('services.google.client_id')
        ]);

        $payload = $client->verifyIdToken($idToken);

        if (!$payload) {
            // Invalid ID token
            return back();
        }

        dd($payload);
    }
}