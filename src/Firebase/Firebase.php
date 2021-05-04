<?php
declare(strict_types = 1);

namespace Apex\Mercury\Firebase;

use Apex\Mercury\Firebase\FirebaseConfig;


/**
 * Frebase class
 */
class Firebase
{

    /**
     * Constructor
     */
    public function __construct(
        private FirebaseConfig $config
    ) { 

    }

    /**
     * Send Firebase message
     */
    public function send(
        string $message, 
        array $android_ids = [], 
        array $ios_ids = [], 
        string $type = '', 
        string $title = '', 
        string $icon = '', 
        string $action_id = ''
    ):int { 

        // Get action id
        if ($action_id == '') { 
            $action_id = (string) rand(1000000, 9999999);
        }
        $total = 0;

        // Send to Android
        if (count($android_ids) > 0) { 

            // Set request
            $request = [
                'registration_ids' => $android_ids, 
                'data' => [
                    'my_data' => [
                        'type' => $type, 
                        'message' => $message, 
                        'icon' => $icon, 
                        'id' => $action_id
                    ]
                ]
            ];

            // Send request
            $count = $this->sendHttpRequest(json_encode($request));
            $total += $count;
        }

        // Send to iOS devices
        if (count($ios_ids) > 0) { 

            // Set request
            $request = [
                'registration_ids' => $ios_ids, 
                'notification' => [
                    'title' => $title, 
                    'text' => $message, 
                    'sound' => 'default', 
                    'badge' => '0', 
                    'type' => $type, 
                    'id' => $action_id, 
                    'image_icon' => $icon, 
                    'name' => $this->config->getAppName()
                ], 
                'priority' => 'high'
            ];

            // Send request
            $count = $this->sendHttpRequest(json_encode($request));
            $total += $count;
        }

        // Return
        return $total;
    }

    /**
     * Send http request
     */
    public function sendHttpRequest(string $request):?int
    {

        // Set headers
        $headers = [
        'Content-type: application/json', 
            'project_id: ' . $this->config->getProjectId(), 
            'Authorization: key=' . $this->config->getServerKey()
        ];

        // Send message via curl
        $ch = curl_init($this->config->getFirebaseUrl());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        $response = curl_exec($ch);
        curl_close($ch);

        // Check response
        if (!$response) { 
            return null;
        } elseif (!$vars = @json_decode($response, true)) { 
            return null;
        }

        // Return
    $total = $vars['success'] ?? 0;
        return (int) $total;
    }

}



