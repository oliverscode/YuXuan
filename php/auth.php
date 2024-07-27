<?php
global $req, $auth, $log, $config;
require_once 'inc/app.php';

$action = $req->string('action');
if ($action === 'login') {
    $pwd = $req->string('pwd');
    if ($pwd ===  $config['PWD'] ) {
        $auth->login(true);
        $log->info('登录成功');
        echo 'ok';
    } else {
        echo '密码错误';
        $log->info("密码错误, 错误密码为: $pwd");
    }
    die;
} else if ($action == 'logout') {
    $auth->clear();
    die('ok');
}

?>
<!doctype html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta charset="UTF-8">
    <link rel="icon" href="favicon.png" type="image/png">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/semantic-ui/2.5.0/semantic.min.js"></script>
    <link href="https://cdn.bootcdn.net/ajax/libs/semantic-ui/2.5.0/semantic.min.css" rel="stylesheet">

    <script src="src/layer/layer.js"></script>
    <title>登录</title>
</head>
<body>


<div class="ui container" style="width: 300px;margin-top: 200px">
    <form class="ui form" id="formLogin">
        <div class="ui header">Yuxuan Login</div>
        <div class="field">
            <div class="ui input left icon action">
                <i class="icon lock"></i>
                <input type="password" id="lblPwd">
                <button type="submit" class="ui green button submit">Enter</button>
            </div>
        </div>
    </form>
</div>


<script>
    document.getElementById("formLogin").onsubmit = function (e) {

        e.preventDefault();
        let pwd = document.getElementById("lblPwd").value;
        if (pwd.length === 0) {
            document.getElementById("lblPwd").focus()
            return false;
        }
        fetch('?action=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'pwd=' + pwd
        }).then(function (response) {
            return response.text();
        }).then(function (data) {
            if (data === 'ok') {
                window.location.href = 'index.php';
            } else {
                if (data?.length > 0) {
                    layer.alert(data, {
                        title: 'Error',
                        icon: 2,
                        shadeClose: true
                    });
                }
                document.getElementById("lblPwd").value = '';
            }
        }).catch(function (e) {
            // 错误弹窗
            layer.alert(e, {
                title: 'Error',
                icon: 2,
                shadeClose: true
            })
        });
        return false;
    }
</script>
</body>
</html>
