<?php header("Content-type: application/javascript"); session_start(); ?>
var info = { 
	user: <?php echo $_SESSION['user']; ?>,
	curr_serv: <?php echo $_SESSION['serv']; ?>,
	source_lang: <?php echo json_encode($_SESSION['source_lang']); ?>,
	server_lang: <?php echo json_encode($_SESSION['server_lang']); ?>,
	server_name: <?php echo json_encode($_SESSION['server_name']); ?>,
	send_translate: <?php echo json_encode($_SESSION['send_translate']); ?>
};
var state = 0;
var sending = false;
var updating;
function Chat () {
    this.update = updateChat;
    this.send = sendChat;
	this.catchUp = catchUp;
	this.translate = translate;
	this.changeServer = changeServer;
	this.logout = logout;
	this.load = load;
}

function load(){
	catchUp();
	$.ajax({
		type: "POST",
		url: "process.php",
		data: {
			'function': 'load',
			'source': info.source_lang
		},
		dataType: "json",
		success: function(data){			
			if (data.servers)
			{
				for (var i = 0; i < data.servers.length; i++) {
                    $('#server-select').append(data.servers[i]);
                }
                if (data.source_name)
                {
                	info.source_name = data.source_name;
                }
			}
		}
	})
}

function logout(){
	$.ajax({
		type: "POST",
		url: "logout.php",
		success: function(){
			window.location.replace("/login.php");
		}
	})
}

function changeServer(server_code){
	$.ajax({
		type: "POST",
		url: "process.php",
		data: {
			'function': 'changeServer',
			'langcode': server_code,
			'userid': info.user
		},
		dataType: "json",

		success: function(data){
			$('#chat-area').text("");
			info.server_lang = server_code;
			info.curr_serv = data.sid;
			info.server_name = data.name;
			$('#current-server').text(data.name);
			document.getElementById("sendie").placeholder = "Message in " + info.server_name;
			catchUp();
		}
	})
}


function translateToServer(message){
	$.ajax({
		type: "POST",
		url: "process.php",
		data: {
			'function' : 'translate',
			'q': [message],
			'target': info.server_lang,
		},
		dataType: "json",
		success: function(data){
			var	translation = data.text.translations[0].translatedText;
			document.getElementById("translated").innerHTML = translation;
		}
	})
}

function translate(message){
	if (info.send_translate == 1)
	{
		translateToServer(message);
	}
	else
	{
		array_backtrans = [];
		array_fortrans = [];
		array_translated = [];
		arr = message.split(" ");
		for (var i = 0; i < arr.length; i++) {
			if (arr[i].indexOf("!!") == arr[i].lastIndexOf("!!") && arr[i].indexOf("!!") == 0)
			{
				array_backtrans.push(arr[i].substring(2));
				arr[i] = null;
			}
		}
		for (var i = 0; i < arr.length; i++)
		{
			if (arr[i] == null)
			{
				array_fortrans.push(null);
			}
			else
			{
				if (array_fortrans[array_fortrans.length-1] == null){
					array_fortrans.push(arr[i]);
				}
				else
				{
					array_fortrans[array_fortrans.length-1] +=  " " + arr[i];
				}
			}
		}
		if (array_backtrans.length > 0)
		{
			$.ajax({
					type: "POST",
					url: "process.php",
					data: {
						'function' : 'translate',
						'q': array_backtrans,
						'target': info.server_lang,
					},
					dataType: "json",
					success: function(data){
						translations = data.text.translations;
							$.ajax({
							type: "POST",
							url: "process.php",
							data: {
								'function' : 'translate',
								'q': array_fortrans,
								'target': info.source_lang,
							},
							dataType: "json",
							success: function(data2){
								translations2 = data2.text.translations;
								var j = 0;
								for (var i = 0; i < translations2.length; i++)
								{
									if (translations2[i].translatedText == "")
									{
										array_translated.push(translations[j].translatedText);
										j++;
									}
									else
									{
										array_translated.push(translations2[i].translatedText);
									}
								}
								var	translation = array_translated.join(" ");
								var text = "";
								if (translation == "")
								{
									document.getElementById('translated').innerHTML = "";
								}
								else{
									if (data2.text.translations[0].detectedSourceLanguage != info.server_lang)
									{
										text += "Warning: language detected is " + data.language_detected + ", not " + info.server_name + " | " + translation;
										document.getElementById('translated').innerHTML = text;
									}
									else{
										document.getElementById('translated').innerHTML = translation;
									}
								}
							}
						})
					}
				})
			}
			else
			{
					$.ajax({
					type: "POST",
					url: "process.php",
					data: {
						'function' : 'translate',
						'q': array_fortrans,
						'target': info.source_lang,
					},
					dataType: "json",
					success: function(data){
						var	translation = data.text.translations[0].translatedText;
						var text = "";
						if (translation == "")
						{
							document.getElementById('translated').innerHTML = "";
						}
						else{
							if (data.text.translations[0].detectedSourceLanguage != info.server_lang)
							{
								text += "Warning: language detected is " + data.language_detected + ", not " + info.server_name + " | " + translation;
								document.getElementById('translated').innerHTML = text;
							}
							else{
								document.getElementById('translated').innerHTML = translation;
							}
						}
						
					}
				})
			}
		}
}

function catchUp(){
		$.ajax({
			type: "POST",
			url: "process.php",
			data: {
				'function': 'catchUp',
				'serverid': info.curr_serv,
			},
			dataType: "json",

			success: function(data){
				if(data.text){
					for (var i = 0; i < data.text.length; i++) {
                        $('#chat-area').append(data.text[i]);
                    }								  
			   }
				if (data.state)	{
					state = data.state;
				}
			   document.getElementById('chat-area').scrollTop = document.getElementById('chat-area').scrollHeight;
			}
		})
}

//Updates the chat
function updateChat(){
	if (!updating)
	{
		updating = true;
		     $.ajax({
				   type: "POST",
				   url: "process.php",
				   data: {  
				   			'function': 'update',
				   			'serverid': info.curr_serv,
							'state': state,
							},
				   dataType: "json",
				   success: function(data){
					   if(data.text){
							for (var i = 0; i < data.text.length; i++) {
	                            $('#chat-area').append(data.text[i]);
				    			document.getElementById('chat-area').scrollTop = document.getElementById('chat-area').scrollHeight;
	                        }								  
					   }
					   if(data.state){
					   	state = data.state;
					   }
					   updating = false;
				   }
				});
	}
}

function sendChat(message){
	if(info.send_translate == 1)
	{
		if (document.getElementById('translated').innerHTML != "")
		{
	    	$.ajax({
			   type: "POST",
			   url: "process.php",
			   data: {  
			   			'function': 'send',
						'message': document.getElementById('translated').innerHTML,
						'serverid': info.curr_serv,
						'userid': info.user
					 },
			   dataType: "json",
			   success: function(data){
				   updateChat();
			   },
			});
		}
	}
	else{
		$.ajax({
			   type: "POST",
			   url: "process.php",
			   data: {  
			   			'function': 'send',
						'message': message,
						'serverid': info.curr_serv,
						'userid': info.user
					 },
			   dataType: "json",
			   success: function(data){
				   updateChat();
			   },
			});	
	}
}
