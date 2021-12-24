<?php

//URLs

$getCodeUrl = 'http://copybro/api/auth.sendCode';
$getTokenUrl = 'http://copybro/api/auth.confirmCode';
$logoutUrl = 'http://copybro/api/auth.logout';
$url = 'http://copybro/api/user.update';


//login

$headers_getCode = array(
	"Content-Type: application/json",
	'project: copybro',
	'v: 1',
);
$data_getCode = array(
	'phone' => '73235092455',
);
$ch_getCode = curl_init($getCodeUrl);
curl_setopt($ch_getCode, CURLOPT_HTTPHEADER, $headers_getCode);
curl_setopt($ch_getCode, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch_getCode, CURLOPT_POST, 1);
curl_setopt($ch_getCode, CURLOPT_POSTFIELDS, json_encode($data_getCode));
$res_getCode = curl_exec($ch_getCode);
if (!curl_errno($ch_getCode)) {
	if(($http_code_getCode = curl_getinfo($ch_getCode, CURLINFO_HTTP_CODE)) == 200) {
		$_resres_getCode = json_decode($res_getCode, true);
		if(isset($_resres_getCode['success']) && $_resres_getCode['success'] === 'true' && !empty($_resres_getCode['response']['code'])){
			$data_getCode['code'] = $_resres_getCode['response']['code'];
		}
	}
}
curl_close($ch_getCode);
if(!empty($data_getCode['code'])){
	$ch_getToken = curl_init($getTokenUrl);
	curl_setopt($ch_getToken, CURLOPT_HTTPHEADER, $headers_getCode);
	curl_setopt($ch_getToken, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch_getToken, CURLOPT_POST, 1);
	curl_setopt($ch_getToken, CURLOPT_POSTFIELDS, json_encode($data_getCode));
	$res_getToken = curl_exec($ch_getToken);
	if (!curl_errno($ch_getToken)) {
		if(($http_code_getToken = curl_getinfo($ch_getToken, CURLINFO_HTTP_CODE)) == 200) {
			$_resres_getToken = json_decode($res_getToken, true);
			if(isset($_resres_getToken['success']) && $_resres_getToken['success'] === 'true' && !empty($_resres_getToken['response']['token'])){
				$token = $_resres_getToken['response']['token'];
			}
		}
	}
	curl_close($ch_getToken);
}


//to API

if(!empty($token)){
	//call API
	
	$headers = array(
		'token: '.$token,
		'project: copybro',
		'v: 1',
		"Content-Type: application/json",
	);
	$data = array(
		'first_name' => 'Петров',
		'last_name' => 'Иван',
		'middle_name' => 'Иванович',
		'email' => 'ss@ll.tt',
		'phone' => '+7323509d-24-55',
	);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	$result = curl_exec($ch);
	if (!curl_errno($ch)) {
		$_result = json_decode($result, true);
		if(($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) == 200) {
			if(isset($_result['success']) && $_result['success'] === 'true' && !empty($_result['response'])){
				echo 'Success: '.$_result['response'].'<br>';
			}
		}else{
			if(isset($_result['success']) && $_result['success'] === 'false' && !empty($_result['error']['error_msg'])){
				echo 'Error: '.$_result['error']['error_msg'].'<br>';
			}
		}
	}
	curl_close($ch);

	//logout

	$ch_logout = curl_init($logoutUrl);
	curl_setopt($ch_logout, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch_logout, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch_logout, CURLOPT_POST, 1);
	$res_logout = curl_exec($ch_logout);
	if (!curl_errno($ch_logout)) {
		if(($http_code_logout = curl_getinfo($ch_logout, CURLINFO_HTTP_CODE)) == 200) {
			$_resres_logout = json_decode($res_logout, true);
			if(isset($_resres_logout['success']) && $_resres_logout['success'] === 'true' && !empty($_resres_logout['response']['message'])){
				echo $_resres_logout['response']['message'];
			}
		}
	}
	curl_close($ch_logout);
}


//exit

exit();

?>
