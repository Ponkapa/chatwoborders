<?php
//from: https://www.tutorialspoint.com/php/php_mysql_login.htm
   session_start();
   if (isset($_SESSION['loggedin'])) header("Location: index.php");
   if (!isset($_SESSION['send_translate'])) $_SESSION['send_translate'] = 1; 
   require('../mysql.php');
   $error = "";
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      $myusername = mysqli_real_escape_string($dbc,$_POST['username']);
      $mypassword = mysqli_real_escape_string($dbc,$_POST['password']);
      $stmt = $dbc->prepare("SELECT u.UID, u.SID, u.sourcelang, s.langcode, u.passhash, s.langname, u.transmode FROM Users u, Servers s where s.SID = u.SID and u.username = ?");
      $stmt->bind_param('s', $myusername);
      $stmt->execute();
      $stmt->bind_result($user, $serv, $source_lang, $server_lang, $passhash, $server_name, $send_translate);
      if ($stmt->fetch())
      {
        if(password_verify($mypassword, $passhash))
        {
            $mypassword = null;
            $passhash = null;
            $_SESSION['login_user'] = $myusername;
            $_SESSION['user'] = $user;
            $_SESSION['serv'] = $serv;
            $_SESSION['source_lang'] = $source_lang;
            $_SESSION['server_lang'] = $server_lang;
            $_SESSION['server_name'] = $server_name;
            $_SESSION['send_translate'] = $send_translate;
            $_SESSION['loggedin'] = true;
            header("Location: index.php");
        }
        else
        {
            $error = "Your Login Name or Password is invalid";
        }
      }
      else
      {
        $error = "Your Login Name or Password is invalid";
      }
   }
?>
<!DOCTYPE html>
<html>
<head>
<title>Chat w/o Borders</title>
</head>
<style>
body{
    background-color: #2e2d2f;
}
input[type=text], select {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    color: #EEEDEA;
}
label{
    color: #EEEDEA;
}
input[type=password], select {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    color: #EEEDEA;
}
.textbox{
    background-color: #595560; 
    color: #EEEDEA;
}
div {
    border-radius: 5px;
    background-color: #47434F;
    padding: 15px;
}
.button{
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    border-radius: 12px;
    background-color: #2A213D;
    color: #C9C4BC;
    border: 3px solid #47434F;
    margin: 0px;
    height: 40px;
    font-size: 14px;
    font-weight: bold;
    -webkit-transition-duration: 0.2s; /* Safari */
    transition-duration: 0.2s;
}
.button:hover {
     background-color: #342C45; 
     color: #EEEDEA;
}
.button:active{
    background-color: #27252B;
}
h1{
    font: 2em impact; 
    padding: 10px;
    color: #dad9db;
    text-align: center;
}
</style>
<body>

<h1> Chat w/o Borders </h1>
<div style="width:350px; margin: auto; border: 3px solid #8b8a8c">
               
  <form action="" method = "post">
    <label for="username">Username</label>
    <input class="textbox" type="text" name="username" placeholder="Enter your username...">

    <label for="password">Password</label>
    <input class="textbox" type="password" id="password" name="password" placeholder="Enter your password...">
  
    <input class="button" type = "submit" value = "Login"/>
  </form>
  <button class="button" style="margin-top: 10px" onclick="javascript:location.href='/signup.php'">Signup</button>
  <div style = "font-size:11px; color:#EEEDEA; margin-top:10px"><?php echo $error; ?></div>
</div>

</body>
</html>
