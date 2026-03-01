<?php

namespace App\Helpers;

use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseNotificationHelper
{
    protected $messaging;

    // Inject Firebase Messaging service
    public function __construct()
    {
        // $this->messaging = $messaging;
        $this->messaging = app('firebase')->createMessaging();
    }

    /**
     * Send a notification to a single device
     *
     * @param string $deviceToken
     * @param string $title
     * @param string $body
     * @param array $data
     * @return array
     */
    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        try {
            // Create notification object
            $notification = Notification::create($title, $body);

            // Create cloud message object
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification)
                ->withData($data); // Optional: Include additional data

            // Send the message
            $this->messaging->send($message);

            return [
                'success' => true,
                'message' => 'Notification sent successfully.'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send a notification to multiple devices
     *
     * @param array $deviceTokens
     * @param string $title
     * @param string $body
     * @param array $data
     * @return array
     */
    public function sendNotificationToMultipleDevices($deviceTokens, $title, $body, $data = [])
    {
        try {
            // Create notification object
            $notification = Notification::create($title, $body);

            // Create cloud message object
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data); // Optional: Include additional data

            // Send the multicast message
            $response = $this->messaging->sendMulticast($message, $deviceTokens);

            return [
                'success' => true,
                'message' => "{$response->successCount()} notifications sent successfully."
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send notifications: ' . $e->getMessage()
            ];
        }
    }
}
