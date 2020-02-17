<?php
date_default_timezone_set("Asia/Seoul");
define('__XE__',true);
require_once("../config/config.inc.php");
$oContext = &Context::getInstance();
$oContext->init();
$oGMscoreModel = getModel('gmscore');
$args = new stdClass;
$args->game_token = Context::get('token');
$args->name = Context::get('name');
$args->score = Context::get('score');
$args->hash = Context::get('hash');
$args->name_check =Context::get('nchk');
$args->log_time = date("Y-m-d H:i:s",time());

if(!$args->game_token || !$args->name || !$args->score)
{
    echo "-4";return -1;
}

if((string)(int)$args->score != $args->score)
{
    echo "-5";return -1;
}

if(!Context::get('member_srl')){
	$args->usr_srl = -1;
}else{
	$args->usr_srl = Context::get('member_srl');
}
$out=$oGMscoreModel->sendScore($args);
echo $out;
?>