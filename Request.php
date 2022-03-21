<?php

namespace ojpro\phpmvc;

class Request
{
    public function getPath()
    {
        // get request uri
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        // get position of ? in the uri
        $position = strpos($path, '?');
        // return path if there is no ? mark
        if (!$position) {
            return $path;
        }
        // get path without query
        $path = substr($path, 0, $position);
        return $path;
    }
    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function isGet()
    {
        return $this->method() == 'get';
    }
    public function isPost()
    {
        return $this->method() == 'post';
    }
    public function getBody()
    {
        $body = [];
        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }
}