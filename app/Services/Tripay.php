<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Tripay {
    public $merchantCode;
    public $privateKey;
    public $mode;
    public $apiKey;

    public function __construct()
    {
        $cfg = config('services.tripay');
        $this->merchantCode = $cfg['merchant'];
        $this->privateKey = $cfg['private'];
        $this->mode = $cfg['mode'];
        $this->apiKey = $cfg['api'];
    }

    public function signature($props) {
        return hash_hmac('sha256', $this->merchantCode.$props['merchant_ref'].$props['amount'], $this->privateKey);
    }

    public function pay($props) {
        $endpoint = strtoupper($this->mode) === "LIVE" ?
            "https://tripay.co.id/api/transaction/create"
        :
            "https://tripay.co.id/api-sandbox/transaction/create";

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}"
        ])
        ->post($endpoint, $props)
        ->json();

        return $response;
    }
}