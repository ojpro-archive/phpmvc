<?php

namespace ojpro\phpmvc;

class View
{
    public string $title = '';

    public function renderView(string $view, $params = [])
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();
        return str_replace('{{ content }}', $viewContent, $layoutContent);
    }

    public function layoutContent()
    {
        $layout = Application::$app->layout;
        if (Application::$app->controller) {
            $layout = Application::$app->controller->getLayout();
        }

        ob_start();
        include Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();
    }
    public function renderOnlyView($view, $params)
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }

        ob_start();
        require_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }
}