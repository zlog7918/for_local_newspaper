<?php
    function validate_pass(string $pass) :bool
    {
        $len=strlen($pass);
        if($len>5 && $len<=50)
            return true;
        return false;
    }
    function file_and_last_edit(string $filepath) :string
    {
        if(strpos($filepath, '/')!==0)
            $filepath="/$filepath";
        // '/style.css?t='.date('YmdHi', filemtime('style.css'))
        $fullpath="{$_SERVER['DOCUMENT_ROOT']}$filepath";
        $file_edit_date=file_exists($fullpath) ? date('YmdHi', filemtime($fullpath)):'ERROR';
        return "$filepath?t=$file_edit_date";
    }
    function str_var_dump(mixed $var) :string
    {
        ob_start();
        var_dump($var);
        return ob_get_clean();
    }
