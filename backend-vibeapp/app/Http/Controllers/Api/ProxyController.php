<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ProxyController extends Controller
{
    public function proxy(Request $request)
    {
        $url = $request->query('url');

        if (!$url) {
            return response()->json(['error' => 'URL parameter is required'], 400);
        }

        $allowedDomains = [
            'api.deezer.com',
            'itunes.apple.com',
            'www.googleapis.com',
            'localhost',
            '127.0.0.1',
        ];

        $host = parse_url($url, PHP_URL_HOST);
        if (!in_array($host, $allowedDomains)) {
            return response()->json(['error' => 'Domain not allowed: ' . $host], 403);
        }

        try {
            $client = new Client();
            $response = $client->get($url);
            $contentType = $response->getHeaderLine('Content-Type') ?: 'application/json';
            return response($response->getBody()->getContents())
                ->header('Content-Type', $contentType)
                ->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}