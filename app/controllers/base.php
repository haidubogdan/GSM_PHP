<?php
namespace controller;

class Base {

    public $data;

    function render($view, $data = array()) {

        $template = file_get_contents($view);

        foreach ($data as $key => $value) {
            $template = str_replace("{{" . $key . "}}", $value, $template);
        }
        echo $template;
    }

}
