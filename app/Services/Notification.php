<?php

namespace App\Service;

use GuzzleHttp\Client;

class Notification
{
    public static function sendNotification($fcm_id, $body, $title, $name = null)
    {
        // dd(config('key.FIREBASE_SERVER_KEY'));
        if ($fcm_id != null) {
            $client = new Client(['base_uri' => 'https://fcm.googleapis.com']);

            $response = $client->request('POST', '/fcm/send', [
                'headers' => [
                    'Authorization' => 'key= ' . config('key.FIREBASE_SERVER_KEY'),
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'to' => "$fcm_id",
                ],
            ]);
            return $response;
        }
    }
}
