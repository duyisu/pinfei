<?php
if (!defined('IN_IA')) 
{
	exit('Access Denied');
}
class Info_EweiShopV2Page extends MobileLoginPage 
{
	protected $member;
	public function __construct() 
	{
		global $_W;
		global $_GPC;
		parent::__construct();
		$this->member = m('member')->getInfo($_W['openid']);
	}
	protected function diyformData() 
	{
		$template_flag = 0;
		$diyform_plugin = p('diyform');
		if ($diyform_plugin) 
		{
			$set_config = $diyform_plugin->getSet();
			$user_diyform_open = $set_config['user_diyform_open'];
			if ($user_diyform_open == 1) 
			{
				$template_flag = 1;
				$diyform_id = $set_config['user_diyform'];
				if (!empty($diyform_id)) 
				{
					$formInfo = $diyform_plugin->getDiyformInfo($diyform_id);
					$fields = $formInfo['fields'];
					$diyform_data = iunserializer($this->member['diymemberdata']);
					$f_data = $diyform_plugin->getDiyformData($diyform_data, $fields, $this->member);
				}
			}
		}
		return array('template_flag' => $template_flag, 'set_config' => $set_config, 'diyform_plugin' => $diyform_plugin, 'formInfo' => $formInfo, 'diyform_id' => $diyform_id, 'diyform_data' => $diyform_data, 'fields' => $fields, 'f_data' => $f_data);
	}
	public function main() 
	{
		global $_W;
		global $_GPC;
		$diyform_data = $this->diyformData();
		extract($diyform_data);
		$returnurl = urldecode(trim($_GPC['returnurl']));
		$member = $this->member;
		$wapset = m('common')->getSysset('wap');


        // 用户关系展示
        if (intval($member['oldagentid']) !== 0) {
            $agentInfo = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where id=' . $member['oldagentid']);
            $agent = $agentInfo['nickname'];
        } else if (intval($member['agentid']) !== 0) {
            $agentInfo = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where id=' . $member['agentid']);
            $agent = $agentInfo['nickname'];
        } else {
            $agent = '总店';
        }

        if (intval($member['isaagent']) === 1 && intval($member['aagentstatus']) === 1) {
            switch(intval($member['aagenttype'])) {
                case 1: $level = '省级代理商';break;
                case 2: $level = '市级代理商';break;
                case 3: $level = '区级代理商';break;
                default: $level = '分销商';break;
            }
        } else {
            $level = '分销商';
        }

        $memberRelative = array(
            'agent' => $agent,
            'level' => $level
        );

//        var_dump($memberRelative);exit;
		
		include $this->template();
	}
	public function submit() 
	{
		global $_W;
		global $_GPC;
		$diyform_data = $this->diyformData();
		extract($diyform_data);
		$memberdata = $_GPC['memberdata'];
		if ($template_flag == 1) 
		{
			$data = array();
			$m_data = array();
			$mc_data = array();
			$insert_data = $diyform_plugin->getInsertData($fields, $memberdata);
			$data = $insert_data['data'];
			$m_data = $insert_data['m_data'];
			$mc_data = $insert_data['mc_data'];
			$m_data['diymemberid'] = $diyform_id;
			$m_data['diymemberfields'] = iserializer($fields);
			$m_data['diymemberdata'] = $data;
			pdo_update('ewei_shop_member', $m_data, array('openid' => $_W['openid'], 'uniacid' => $_W['uniacid']));
			if (!empty($this->member['uid'])) 
			{
				if (!empty($mc_data)) 
				{
					m('member')->mc_update($this->member['uid'], $mc_data);
				}
			}
		}
		else 
		{
			pdo_update('ewei_shop_member', $memberdata, array('openid' => $_W['openid'], 'uniacid' => $_W['uniacid']));
			if (!empty($this->member['uid'])) 
			{
				$mcdata = $_GPC['mcdata'];
				m('member')->mc_update($this->member['uid'], $mcdata);
			}
		}
		show_json(1);
	}
}
?>