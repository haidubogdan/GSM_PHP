<?php
namespace controller;

class Controller {
    function __construct() {
        $pages = require "config/routes.php";
        $page = "home";
        
        if (isset($_GET["page"])) {
            $page = $_GET["page"];
        }
        
        //$controllerPath = $pages[$page]["path"];
        $controller = "controller\\".$pages[$page]["class"];

        new $controller();
        
    } 
}

