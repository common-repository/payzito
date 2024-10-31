<?php

namespace payzitoRepository\libraries;

use payzitoRepository\controllers;

defined('PAR_EXEC') OR die('Payzito Repository Restricted Access');

class controller
{
    function load($class,$method='index')
    {
        $file = helper::getCorePath().DS.'controllers'.DS.$class.'.php';
        if(!file_exists($file))
        {
            return false;
        }

        require_once $file;

        $classname = '\payzitoRepository\controllers\\'.$class;
        if(!class_exists($classname))
        {
            return false;
        }

        $controller = new $classname;
        if(!method_exists($controller,$method))
        {
            return false;
        }

        return $controller->$method();
    }

    function loadView($directory,$passParams=[],$return=false)
    {
        $file = helper::getCorePath().DS.'views'.DS.trim(str_replace('/',DS,$directory),DS).'.php';

        if(file_exists($file))
        {
            if(!empty($passParams))
            {
                extract($passParams);
            }

            if($return)
            {
                ob_start();
                include $file;
                $output = ob_get_contents();
                ob_end_clean();

                return $output;
            }
            else
            {
                include $file;
            }
        }

        return '';
    }
}