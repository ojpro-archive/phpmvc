<?php

namespace ojpro\phpmvc\Exception;

use Exception;

class ForbiddenException extends Exception
{
    public $code = 403;
    public $message = "You don't have permissions to access this route";
}