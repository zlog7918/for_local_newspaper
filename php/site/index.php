<?php
    // phpinfo();
    require_once 'classes.php';
    require_once 'funcs.php';
    session_start();
    // unset($_SESSION['user']);
    if(!isset($_SESSION['user']))
        $_SESSION['user']=new \Users\UnloggedUser();
    require 'connect.php';
    if(!$db->is_connection()) {
        if(isset($_GET['do'])) {
            echo json_encode(['error'=>true, 'message'=>'No datebase connection. Please try again later.']);
        } else {
            require_once 'err404.php';
        }
        exit(0);
    }

    if(isset($_GET['do'])) {
        if(method_exists($_SESSION['user'], $_GET['do'])) {
            $ret=$_SESSION['user']->{$_GET['do']}();
            if($ret instanceof \Users\UsrBase) {
                $_SESSION['user']=$ret;
                if($ret instanceof \Users\UnloggedUser && $ret->is_err()===true) {
                    echo json_encode(['error'=>true, 'message'=>$ret->get_err()]);
                    exit();
                }
                echo json_encode(['error'=>false]);
            } else
                echo $ret;
        } else
            echo json_encode(['error'=>true, 'message'=>'Unknown action']);
    } else {
        if(isset($_GET['get'])) {
            $filename="get/{$_GET['get']}.php";
            if(!(strpos($_GET['get'], '/')===false && file_exists($filename))) {
                require_once 'err404.php';
                exit();
            }
        } else
            $filename=($_SESSION['user']->is_logged() ? 'get/logged.php':'get/log_in.php');
        require_once 'inner_index.php';
    }
