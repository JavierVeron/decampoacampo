<?php

namespace App\Classes;

class Logger {
    private static $file = __DIR__ . '../../logs/app.log';

    static private function log($message, $type)
    {
        $message = date("Y-m-d H:i:s") ."|" .$type ."|" .$message ."\n";
        file_put_contents(self::$file, $message, FILE_APPEND);
    }

    static public function info($message)
    {
        self::log($message, "INFO");
    }

    static public function error($message)
    {
        self::log($message, "ERROR");
    }
}