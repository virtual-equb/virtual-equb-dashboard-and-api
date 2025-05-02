<?php

namespace App\Helpers;

use Exception;
use phpseclib3\Crypt\RSA;

class SignHelper
{
    public static function sign($request)
    {
        $exclude_fields = ["sign", "sign_type", "header", "refund_info", "openType", "raw_request"];
        $data = (array)$request; // Convert stdClass to array
        ksort($data);
        $stringApplet = '';

        foreach ($data as $key => $values) {
            if (in_array($key, $exclude_fields)) {
                continue;
            }

            if ($key === "biz_content") {
                // Since biz_content is an array, we handle it accordingly
                if (is_array($values)) {
                    foreach ($values as $value => $single_value) {
                        // Build the string for biz_content
                        $stringApplet .= $stringApplet === '' ? "$value=$single_value" : "&$value=$single_value";
                    }
                } else {
                    echo "biz_content is not an array.";
                }
            } else {
                // Handle non-biz_content fields
                $stringApplet .= $stringApplet === '' ? "$key=$values" : "&$key=$values";
            }
        }

        // Sort the string to create the final string for signing
        $sortedString = self::sortedString($stringApplet);

        return self::SignWithRSA($sortedString);
    }

    public static function sortedString($stringApplet)
    {
        $sortedArray = explode("&", $stringApplet);
        sort($sortedArray);
        return implode('&', $sortedArray);
    }

    public static function SignWithRSA($data)
    {
        try{
        // Retrieve private key from .env
        $private_key = config('key.PRIVATE_KEY');

        if (empty($private_key)) {
            echo "Error: Private key is not set in the .env file.";
            return;
        } 
         // Load the private key into RSA
        $rsa = RSA::loadPrivateKey($private_key);
 
        // Sign the data with SHA-256 hash
        $signature = $rsa->sign($data, 'sha256'); // Pass the hash algorithm directly here

        // Return base64 encoded signature
        return base64_encode($signature);
        }catch(Exception $e){
           throw $e;
        }
    }

    public static function createMerchantOrderId()
    {
        return (string) time(); // Unique merchant order ID
    }

    public static function createTimeStamp()
    {
        return (string) time(); // Current timestamp
    }

    public static function createNonceStr()
    {
        $chars = array_merge(range(0, 9), range('A', 'Z'));
        $str = '';
        for ($i = 0; $i < 32; $i++) {
            $str .= $chars[rand(0, count($chars) - 1)];
        }
        return $str;
    }
}