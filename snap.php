<?php
require_once('./snaphax.php');
$key = 'somekey';

if (!isset($_GET['d']))
	die('no data');

$data = unserialize(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($_GET['d']), MCRYPT_MODE_ECB));
$opts = array();
$opts['username'] = $data['u'];

$s = new Snaphax($opts);
$s->auth_token = $data['at'];

header("Content-Type: image/png");
$blob_data = $s->fetch($data['id']);
echo $blob_data;
?>