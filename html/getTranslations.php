<?php
    require_once('../mysql.php');
    require_once('../translate.php');
	$url = 'https://translation.googleapis.com/language/translate/v2/languages?key=' . $apiKey . '&target=en';
	$handle = curl_init($url);
	curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);    
	$response = curl_exec($handle);
	$responseDecoded = json_decode($response, true);                    
	curl_close($handle);
	$query = "INSERT IGNORE into Servers (langcode, langname) values ";
	foreach($responseDecoded['data']['languages'] as $response)
	{
		$value = '("' . $response['language'] . '", "' . $response['name'] .'"), ';
		$query = $query . $value;
	}
	$query = chop($query, ", ") . ";";
    $stmt = mysqli_prepare($dbc, $query);
    mysqli_stmt_execute($stmt);
?>