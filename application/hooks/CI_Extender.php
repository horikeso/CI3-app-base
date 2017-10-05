<?php

class CI_Extender {

    public function load_class()
    {
        spl_autoload_register(function ($class) {
            $file_path = APPPATH . 'ci_extender/' . $class . '.php';
            if (file_exists($file_path) === true)
            {
                include $file_path;
            }
        });
    }
}