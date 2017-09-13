<?php
session_start();
if (isset($_SESSION['loggedin'])) header("Location: index.php");
    require_once('../mysql.php');
  $error = "";

if($_SERVER["REQUEST_METHOD"] == "POST")
{
  $myusername = mysqli_real_escape_string($dbc,$_POST['username']);
  $mypassword = mysqli_real_escape_string($dbc,$_POST['password']);
  $mypassword2 = mysqli_real_escape_string($dbc,$_POST['password2']);
  $sourcelang = $_POST['sourcelang'];
  if (empty($myusername))
  {
    $error = "Please enter a username";
  }
  else
  {
    if (empty($mypassword))
    {
      $error = "Please enter a password";
    }
    else
    {
      if (empty($mypassword2) || $mypassword != $mypassword2)
      {
        $error = "Passwords do not match.";
      }
      else
      {
      $stmt = $dbc->prepare("select COUNT(*) from Users where username = ?");
      $stmt->bind_param('s', $myusername);
      $stmt->execute();
      $stmt->bind_result($users);
      $stmt->fetch();
      $stmt->close();
        if ($users != 0)
        {
          $error = "Username already in system, please choose another.";
        }
        else
        {
          $stmt = $dbc->prepare("select SID from Servers where langcode = ?");
          $stmt->bind_param('s', $sourcelang);
          $stmt->execute();
          $stmt->bind_result($defaultserver);
          $stmt->fetch();
          $stmt->close();
          $stmt = $dbc->prepare("insert into Users (username, sourcelang, passhash, SID, nickname, transmode) values(?, ?, ?, ?, ?, ?)");
          $stmt->bind_param("sssisi", $myusername, $sourcelang, password_hash($mypassword, PASSWORD_DEFAULT), $defaultserver, $myusername, $transmode);
          $stmt->execute();
          $stmt->close();
          header( 'Location: /login.php' );
        }
      }
    }
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
    <label for="password">Retype Password</label>
    <input class="textbox" type="password" id="password2" name="password2" placeholder="Re-enter your password...">
    <label for="server">Native Language</label>
    <select class="textbox" id="server" name = "sourcelang">
        <?php
          $query = "select langcode, langname from Servers";
          $response = @mysqli_query($dbc, $query);
          if ($response)
          {                
              while ($row = mysqli_fetch_array($response))
              {
                  echo "<option value=". $row['langcode'] . ">" . $row['langname'] . "</option>";
              }
          }
    ?>
    </select>
    <input class="button" type = "submit" value = "Register"/>
  </form>
  <button class="button" style="margin-top: 10px" onclick="javascript:location.href='/login.php'">Cancel</button>
  <div style = "font-size:11px; color:#EEEDEA; margin-top:10px"><?php echo $error; ?></div>
</div>

</body>
</html>
