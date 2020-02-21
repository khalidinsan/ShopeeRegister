<?php
$url = 'https://shopee.co.id/api/v0/captcha/';
$cookies = base64_decode($_GET['cookies']);
$crsf = $_GET['crsf'];
$header = array(
	'host: shopee.co.id',
	'accept: */*',
	'origin: https://shopee.co.id',
	'referer: https://shopee.co.id/',
	'sec-fetch-mode: cors',
	'sec-fetch-site: same-origin',
	'x-csrftoken: '.$crsf,
	'x-requested-with: XMLHttpRequest'
);
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $url);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch2, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31"); 
curl_setopt($ch2, CURLOPT_TIMEOUT, 60); 
curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch2, CURLOPT_COOKIE, $cookies);
curl_setopt($ch2, CURLOPT_HTTPHEADER, $header);
curl_setopt ($ch2, CURLOPT_REFERER, $url);
curl_setopt($ch2, CURLOPT_POST, 1);
$result = curl_exec ($ch2);
curl_close($ch2);
header('Content-type: image/jpeg');
echo $result;