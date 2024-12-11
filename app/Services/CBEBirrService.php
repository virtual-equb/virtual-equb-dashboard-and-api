<?php

namespace App\Services;

class CBEBirrService
{
    protected $key;

    public function __construct()
    {
        $this->key = config('app.cbebirr_api_key'); // Load API Key from config/env
    }

    public function preparePayload($data)
    {
        $sortedData = collect($data)->sortKeys();
        $payloadString = $sortedData->map(function($value, $key) {
            return "{$key}={$value}";
        })->join('&');
        return $payloadString;
    }

    public function hashPayload($payloadString)
    {
        return hash('sha256', $payloadString); // Adjust based on the hashing algorithm provided
    }

    public function encrypt($data)
    {
        return openssl_encrypt($data, 'aes-256-cbc', $this->key, 0, $this->getIv());
    }

    public function decrypt($encryptedData)
    {
        return openssl_decrypt($encryptedData, 'aes-256-cbc', $this->key, 0, $this->getIv());
    }

    private function getIv()
    {
        return substr(hash('sha256', $this->key), 0, 16); // Adjust IV length as per encryption method
    }
}