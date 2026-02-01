<?php 

namespace App\Support;

class FlashMessage
{
    public static function set(string $type, string $message): void
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    public static function get(): ?array
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $flashMessage = $_SESSION['flash_message'] ?? null;
        unset($_SESSION['flash_message']);

        return $flashMessage;
    }
}
