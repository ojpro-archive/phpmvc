<?php

namespace ojpro\phpmvc;

use app\controller\Controller;
use ojpro\phpmvc\database\Database;
use ojpro\phpmvc\database\ORM;

class Application
{
    public string $userClass;
    public Router $router;
    public static string $ROOT_DIR;
    public Request $request;
    public Response $response;
    public static Application $app;
    public Database $db;
    public Session $session;
    public ?ORM $user;
    public Controller $controller;
    public View $view;
    public string $layout = 'main';
    public function __construct(string $rootPath, array $config)
    {
        $this->userClass = $config['user_class'];
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->view = new View();
        $this->controller = new Controller();
        $this->request = new Request();
        $this->session = new Session();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);


        $this->db = new Database($config['db']);


        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }
    }

    public function run()
    {
        try {
            echo  $this->router->resolve();
        } catch (\Exception $ex) {
            $this->response->setStatusCode($ex->getCode());

            echo  $this->view->renderView('_error', ['exception'=>$ex]);
        }
    }

    /**
     * Get the value of controller
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set the value of controller
     *
     * @return  self
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    public function login(ORM $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }
    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isLogin()
    {
        return self::$app->user ?? false;
    }
}