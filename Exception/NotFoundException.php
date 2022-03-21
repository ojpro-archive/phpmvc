<?php

namespace ojpro\phpmvc\Exception;

use Exception;

class NotFoundException extends Exception
{
    public $code = 404;
    public $message = "Page Not Found.";
}