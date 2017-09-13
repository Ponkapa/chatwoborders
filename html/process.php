<?php
    session_start();
    require_once('../mysql.php');
    require_once('../translate.php');
    $function = $_POST['function'];
    
    $log = array();        
    if (isset($_SESSION['last']))
    {
        $last = $_SESSION['last'];
    }
    else
    {
        $last = "";
    }
    switch($function) {
        
        case('load'):
            $servers = array();
            $stmt = $dbc->prepare("select langcode, langname from Servers");
            $stmt->execute();
            $stmt->bind_result($langcode, $langname);
            while ($stmt->fetch())
            {
                    $servers[] = "<button class=\"serverButton\" onclick='chat.changeServer(\"" . $langcode . "\")'>" . $langname . "</button>";
            }
            $langcode = $_POST['source'];
            $stmt = $dbc->prepare("select langname from Servers where langcode = ?");
            $stmt->bind_param('s', $langcode);
            $stmt->execute();
            $stmt->bind_result($langname);
            $stmt->fetch();
            $log['source_name'] = $langname;
            $log['servers'] = $servers;
        break;

        case('changeServer'):
            $langcode = $_POST['langcode'];
            $uid = $_POST['userid'];
            $stmt = $dbc->prepare("select sid, langname from Servers where langcode = ?");
            $stmt->bind_param('s', $langcode);
            $stmt->execute();
            $stmt->bind_result($sid, $name);
            $stmt->fetch();
            $stmt->close();
            $stmt = $dbc->prepare("update Users set sid = (?) where uid = (?)");
            $stmt->bind_param('ii', $sid, $uid);
            $stmt->execute();
            $stmt->close();
            $_SESSION['serv'] = $sid;
            $log['sid'] = $sid;
            $_SESSION['server_lang'] = $langcode;
            $_SESSION['server_name'] = $name;
            $log['name'] = $name;
            mysqli_close($dbc);
        break;
        

         case('catchUp'):
            $text = array();
            $sid = $_POST['serverid'];
            $stmt = $dbc->prepare("select u.nickname, m.content, m.sent, m.CID from Messages m, Users u where m.SID = ? and u.UID = m.UID ORDER BY sent asc");
            $stmt->bind_param('i', $sid);
            $stmt->execute();
            $stmt->bind_result($nickname, $content, $sent, $CID);
            $last = "";
            while ($stmt->fetch())
            {
                if ($last == $nickname)
                {
                    $text[] = "<p><span class='hidden'>" . $nickname . "</span>". $content . "</p>";
                }
                else
                {
                    $text[] = "<div id='message-top'></div><p><span>" . $nickname . "</span>" . $content . "</p>";
                }
                $last = $nickname;
                $state = $CID;
            }
            $_SESSION['last'] = $last;
            $log['state'] = $state;
            $log['text'] = $text;
            mysqli_close($dbc);
        break;
    	
    	 case('update'):
            $state = $_POST['state'];
            $text = array();
            $sid = $_POST['serverid'];
            $stmt = $dbc->prepare("select u.nickname, m.content, m.sent, m.CID from Messages m, Users u where m.SID = ? and m.cid > ? and u.UID = m.UID ORDER BY sent asc");
            $stmt->bind_param('ii', $sid, $state);
            $stmt->execute();
            $stmt->bind_result($nickname, $content, $sent, $CID);
            $last = "";
            while ($stmt->fetch())
            {
                if ($last == $nickname)
                {
                    $text[] = "<p><span class='hidden'>" . $nickname . "</span>". $content . "</p>";
                }
                else
                {
                    $text[] = "<div id='message-top'></div><p><span>" . $nickname . "</span>" . $content . "</p>";
                }
                $last = $nickname;
                $state = $CID;
            }
            $_SESSION['last'] = $last;
            $log['state'] = $state;
            $log['text'] = $text;
            mysqli_close($dbc);
        break;
    	 
    	case('send'):
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
		$message = htmlentities(strip_tags($_POST['message']));
		 if(($message) != "\n"){
	         $sid = $_POST['serverid'];
             $uid = $_POST['userid'];
			 if(preg_match($reg_exUrl, $message, $url)) {
       			$message = preg_replace($reg_exUrl, '<a href="'.$url[0].'" target="_blank">'.$url[0].'</a>', $message);
				} 
			 $query = "insert into Messages (content, sid, uid) values (?, ?, ?)";
             $stmt = mysqli_prepare($dbc, $query);
             mysqli_stmt_bind_param($stmt, "sii", $message, $_POST['serverid'], $_POST['userid']); 
            mysqli_stmt_execute($stmt);


             $affected_rows = mysqli_stmt_affected_rows($stmt);
              mysqli_stmt_affected_rows($stmt);

            if ($affected_rows == 1){
                $log['debug'] = null;
            }
            else{
                $log['debug'] = 'Error Occured <br />' . mysqli_error();
            }
                mysqli_stmt_close($stmt);
                mysqli_close($dbc);
		} else {
            $log['debug'] = 'Message is newline';
        }
        break;

    	case('translate'):
            $target = $_POST["target"];
            $text = $_POST["q"];
            $url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey;
            foreach($text as $query)
            {
                $url = $url . '&q=' . rawurlencode($query);
            }
            if ($target){
                $url = $url . '&target=' . $target;
            }
            $handle = curl_init($url);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);     //We want the result to be saved into variable, not printed out
            $response = curl_exec($handle);
            $responseDecoded = json_decode($response, true);                    
            curl_close($handle);
            $source = $responseDecoded['data']['translations'][0]['detectedSourceLanguage'];
            $stmt = $dbc->prepare("select langname from Servers where langcode = ?");
            $stmt->bind_param('s', $source);
            $stmt->execute();
            $stmt->bind_result($source_name);
            $stmt->fetch();
            $log['language_detected'] = $source_name;
            $log['text'] = $responseDecoded['data'];
            break;
    }


    echo json_encode($log);

?>

