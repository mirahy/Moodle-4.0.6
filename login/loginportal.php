<?php
require_once(__DIR__ . '/../config.php');

$s = required_param('s', PARAM_RAW);//PARAM_RAW
$cursoid = 1;

$systemcontext = context_system::instance();

$date = getdate();
  
$tms = $date['0'];

$portaldados = decrypt($s);

$dados = explode('&p=', $portaldados);

if($tms > ($dados[4]-500) AND $tms < ($dados[4]+500)){ //valida o dia do login -24h--3600
    $user = authenticate_user_login($dados[0], decrypt($dados[1]));
	$cursoid= $dados[2];
    complete_user_login($user);
} 
redirect("$CFG->wwwroot/"."course/view.php?id=$cursoid");


function decrypt($encryptedText)
{
    return trim( sodium_crypto_secretbox_open ( base64_decode(rawurldecode( $encryptedText )  ), hex2bin('4614aed4a010bc5d7c3bc0d396261c08ccc61db910b34159'), hex2bin('4ecf9ad8be9eaff7acbd3df377c75b75e4b00854baed5102dc4bb46e01150e0b') ) );
}