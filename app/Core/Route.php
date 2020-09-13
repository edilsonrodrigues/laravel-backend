<?php

namespace App\Core\Library;

use \Illuminate\Support\Facades\Route as R;
use Illuminate\Support\Facades\App as App;

/**
 * Class Route
 * @package App\Core\Library
 * @autor Flavio
 * @autor Edilson Rodrigues
 */
class Route
{

    protected $_route = null;
    protected $_method = null;
    protected $_controller = null;
    protected $_action = null;
    protected $_params = [];
    protected $_language = null;

    protected $_controllerFunction = null;
    protected $_middleware = [];

    protected function setDefaultAction()
    {
        if ($this->getMethod() == 'get') {
            $this->_action = 'list';
        } else if ($this->getMethod() == 'post') {
            $this->_action = 'create';
        } else if ($this->getMethod() == 'put') {
            $this->_action = 'update';
        } else if ($this->getMethod() == 'delete') {
            $this->_action = 'remove';
        }
    }

    public function init()
    {
        $this->_method = strtolower(request()->method());
        $this->_locale = current(explode('/', request()->path()));

        $this->_route = str_replace(
            ($this->getLocale('') ? $this->getLocale('') . '/' : ''),
            '',
            request()->path()
        );

        $path = explode('/', $this->_route);

        while (count($path) > 0 && is_null($this->_controller)) {
            $controllerName = 'App\Http\Controllers';
            $route = '';
            foreach ($path as $p) {
                $controllerName .= "\\" . ucfirst($this->camelCase($p));
                $route .= "/{$p}";
            }
            $controllerName .= "Controller";
          
            if (class_exists("{$controllerName}")) {
                $this->_route = $route;
                $this->_controller = $controllerName;


                if (count($this->_params) == 0) {
                    $this->setDefaultAction();
                } else if (count($this->_params) > 0) {
                    $this->_action = current($this->_params);

                    if (!method_exists($this->_controller, $this->getAction())) {
                        $this->setDefaultAction();
                    } else {
                        array_shift($this->_params);
                        $this->_route .= "/{$this->_action}";
                    }
                }
            } else {
                array_unshift($this->_params, end($path));
                array_pop($path);
            }
        }
        return $this;
    }

    protected function getLocale($default)
    {
        if (in_array($this->_locale, array('en', 'es', 'pt-br'))) {
            $default = $this->_locale;
        }
        return $default;
    }

    protected function getEndpoint()
    {
        $endPoint = $this->_route;

        foreach ($this->_controllerFunction->getParameters() as $p) {
            try {
                $p->getDefaultValue();
                $endPoint .= "/{{$p->getName()}?}";
            } catch (\Exception $e) {
                $endPoint .= "/{{$p->getName()}}";
            }
        }

        return $endPoint;
    }

    protected function getMethod()
    {
        return $this->_method;
    }

    protected function getController()
    {
        return $this->_controller;
    }

    protected function getAction()
    {
        return strtolower($this->_method) . ucfirst($this->camelCase($this->_action));
    }

    protected function getParams()
    {
        return $this->_params;
    }

    protected function getMiddleware()
    {
        return $this->_middleware;
    }

    protected function readSettings()
    {
        $comments = $this->_controllerFunction->getDocComment();
        if ($comments) {
            $comments = explode("\n", $comments);
            foreach ($comments as $comment) {
                $index = '@middleware';
                if (strpos(" {$comment}", $index)) {
                    $comment = trim(substr($comment, strpos($comment, $index) + strlen($index)));
                    if (!empty($comment))
                        $this->_middleware[] = explode(",", $comment)[0];
                }
            }
        }
        return $this->_middleware;
    }

    protected function validateController()
    {
        if (is_null($this->getController()))
            throw new \Exception("O recurso que esta tentando acessar nao existe.");
    }

    protected function validateAction()
    {
        if (!method_exists($this->getController(), $this->getAction()))
            throw new \Exception("O endpoint {$this->getAction()} nao existe neste recurso.");
    }

    protected function validateParameters()
    {
        $this->_controllerFunction = new \ReflectionMethod($this->getController(), $this->getAction());
        $parametros = count($this->_controllerFunction->getParameters());
        foreach ($this->_controllerFunction->getParameters() as $p) {
            try {
                $p->getDefaultValue();
                $parametros--;
            } catch (\Exception $e) {
            }
        }

        if (count($this->_params) < $parametros)
            throw new \Exception("A quantidade de parametros passada esta incorreta.");
    }

    protected function validate()
    {

        try {
            $this->validateController();
            $this->validateAction();
            $this->validateParameters();
            $this->readSettings();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function createRoute()
    {
        try {
            $this->validate();

            $controller = str_replace('App\Http\Controllers\\', '', $this->getController());
            R::{$this->getMethod()}(
                "{$this->getEndpoint()}",
                "{$controller}@{$this->getAction()}"
            )
                ->prefix($this->getLocale(''))
                ->middleware($this->getMiddleware())
                ->name('router');

            App::setLocale(strtolower($this->getLocale('pt-br')));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function camelCase($str, $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }
}
