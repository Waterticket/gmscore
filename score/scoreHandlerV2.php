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

if(!Context::get('member_srl')){
	$args->usr_srl = -1;
}else{
	$args->usr_srl = Context::get('member_srl');
}
$out=$oGMscoreModel->sendScore($args);
$datas = $oGMscoreModel->getScoreBoard_byToken($args->game_token);
$scores = $datas->data;
$s_type = ($datas->sort_type == "asc") ? SORT_ASC:SORT_DESC;
foreach ($scores as $key => $row) {
	$score[$key]  = $row->score;
	$name[$key] = $row->name;
	$log_time[$key] = $row->log_time;
}
array_multisort($score, $s_type,SORT_NUMERIC, $name, $log_time);

for($i = 0; $i < sizeof($score); $i +=1){
	if($name[$i]==$args->name&&$score[$i]==$args->score){
		$ranks = ($i+1); 
		break;
	}
}
echo '{"status":'.$out.',"rank":'.$ranks.'}';
?>