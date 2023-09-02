<?php

namespace App\Exceptions;

use Exception;

class BookNotAvailableException extends Exception
{
    // exception msg
    protected $message = 'Book is out of stock';

    // exception code
    protected $code = 400;

    // exception type
    protected $type = 'BookNotAvailableException';
}
