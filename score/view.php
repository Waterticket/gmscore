<?php
	define('__XE__',true);
	require_once("../config/config.inc.php");
	$oContext = &Context::getInstance();
	$oContext->init();
	
	$oGMscoreModel = getModel('gmscore');
	$game_name = Context::get('game_name');
	if(!$game_name) echo "현재 이 게임 스코어보드는 존재하지 않습니다. 게임 개발자에게 문의하세요.";
	
	$out=$oGMscoreModel->getScoreBoard($game_name);
	$scores = $out->data;
	$is_Pro = 1;
	
	$n = 1;
	//echo "sort_type: ". $out->sort_type. "<br>background_color:" . $out->color. "<br>";
?>
<link rel="stylesheet" href="//www.cookiee.net/score/css/gmscore.css" />
<link rel="stylesheet" href="//www.cookiee.net/score/css/bootstrap.min.css" />
<link rel="icon" href="//www.cookiee.net/favicon.ico" type="image/x-icon" />

<title><?=$game_name;?> - 쿠키 스코어보드</title>
<section class="xm bs3-wrap">
<div id="general">
    <div id=head style="background-color: <?=$out->color?>;"><center><?=$game_name;?></center><div class=description><?=$out->game_des;?></div></div>
	
<div id="game_list">
	<? if($is_Pro != 1){ ?>
	<br>
	<div style="width:730px; height:100px; margin:auto;">
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<!-- board_pc -->
	<ins class="adsbygoogle"
		 style="display:inline-block;width:728px;height:90px"
		 data-ad-client="ca-pub-1032849332108543"
		 data-ad-slot="9580407941"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
	</div>
	
	<?php
	}	
	$s_type = ($out->sort_type == "asc") ? SORT_ASC:SORT_DESC;
	//echo $s_type;
	foreach ($scores as $key => $row) {
		//echo $key. ", ".$row->name." ".$row->score."</br>";
		$score[$key]  = $row->score;
		$name[$key] = $row->name;
	}
	array_multisort($score, $s_type,SORT_NUMERIC, $name);
	//print_r($scores);
	//print_r($score);
	
	for($i = 0; $i < sizeof($score); $i +=1){
		echo "<div class='game_list'>";
		echo "<div class='list_st'>".($i+1)."</div>";
		echo "<div class='list_name'>".$name[$i]."</div>";
		echo "<div class='list_score'>".$score[$i]."</div>";
		echo "</div>";
		$n += 1;
	}
	
	?>   
</div>
</div>