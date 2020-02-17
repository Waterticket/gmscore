<?PHP
/**
* @class gmScore모듈의 gmscoreView 클래스
* @author 탱곰이 (runway3207@hanmail.net)
* @개인이 소유한 스코어보드 출력
*
* 각종 쿼리 & 계산
**/

class gmscoreModel extends gmscore
{
	var $config;

	/**
	 * @brief 초기화
	 */
	function init()
	{
	}

	function getConfig()
	{
		if(!$this->config)
		{
			$oModuleModel = getModel('module');
			$config = $oModuleModel->getModuleConfig('gmscore');

			if(!$config->free_max_board) $config->free_max_board = '3';
            if(!$config->pro_max_board) $config->pro_max_board = '100';
            if(!$config->free_max_list) $config->free_max_list = '50';
            if(!$config->pro_max_list) $config->pro_max_list = '1000';
            if(!$config->pro_upgrade_cost) $config->pro_upgrade_cost = '1000';
			//if(!$config->) $config-> == '';
			$this->config = $config;
		}
		return $this->config;
	}
	/**
	 * @brief 모듈의 존재를 나타내도록
	 */
	function getGMscoreInfo()
	{
		$output = executeQuery('gmscore.getBoard');
		if(empty($output->data->module_srl)) return;
		$oModuleModel = &getModel('module');
		$module_info = $oModuleModel->getModuleInfoByModuleSrl($output->data->module_srl);

		return $module_info;
	}
	/**
	 * @brief 회원이 관리하는 스코어보드 모두 출력
	 */
	function getUserBoard($member_srl)
	{
		$arg = new stdClass;
		$arg->member_srl = $member_srl;
		$output = executeQuery('gmscore.getUserBoard',$arg);
		return $output->data;
	}

	/**
	 * @brief 특정 보드의 스코어를 출력
	 */
	function getScoreBoard($game_name)
	{
		$args = new stdClass;
        $args->game_name = $game_name;
        //게임네임에 해당하는 토큰 검색
        $output = executeQuery('gmscore.getBoardInfo',$args);
        
        //게임이 존재하지 않을경우
        if(empty($output->data->game_token))
        {
            return -1;
        }
        $args = new stdClass;
        $args->game_token = $output->data->game_token;
        $s = $output->data->sort_type;
        $c = $output->data->background_color;
		$ds = $output->data->game_des;
        //스코어 불러오기
		$output = executeQueryArray('gmscore.getScore',$args);
		$output->sort_type = $s;
		$output->color = $c;
		$output->game_des = $ds;
		return $output;
	}
    
    function getScoreBoard_json($game_token)
	{
		$args = new stdClass;
        $args->game_token = $game_token;
        //게임네임에 해당하는 토큰 검색
        $output = executeQuery('gmscore.getBoardInfo',$args);
        
        //게임이 존재하지 않을경우
        if(empty($output->data->game_token))
        {
            return -1;
        }
        $args = new stdClass;
        $args->game_token = $output->data->game_token;
        $s = $output->data->sort_type;
        $c = $output->data->background_color;
		$ds = $output->data->game_des;
        //스코어 불러오기
		$output = executeQueryArray('gmscore.getScore',$args);
		$output->sort_type = $s;
		$output->color = $c;
		$output->game_des = $ds;
		return $output;
	}
	
	/**
	 * @brief 특정 보드의 스코어를 출력 (게임 토큰으로)
	 */
	function getScoreBoard_byToken($game_token)
	{
		$args = new stdClass;
        $args->game_token = $game_token;
        //게임네임에 해당하는 토큰 검색
        $output = executeQuery('gmscore.getBoardInfo',$args);
        
        //게임이 존재하지 않을경우
        if(empty($output->data->game_token))
        {
            return -1;
        }
        $args = new stdClass;
        $args->game_token = $output->data->game_token;
        $s = $output->data->sort_type;
        //스코어 불러오기
		$output = executeQueryArray('gmscore.getScore',$args);
		$output->sort_type = $s;
		return $output;
	}
    
    /**
	 * @brief 스코어 전송
	 */
	function sendScore($args)
	{
        //게임토큰 유효여부 검색
        $is_valid_token = executeQuery('gmscore.checkToken',$args);
        if($is_valid_token->data->count<=0) {
            //토큰이 유효하지 않을경우
            return -2;
        }
		//해시값 체크
		$GetUsrSecret = executeQuery('gmscore.getUserSecret',$args);

        if($args->hash != sha1($args->name.$args->score.$GetUsrSecret->data->game_secret))
        {	//이름 + 스코어 + 클라 시크릿
			//해시값이 일치하지 않을경우
			return -1;
		}
		
        //name_check값이 없을경우 이름 중복검사 + usr_srl이 음수가 아닐경우
        if(!$args->name_check&&$args->usr_srl>0)
        {
			$chk = executeQuery('gmscore.checkNameE',$args);
				
			if(!$chk->data) 
			{
				$out = executeQuery('gmscore.insertScore',$args);
				return 1;
			}
			
            //점수가 이전점수보다 높을경우
            if($chk->data->score<$args->score){
                //스코어 업데이트
                executeQuery('gmscore.updateScore',$args);
                return 2;
            }
            else
            {
                //점수가 이전점수보다 낮을경우
                return -3;
            }
        }
        //스코어 등록
        $out = executeQuery('gmscore.insertScore',$args);
        return 1;
	}
    
    /**
	 * @brief 프로 여부 체크
	 */
	function checkIfPro($member_srl)
	{
		$arg = new stdClass;
		$arg->member_srl = $member_srl;
		$output = executeQuery('gmscore.checkPro',$arg);
        if($output->data->count > 0 )
        {
            return true;
        }
        else
        {
            return false;
        }
	}
    
    /**
	 * @brief 보드가 존재하는지 체크
	 */
	function checkIfBoardExists($game_name)
	{
		$arg = new stdClass;
		$arg->game_name = $game_name;
		$output = executeQuery('gmscore.checkBoardExists',$arg);
		if($output->data->count > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
	}
    function checkIfavailable($member_srl, $max_board)
	{
		$arg = new stdClass;
		$arg->member_srl = $member_srl;
		$output = executeQueryArray('gmscore.getUserBoard',$arg);
        $output = count($output->data);
		if($output < $max_board)
        {
            return true;
        }
        else
        {
            return false;
        }
	}
}
