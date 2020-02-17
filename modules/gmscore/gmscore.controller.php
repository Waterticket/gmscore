<?PHP
/**
* @class gmScore모듈의 gmscoreView 클래스
* @author 탱곰이 (runway3207@hanmail.net)
* @개인이 소유한 스코어보드 출력
*
* 관리자페이지에 표시할 내용과 사용변수에 대한 정의/전달
**/

class gmscoreController extends gmscore
{
    function procGMscoreInsertBoard()
	{
        //권한 체크
        if(!$this->grant->create_scoreboard)
        {
            return new BaseObject(-1, 'msg_not_permitted');
        }
        //변수 불러오기
        $member_srl = Context::get('member_srl');
        $game_token = bin2hex(openssl_random_pseudo_bytes(16));
		$game_secret = bin2hex(openssl_random_pseudo_bytes(5));
		$args = new stdClass;
		$args->regdate = zDate(date("YmdHis"),"YmdHis");
		$args->member_srl = $member_srl;
        $args->game_name = Context::get('game_title');
        $args->game_des = Context::get('game_des');
        $args->game_token = $game_token;
		$args->game_secret = $game_secret;
        $args->sort_type = Context::get('sort_type');
        $args->background_color = Context::get('back_col');
        //타이틀 유효성 체크 (생략)
        /*(if ( preg_match('/[^\x{1100}-\x{11FF}\x{3130}-\x{318F}\x{AC00}-\x{D7AF}0-9a-zA-Z]/u',$args->game_name) ) 
        { 
            return new BaseObject(-1, '게임타이틀에는 영어, 숫자, 한글만 입력 가능합니다.');
        } */
		
        //로그인 체크
        if(!$member_srl)
        {
            return new BaseObject(-1, "로그인하셔야 등록가능합니다.");
        }
        
        //보드 생성 가능여부 체크
        $oGMscoreModel = getModel('gmscore');
        $config = $oGMscoreModel->getConfig();
        $max_board=0;
        //프로일 경우
        if($oGMscoreModel->checkIfPro($args->member_srl))
        {
            $max_board=$config->pro_max_board;
        }
        //프로가 아닐경우
        else
        {
            $max_board=$config->free_max_board;
        }
        if(!$oGMscoreModel->checkIfavailable($args->member_srl, $max_board))
        {
            return new BaseObject(-1, "더 이상 생성할 수 없습니다.");
        }
        
        //게임타이틀 중복체크
        $oGMscoreModel = getModel('gmscore');
        if($oGMscoreModel->checkIfBoardExists($args->game_name))
        {
            return new BaseObject(-1, "이미 있는 게임타이틀입니다.");
        }
        //Hex Color 유효성 체크
        if(!preg_match('/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)|(^[0-9A-F]{6}$)|(^[0-9A-F]{3}$)/i', $args->background_color))
        {
            return new BaseObject(-1, "색상은 Hex값으로 입력하셔야합니다 (예)흰색 : #ffffff");
        }
        //Hex Color에 #이 붙지 않았을경우 #을 붙힘
        if(!preg_match('/#/',$args->background_color))
        {
            $args->background_color = '#' . $args->background_color;
        }
        $output = executeQuery('gmscore.insertBoard',$args);
        $this->setRedirectUrl(getNotEncodedUrl('','mid','gmscore','act','dispGMscoreContent'));
	}
	
    function procGMscoreUpdateBoard()
	{
        //권한 체크
        if(!$this->grant->create_scoreboard)
        {
            return new BaseObject(-1, 'msg_not_permitted');
        }
        //변수 불러오기
        $member_srl = Context::get('member_srl');
		$args = new stdClass;	
		$args->regdate = zDate(date("YmdHis"),"YmdHis");
        $args->game_des = Context::get('game_des');
		$args->game_token = Context::get('game_token');
        $args->sort_type = Context::get('sort_type');
		$args->game_secret = Context::get('game_secret');
        $args->background_color = Context::get('back_col');
        //로그인 체크
        if(!$member_srl)
        {
            return new BaseObject(-1, "로그인하셔야 등록가능합니다.");
        }
        //Hex Color 유효성 체크
        if(!preg_match('/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)|(^[0-9A-F]{6}$)|(^[0-9A-F]{3}$)/i', $args->background_color))
        {
            return new BaseObject(-1, "색상은 Hex값으로 입력하셔야합니다 (예)흰색 : #ffffff");
        }
        //Hex Color에 #이 붙지 않았을경우 #을 붙힘
        if(!preg_match('/#/',$args->background_color))
        {
            $args->background_color = '#' . $args->background_color;
        }
        $output = executeQuery('gmscore.updateBoard',$args);
        $this->setRedirectUrl(getNotEncodedUrl('','mid','gmscore','act','dispGMscoreContent'));
	}
	
    function procGMscoredeleteBoard()
    {
        //권한 체크
        if(!$this->grant->create_scoreboard)
        {
            return new BaseObject(-1, 'msg_not_permitted');
        }
        $game_token = Context::get('game_token');
        if(!$game_token)
        {
            return new BaseObject(-1, 'Invalid Game Token');
        }
        $args = new stdClass;
        $args->game_token = $game_token;
        //점수 삭제
        executeQuery('gmscore.deleteScore',$args);
        //보드 삭제
        executeQuery('gmscore.deleteBoard',$args);
        
        $this->setRedirectUrl(getNotEncodedUrl('','mid','gmscore','act','dispGMscoreContent'));
    }
	
    function procGMscoreInsertPro()
    {
		$oModuleModel = getModel('module');
		$config = $oModuleModel->getModuleConfig('gmscore');
		
        //권한 체크
        if(!$this->grant->create_scoreboard)
        {
            return new BaseObject(-1, 'msg_not_permitted');
        }
        //변수 로드
        $member_srl = Context::get('member_srl');
        $point = Context::get('point');
        $hash = Context::get('hash');
        //변조 체크
        if($hash!=sha1($point.$member_srl.'joymaker'))
        {
            return new BaseObject(-1, '데이터가 변조되었습니다. 취소를 눌러서 뒤로 돌아가신 후 다시 시도해주세요.');
        }

        $oPointModel = &getModel('point'); //포인트 모듈을 불러오고
        $point = $oPointModel->getPoint($member_srl);
		
        //프로목록에 등록
		if($point < $config->pro_upgrade_cost)
		{
			return new BaseObject(-1, '포인트가 부족합니다.');
		}else{
		//포인트 차감
        $oPointController = &getController('point');
        $oPointController->setPoint($member_srl, 1000, 'minus');
        $args = new stdClass;
        $args->member_srl=$member_srl;
        $args->regdate=zDate(date("YmdHis"),"YmdHis");
        $args->enddate=zDate(date("YmdHis"),"YmdHis");
        $output=executeQuery('gmscore.insertPro',$args);
        //등록오류시
        if(!$output)
        {
            return new BaseObject(-1, '등록에 실패하였습니다 다시 시도해주세요.');
        }
		}
        
        $this->setRedirectUrl(getNotEncodedUrl('','mid','gmscore','act','dispGMscorePro'));
    }
}
