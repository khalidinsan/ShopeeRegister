
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
$url = "https://shopee.co.id/api/v0/buyer/login/email/signup";
if(isset($_GET['ignore'])){
	$lines = file('config.txt');
	$cookies = trim($lines[0]);
	$crsf = trim($lines[1]);
}else{
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31"); 
	curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, false); 
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt ($ch, CURLOPT_REFERER, $url);
	$result = curl_exec ($ch);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = substr($result, 0, $header_size);
	$body = substr($result, $header_size);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	$result = array($body,$httpcode,$header);
	preg_match_all('/^set-cookie:\s*([^;]*)/mi', $result[2], $cookie);
	$cookies = "";

	$crsf = explode('=', $cookie[1][9]);
	$crsf = $crsf[1];

	foreach($cookie[1] as $item) {
		$cookies .= $item.";";
	}
	$arr = json_decode($result[0]);

	// CREATE CONFIG
	$myfile = fopen("config.txt", "w") or die("Unable to open file!");
	$txt = $cookies."\r\n";
	fwrite($myfile, $txt);
	$txt = $crsf."\r\n";
	fwrite($myfile, $txt);
	fclose($myfile);
	// -------------
}
$cok = base64_encode($cookies);
$v_username = "waduksia".rand(0,99999);
$ehe = file('result.txt');
$jml = count($ehe);
?>
Jumlah Akun = <b><?=$jml?></b> <br>
<form method="POST" action="?ignore">
	<input type="text" name="username" placeholder="username" value="<?=$v_username?>"><br>
	<input type="text" name="email" placeholder="email" value="<?=$v_username.'@yopmail.com'?>"><br>
	<input type="text" name="password" placeholder="password" value="Waduk@12"><br>
	<img id="picFrame" src="capcay.php?cookies=<?=$cok?>&crsf=<?=$crsf?>">
	<a id="refresh" href="javascript:void(0)" onclick="refreshCaptcha()">refresh</a>
	<br>
	<input type="text" name="captcha" placeholder="captcha"><br>
	<input type="submit" name="submit" value="Submit">
</form>
<script type="text/javascript">
	function refreshCaptcha(){
		document.getElementById("refresh").innerHTML = "refreshing ...";
		var xhttp;
		xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				document.getElementById("picFrame").src = "capcay.php?cookies=<?=$cok?>&crsf=<?=$crsf?>";
				document.getElementById("refresh").innerHTML = "refresh";
			}
		};
		xhttp.open("GET", "capcay.php?cookies=<?=$cok?>&crsf=<?=$crsf?>", true);
		xhttp.send();
	}
</script>
<?php
if(!empty($_POST)){
	$username = $_POST['username'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$captcha = $_POST['captcha'];
	$password_hash = hash('sha256',(md5($password)));
	$post = array('username'=>$username,'email'=>$email,'password_hash'=>$password_hash,'captcha'=>$captcha);
	// print_r($post);die;
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
	curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post));
	$result = curl_exec ($ch2);
	curl_close($ch2);
	$result = json_decode($result);
	$err = $result->error;
	if($err>0){
		echo "[GAGAL] ";
		if($err==1){
			echo "Captcha salah";
		}elseif($err==105){
			echo "Email sudah terdaftar";
		}elseif($err==111){
			echo "Username sudah digunakan";
		}else{
			echo "#code = ".$err;
		}
	}else{
		echo "[BERHASIL] UserID : ".$result->user_id;
		$result_file = 'result.txt';
		if(file_exists($result_file)){
			$myfile = fopen($result_file, "a") or die("Unable to open file!");
		}else{
			$myfile = fopen($result_file, "w") or die("Unable to open file!");

		}
		$txt = $username."|".$password."\r\n";
		fwrite($myfile, $txt);
		fclose($myfile);
	}
}
