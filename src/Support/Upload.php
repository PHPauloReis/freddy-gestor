<?php

namespace App\Support;

class Upload
{
    public static function handle($file): ?string
    {
        if ($file && $file->getError() === UPLOAD_ERR_OK) {
            $filename = sprintf('%s_%s', time(), $file->getClientFilename());
            $file->moveTo(__DIR__ . '/../../public/uploads/' . $filename);
            return '/uploads/' . $filename;
        }
        
        return null;
    }
}