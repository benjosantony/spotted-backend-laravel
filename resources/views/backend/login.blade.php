<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Spotted Puzzle | Admin Portal | Log in</title>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link href="{{ asset("/AdminLTE-2.2.0/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/AdminLTE-2.2.0/dist/css/AdminLTE.min.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/AdminLTE-2.2.0/plugins/iCheck/square/blue.css") }}" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Admin </b>Spotted Puzzle
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>
        <form id="login-form">
            <div class="form-group has-feedback">
                <input id="login-username" type="text" class="form-control" placeholder="Username" />
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input id="login-password" type="password" class="form-control" placeholder="Password" />
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row" style="text-align: center">
                <button class="btn btn-primary btn-flat" style="width: 100px" onclick="LoginToAdminPortal(); return false;">Sign In</button>
            </div>
        </form>
    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="{{ asset("/AdminLTE-2.2.0/plugins/jQuery/jQuery-2.1.4.min.js") }}" type="text/javascript"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset("/AdminLTE-2.2.0/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<!-- iCheck -->
<script src="{{ asset("/AdminLTE-2.2.0/plugins/iCheck/icheck.min.js") }}" type="text/javascript"></script>
<script type="text/javascript">
    var CSRF_TOKEN = $('meta[name="csrf-token"]').prop('content');
    var adminURL = "{{ env("ADMIN_URL") }}";
    function LoginToAdminPortal(){
        $.ajax({
            method: "POST",
            url: adminURL + "login",
            dataType: "json",
            data: {
                username: $("#login-username").val(),
                password: $("#login-password").val(),
                _token: CSRF_TOKEN
            }
        }).done(function (rs) {
            if(rs.error){
                alert("Login Failed !");
            } else if(!rs.error) {
                window.location.href = adminURL.substr(0, adminURL.length - 1);
            } else {
                alert("Failed to login !");
            }
        }).fail(function () {
            alert("Failed to login !");
        }).always(function () {
            $("#login-username").val("");
            $("#login-password").val("");
        });
    }
</script>
</body>
</html>