<?php	
function httpRequest($strUrl,$postfield=null)
{
	$ch = curl_init();
	if ($ch == false) {
		exit(3);
	}
    $user_agent = 'Mozilla/5.0 (Windows; U; 
Windows NT 5.1; ru; rv:1.8.0.9) Gecko/20061206 Firefox/1.5.0.9';
    $header = array(
"Accept: text/xml,application/xml,application/xhtml+xml,
text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
        "Accept-Language: ru-ru,ru;q=0.7,en-us;q=0.5,en;q=0.3",
        "Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7",
        "Keep-Alive: 300");
	curl_setopt($ch, CURLOPT_URL,$strUrl);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,5);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    if ($postfield !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfield);
    }
	// curl_setopt($ch, CURLOPT_AUTOREFERER,true);
	// curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
	// curl_setopt($ch, CURLOPT_FAILONERROR, true);
	$strText  = curl_exec($ch);
	$response = curl_getinfo($ch);
	curl_close($ch);
	if($response['http_code']==200 ){
		return $strText;
	} else {
        return false;
	}
}

// echo httpRequest('http://www.phpdo.net/curl_setopt.html');


