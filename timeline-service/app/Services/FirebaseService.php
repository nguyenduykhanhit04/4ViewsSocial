<?php

namespace App\Services;

use GuzzleHttp\Client;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    // Sửa ID từ fir-d7e71 thành viewsocial-f038a
    const FCM_URL = 'https://fcm.googleapis.com/v1/projects/viewsocial-f038a/messages:send';
    private $client;
    private $firebaseToken;

    public function __construct()
    {
        // Init Guzzle client
        $this->client = new Client();
        // Render accesstoken
        $scope = [
            'https://www.googleapis.com/auth/firebase.messaging'
        ];
        $pathToServiceAccount = storage_path('firebase_credentials.json');
        $credentials = new ServiceAccountCredentials($scope, $pathToServiceAccount);
        $credentials->fetchAuthToken();
        $this->firebaseToken = $credentials->getLastReceivedToken()["access_token"];
    }

    /**
     * Function  service send nofitication to device
     *
     * @param string $content message
     * @param string $deviceToken of device
     * @return void | mixed
     */
    public function sendFCM($content, $deviceToken)
    {
        try {
            $this->client->request('POST',self::FCM_URL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->firebaseToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'message' => [
                        'token' => $deviceToken,
                        'notification' => [
                            'title' => '4Views Social notification',
                            'body' => $content,
                        ],
                    ],
                ],
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }
}