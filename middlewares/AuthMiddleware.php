<?php

namespace ojpro\phpmvc\middlewares;

use ojpro\phpmvc\Application;
use ojpro\phpmvc\Exception\ForbiddenException;

class AuthMiddleware extends Middleware
{
    public array $actions ;
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }
    public function execute()
    {
        if (!Application::isLogin() && in_array(Application::$app->controller->action, $this->actions)) {
            throw new ForbiddenException();
        }
    }
}