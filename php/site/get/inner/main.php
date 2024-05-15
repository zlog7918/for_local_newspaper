<?php
    if($_SESSION['user']->is_logged())
        echo '<h1>Hello!</h1>';
    else {
        if(isset($_GET['do'])) {
            if($_GET['do']=='log_in')
                require 'inner/log_in.php';
            else {
                echo 'doing sth different';
            }
        } else
            echo '<h1>Hello, unlogged!</h1>';
    }
