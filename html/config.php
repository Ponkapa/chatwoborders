<?php session_start(); ?>
<?php if (!$_SESSION['loggedin']) header("Location: login.php"); ?>
<?php
    require_once('../mysql.php');
  $error = "";

  if($_SERVER["REQUEST_METHOD"] == "POST") {
        $newnickname = mysqli_real_escape_string($dbc,$_POST['nickname']);
        if (!empty($newnickname)){
          if (isset($_POST['translate']))
          {
            $transmode = 1; 
           }
          else
          {
            $transmode = 0;
          }
          $sourcelang = $_POST['sourcelang'];
          $stmt = $dbc->prepare("Update Users set nickname = (?), sourcelang = (?), transmode = (?) where UID = (?);");
          $stmt->bind_param("ssii", $newnickname, $sourcelang, $transmode, $_SESSION['user']);
          $stmt->execute();
          $stmt->close();
          $_SESSION['send_translate'] = $transmode;
          $_SESSION['source_lang'] = $sourcelang;
          header( 'Location: /index.php' );
      }
      else
      {
        $error = "Please enter a nickname";
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
    padding: 2px;
    color: #dad9db;
    text-align: center;
}
h2{
  font: 1.5em "Lucida Grande", Sans-Serif;
  padding: 0px;
  color: #dad9db;
  text-align: center;
  font-weight: bold;
}
/* The switch - the box around the slider */
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 34px;
  margin: 5px;
}

/* Hide default HTML checkbox */
.switch input {display:none;}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .2s;
  transition: .2s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 26px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .2s;
  transition: .2s;
}

input:checked + .slider {
  background-color: #69416D;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
<body onload="selectLanguage(); selectTranslate();">

<h1> Chat w/o Borders </h1>
<h2> Settings </h2>
<div style="width:350px; margin: auto; border: 3px solid #8b8a8c">
  <form action="" method = "post">
    <label for="nickname">Nickname</label>
    <input type = "text" placeholder="Enter nickname..." name = "nickname" class = "textbox" value = <?php $query = "select nickname from Users where UID = " . $_SESSION['user'];
                  $response = @mysqli_query($dbc, $query); if ($response){
                    if ($response)
                    {
                    $row = mysqli_fetch_row($response);
                    if ($row)
                    {
                        $nickname = $row[0];
                        echo $nickname;
                    }
                }
                    } ?> />
    <label for="server">Change Native Language</label>
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
    <label for= "translate">Translate Mode (On means translated text is sent)</label>
    <label class="switch">
      <input type="checkbox" name = "translate" id="translate">
      <span class="slider round" value="on"></span>
    </label>
    <input class="button" type = "submit" value = "Save Changes"/>
  </form>
  <button class="button" style="margin-top: 10px" onclick="javascript:location.href='/login.php'">Cancel</button>
  <div style = "font-size:11px; color:#EEEDEA; margin-top:10px"><?php echo $error; ?></div>
</div>

</body>
</html>

<script type="text/javascript">
  var info = { 
    user: <?php echo $_SESSION['user']; ?>,
    curr_serv: <?php echo $_SESSION['serv']; ?>,
    source_lang: <?php echo json_encode($_SESSION['source_lang']); ?>,
    server_lang: <?php echo json_encode($_SESSION['server_lang']); ?>,
    send_translate: <?php echo json_encode($_SESSION['send_translate']); ?>
  }
  function selectLanguage(){
    document.getElementById('server').value = info.source_lang;
  }
  function selectTranslate(){
    if(info.send_translate == 1)
    {
      document.getElementById('translate').checked = true;
    }
    else
    {  
      document.getElementById('translate').checked = false;

    }
  }
</script>
