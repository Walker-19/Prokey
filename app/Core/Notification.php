<?php

namespace App\Core;

class Notification
{
    public static function add($type, $message)
    {
        if (!isset($_SESSION['notifications'])) {
            $_SESSION['notifications'] = [];
        }

        $_SESSION['notifications'][] = [
            'type' => $type,
            'message' => $message
        ];
    }

    public static function get()
    {
        if (!isset($_SESSION['notifications'])) {
            return [];
        }

        $notifications = $_SESSION['notifications'];
        unset($_SESSION['notifications']);
        return $notifications;
    }
}