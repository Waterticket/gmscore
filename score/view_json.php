<?php
	define('__XE__',true);
	require_once("../config/config.inc.php");
	$oContext = &Context::getInstance();
	$oContext->init();
	
	$oGMscoreModel = getModel('gmscore');
	$game_name = Context::get('game_token');
	$score_count = Context::get('count');
	if(!$game_name) echo "-1";
	
	$out=$oGMscoreModel->getScoreBoard_json($game_name);
	$scores = $out->data;
	$is_Pro = 1;
	
	$n = 1;
	//echo "sort_type: ". $out->sort_type. "<br>background_color:" . $out->color. "<br>";
	if($is_Pro != 1){
		echo "-2";
		return -2;
	}
	
	$s_type = ($sort_type == "asc") ? SORT_ASC:SORT_DESC;
	//echo $s_type;
	foreach ($scores as $key => $row) {
		//echo $key. ", ".$row->name." ".$row->score."</br>";
		$score[$key]  = $row->score;
		$name[$key] = $row->name;
		$log_time[$key] = $row->log_time;
	}
	array_multisort($score, $s_type,SORT_NUMERIC, $name, $log_time);
	//print_r($scores);
	//print_r($score);
	
	if(!$score_count){ //스코어 전체 출력
		echo '{"list":[';
		for($i = 0; $i < sizeof($score); $i +=1){
			echo '{"rank":'.($i+1).',"user_srl":-1'.',"name":"'.$name[$i].'","score":'.$score[$i].',"time":"'.$log_time[$i].'"'.'}';
			if($i+1 != sizeof($score)) echo ",";
			$n += 1;
		}
		echo '],"count":'.($n-1).'}';
	}else{ //스코어 부분 출력
		
		$score_count = (int)$score_count;
		if($score_count > sizeof($score)) $score_count = sizeof($score);
	
		echo '{"list":[';
		for($i = 0; $i < $score_count; $i +=1){
			echo '{"rank":'.($i+1).',"user_srl":-1'.',"name":"'.$name[$i].'","score":'.$score[$i].',"time":"'.$log_time[$i].'"'.'}';
			if($i+1 != $score_count) echo ",";
			$n += 1;
		}
		echo '],"count":'.$score_count.'}';
	}
?>   
