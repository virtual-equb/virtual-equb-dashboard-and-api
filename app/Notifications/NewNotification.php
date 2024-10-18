<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Spatie\FlareClient\Http\Client as HttpClient;

class NewNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private string $notification)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['firebase'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toFirebase($notifiable)
    {
        $client = new Http([
            'headers' => [
                'Authorization' => 'key=' . env('FIREBASE_SERVER_KEY'),
                'Content-Type' => 'application/json',
            ],
        ]);

        $body = [
            'notification' => [
                'title' => 'Notification Title',
                'body' => 'Notification Body'
            ],
            'to' => 'device_token'
        ];

        $response = $client->post('https://fcm.googleapis.com/fcm/send', [
            'body' => json_encode($body)
        ]);


        // $client = new Client([
        //     'headers' => [
        //         'Authorization' => 'key=' . env('FIREBASE_SERVER_KEY'),
        //         'Content-Type' => 'application/json',
        //     ],
        // ]);

        // $response = $client->post('https://fcm.googleapis.com/fcm/send', [
        //     'json' => [
        //         'to' => $notifiable->fcm_token,
        //         'notification' => [
        //             'title' => 'New Notification',
        //             'body' => $this->notification,
        //         ],
        //     ],
        // ]);

        return $response;
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
