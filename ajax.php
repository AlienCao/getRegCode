<?php
define('AJAX','1');
require_once('../global.php');
L::loadClass('forum', 'forum', false);
L::loadClass('post', 'forum', false);

S::gp(array('action','page'));
S::gp(array('car_modlename','car_typename','car_typename_4s','attachmentfiles'));
S::gp(array('hidden_viewcolony','hidden_viewbbs','hidden_urlall','hidden_type','hidden_minor_category','hidden_car_typename','hidden_carprice','hidden_caryear','speicalcar'));
//fid_sel
S::gp(array('selval'));
//
S::gp(array('pid','uid','aid'));
//20140807 caoyj add end 
if($action == 'mblike'){

	empty($winduid) && Showmsg('illegal_pid');
	$result = $db->get_one("SELECT like_uids,likecounts FROM pw_posts1 WHERE pid = '".$pid."'");

	empty($result) && Showmsg('illegal_pid');
	if(in_array($winduid,explode(',',$result[like_uids]))){
		//-1
		$winduidtemp = $winduid.',';
		$result['like_uids'] = str_replace($winduidtemp,'',$result['like_uids']);
		$result['likecounts'] = $result['likecounts']-1;
		$db->query("UPDATE pw_posts1 SET like_uids ='".$result['like_uids']."' ,likecounts = '".$result['likecounts']."' WHERE pid = '".$pid."'");
		$db->free_result($query);
		echo "success\t".$result[likecounts]."\t"."nored";
	}else{
		//+1
		if($result['like_uids'] == ''){
			$result['like_uids'] = $winduid.',';
		}else{
			$result['like_uids'] = $result['like_uids'].$winduid.',';
		}
		
		$result['likecounts'] = $result['likecounts']+1;
		$db->query("UPDATE pw_posts1 SET like_uids ='".$result['like_uids']."' ,likecounts = '".$result['likecounts']."' WHERE pid = '".$pid."'");
		$db->free_result($query);
		echo "success\t".$result[likecounts]."\t"."red";
	}
	ajax_footer();
}elseif($_POST['action'] == 'deleteattach'){

		$db->query("UPDATE pw_attachs SET tid ='1'  WHERE aid = '".$aid."'");

}elseif($action == 'postmessage'){
	
		$count = $db->get_value("SELECT COUNT(*) AS count FROM pw_topictype WHERE fid = '".$selval."'");

		if($count > 0){
			
			$sql= "SELECT id,name FROM pw_topictype WHERE fid = '".$selval."' ORDER BY vieworder";
			
			$result = $db->query($sql);
			
			$dbresult = "<select name='mb_type_v' class='mr5' id='car_typename'  style='width:30%;'>";
			
			while ($rt = $db->fetch_array($result)) {
			
				$dbresult .= "<option value='".$rt[id]."'>".$rt[name]."</option>";
			
			}
			$dbresult .= '</select>';
			
			$db->free_result($result);
			
			echo "success\t".$dbresult;
			
		}else{
			
			echo "fail\t";
			
		}
		ajax_footer();
}elseif($action == 'attention'){
	S::gp(array('uid', 'add'), 'GP', 2);
		if($add){
			define('AJAX',1);
			
			(!$winduid && !$uid) && Showmsg('undefined_action');
			
			//PostCheck();
			if ($uid == $winduid) {
				Showmsg('attention_self_add_error');
			}
			$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
			$member = $userService->get($uid);//uid,username,icon
			if (!$member) {
				$errorname = '';
				Showmsg('user_not_exists');
			}
			
			$attentionService = L::loadClass('Attention', 'friend'); /* @var $attentionService PW_Attention */
			if (($ifAttention = $attentionService->isFollow($winduid, $uid))) {
				Showmsg('attention_already_exists');
			}
			if ($attentionService->isInBlackList($uid, $winduid)) {
				Showmsg('对方已设置隐私，您无法加为关注!');
			}
			
			if (!$ifAttention && ($return = $attentionService->addFollow($winduid, $uid)) !== true) {
				Showmsg($return);
			}
// 			if ($recommend) {
// 				$userCache = L::loadClass('UserCache', 'user');
// 				$userCache->delete($winduid, 'recommendUsers');
// 			}
			echo "success\t";
			ajax_footer();
		}else{
			(!$winduid && !$uid) && Showmsg('undefined_action');
			
			//PostCheck();
			if ($uid == $winduid) {
				Showmsg('undefined_action');
			}
			
			$userService = L::loadClass('UserService', 'user'); /* @var $userService PW_UserService */
			$member = $userService->get($uid);//uid,username,icon
			$errorname = $member['username'];
			!$member && Showmsg('user_not_exists');
			
			$attentionService = L::loadClass('Attention', 'friend'); /* @var $attentionService PW_Attention */
			if (($return = $attentionService->delFollow($winduid, $uid)) !== true) Showmsg($return);
			$userCache = L::loadClass('UserCache', 'user'); /* @var $userCache PW_Usercache */
			$userCache->delete($winduid, 'recommendUsers');
			echo "success\t";
			ajax_footer();
		}
}elseif($action=="telCheck"){
	S::gp(array('tel'));
	$sql= "SELECT * FROM jx09_register_code WHERE mobile = '".$tel."'";
	$result = $db->query($sql);
	$hasTel = 0;
	while ($rt = $db->fetch_array($result)) {
		$hasTel = 1;
	}
	echo json_encode($hasTel);
}
?>