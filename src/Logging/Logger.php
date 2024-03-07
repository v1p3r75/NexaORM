<?php

namespace Nexa\Logging;

class Logger 
{

    public static function info(string $message)
    {
        echo "[*] - $message\n";
        return true;
    }
    
}