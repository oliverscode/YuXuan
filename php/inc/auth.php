<?php
function authLogin()
{
    session_start();
    $_SESSION['auth'] = true;

}

function authLogout()
{
    session_start();
    session_destroy();

}

function authCheck()
{
    // 判断当前页面是否是登录页面
    if (strpos($_SERVER['SCRIPT_NAME'], 'auth.php') !== false) {
        return;
    }

    // 如果是开发环境，直接通过, 也就是url中有localhost
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        return;
    }


    session_start();
    if ($_SESSION['auth'] !== true) {
        header('Location: auth.php');
        die;
    }
}

authCheck();
