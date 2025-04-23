<?php

namespace App\Exception;

use RuntimeException;

class CMFBanksApiException extends RuntimeException
{
    public function __construct(
        string     $message = "Error al obtener los datos de la API",
        int        $code = 0,
        \Throwable $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }
}
