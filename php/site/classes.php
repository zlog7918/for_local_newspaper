<?php
    spl_autoload_register(function ($class) {
        $class=str_replace('\\', '/', $class);
        $filename="classes/{$class}.class.php";
        if(file_exists($filename))
            require $filename;
    });
