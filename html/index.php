<?php session_start(); ?>
<?php if (!$_SESSION['loggedin']) header("Location: login.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Chat w/o Borders</title>
<link rel="stylesheet" type="text/css" href="flex.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="chat.php"></script>
<script>
var serverselectShow = false;
var serverselectTimeout;
var timeout_translate;
var timeout_logout;

var chat = new Chat();
function toggleElement(){
	if (serverselectShow == false){
		showElement();
	}
	else{
		hideElement();
	}
}
function showElement(){
	$("#server-select").animate({'min-width': '170px'}, "fast");
	serverselectShow = true;
}
function hideElement(){
	$("#server-select").animate({'min-width': '0px', 'width': '0px'}, "fast");
	serverselectShow = false;
}
function sendMessage(){
	var text = $("#sendie").val();
    $("#sendie").val("");
       if (timeout_translate){
        clearTimeout(timeout_translate);
        timeout_translate = null;
       }
       if (text != ""){
 	       chat.send(text);
       }
}

$(document).ready(function(){
	document.getElementById("sendie").placeholder = "Message in " + <?php echo json_encode($_SESSION['server_name']); ?>;
	$("#server-select").mouseleave(function(){
		serverselectTimeout = setTimeout('hideElement();', 300);
	});
	$("#server-select").mouseenter(function(){
		clearTimeout(serverselectTimeout);
		serverselectTimeout = null;
	});
	 $(function() {


		 // watch textarea for key presses
         $("#sendie").keydown(function(event) {  
         
             var key = event.which;
             //all keys including return.  
             if (key >= 33) {
               
                 var maxLength = $(this).attr("maxlength");  
                 var length = this.value.length;  
                 // don't allow new content if length is maxed out
                 if (length >= maxLength) {  
                     event.preventDefault();  
                 }

              }
               if (key == 13) {
                event.preventDefault();
               sendMessage();
              }    
        });
		 // watch textarea for release of key press
		 $('#sendie').keyup(function(e) {
            if (timeout_translate){
                clearTimeout(timeout_translate);
                timeout_translate = null;
            }
            timeout_translate = setTimeout(chat.translate, 200, $(this).val());
		     
            
          });
        
	});
});
</script>
</head>
<body onload="chat.load();setInterval('chat.update();', 1000);">
<div id="main-wrapper" class="container-vertical">
	<div id="header" class="container-horizontal">
		<button onclick='toggleElement()' class="button"> Servers </button>
		<h1 class="item"> Chat w/o Borders </h1>
		<h2> Current Server: </h2>
		<p id="current-server"> <?php echo $_SESSION['server_name']; ?> </p>
		<button class="button" onclick="javascript:location.href='/config.php'">  Settings  </button>
        <button class="button" onclick="chat.logout()">Logout</button>
	</div>
	<div id="app" class="container-horizontal item">
		<div id="server-select" class="scrollbar">
		</div>
		<div id="chat" class="container-vertical">
			<div class="scrollbar item" id="chat-area">
			</div>
			<div id="translate-area"> 
				<div id="translate-note"> Translation: </div><div id="translated"></div>
			</div>
			<div id="send-message-area" class="container-horizontal">
	            <div id="spacing"></div>
	            <textarea id="sendie" maxlength = '100' class="item"></textarea>
	            <button id="sender" onclick="sendMessage();" class="item button"> Send Message </button>
	        </div>
		</div>
	</div>
</div>
</body>
</html>
