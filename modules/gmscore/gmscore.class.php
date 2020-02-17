<?php
/**
* @class gmScore모듈의 gmscoreView 클래스
* @author 탱곰이 (runway3207@hanmail.net)
* @개인이 소유한 스코어보드 출력
*
* 관리자페이지에 표시할 내용과 사용변수에 대한 정의/전달
**/

class gmscore extends ModuleObject 
{
    /**
    * @brief 모듈 설치
    **/
	function moduleInstall()
	{
		//moduleController 등록
		$oModuleController = getController('module');
		$oModule = getModel('module');
		$module_info = $oModule->getModuleInfoByMid('gmscore');
		if($module_info->module_srl)
		{
			//이미 만들어진 gmscore mid가 있다면
			if($module_info->module != 'gmscore')
			{
				return new BaseObject(1,'gmscore_error_mid');
			}
		}
		else
		{
			/*Create mid*/
			$oModuleController = getController('module');
			$args = new stdClass;
			$args->mid = 'gmscore';
			$args->module = 'gmscore';
			$args->browser_title = '쿠키 스코어보드 호스팅';
			$args->site_srl = 0;
			$args->skin = 'default';
			$args->order_type = 'desc';
			$output = $oModuleController->insertModule($args);
		}
	}

    /**
    * @brief 업데이트 할 만한 게 있는지 체크
    **/
	function checkUpdate()
	{
		return false;
	}

	/**
	* @brief 모듈 업데이트
	**/
	function moduleUpdate() {
		return new BaseObject();
	}
    /**
	 * @brief Receives a document title and returns an URL firendly name
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @param $entry_name string
	 * @return string 
	 */
	function beautifyEntryName($entry_name) 
	{
		$entry_name = strip_tags($entry_name); 
		$entry_name = html_entity_decode($entry_name); 
		$entry_name = preg_replace($this->omitting_characters, $this->replacing_characters, $entry_name); 
		$entry_name = preg_replace('/[_]+/', '_', $entry_name); 
		$entry_name = strtolower($entry_name); 
		return $entry_name;
	}
    
    /**
	 * @brief Returns qualified internal link, given an alias or doc title
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $document_name string
	 * @return string
	 */
	function getFullLink($document_name) 
	{
		return getUrl('', 'gmscore', $document_name, 'game_name', '');
	}
}
