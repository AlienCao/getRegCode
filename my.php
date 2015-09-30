<?php
!defined('W_P') && exit('Forbidden');
InitGP ( array ('uid','goto') );
!$winduid && wap_msg ('not_login');
require_once(W_P.'include/db/myspace.db.php');
pwCache::getData(D_P.'data/bbscache/constant_post.php');
$myspace = new MyspaceDB();
if($goto == 'articles'){
	if($uid && $uid != $winduid){
		$results = $myspace->getArticlesByUser($uid);
	}else{
		$results = $myspace->getArticlesByUser($winduid);
	}
	$bodybackground = 'read';
	wap_header ();
	require_once PrintWAP ('mylist');
	wap_footer ();
}elseif($goto == 'replaies'){
	if($uid && $uid != $winduid){
		$results = $myspace->getReplaysByUser($uid);
	}else{
		$results = $myspace->getReplaysByUser($winduid);
	}
	$bodybackground = 'read';
	wap_header ();
	require_once PrintWAP ('mylist');
	wap_footer ();
}elseif($goto == 'mymsg'){
	$results = $myspace->getArticlesByUser($uid);
	$bodybackground = 'read';
	wap_header ();
	require_once PrintWAP ('???');
	wap_footer ();
}elseif($goto == 'collection'){
	$results = $myspace->getArticlesByUser($uid);
	$bodybackground = 'read';
	wap_header ();
	require_once PrintWAP ('???');
	wap_footer ();
}elseif($goto == 'fans'){
	$attentionSerivce = L::loadClass('Attention', 'friend');
	if($uid && $uid != $winduid){
		$attentions = $attentionSerivce->getFansListInPageMB($uid);
	}else{
		$attentions = $attentionSerivce->getFansListInPageMB($winduid);
	}
	$bodybackground = 'read';
	wap_header ();
	require_once PrintWAP ('mylist');
	wap_footer ();
}elseif($goto == 'myedit'){
	//$results = $myspace->getArticlesByUser($uid);
	$rt = $db->get_one("SELECT bday,gender,email,oicq
						FROM pw_members
						WHERE uid=".pwEscape($winduid));
	foreach ($bdayyearArr as $key => $value) {
		if(substr($rt['bday'],0,4) == $key){
			$yearHTML.= "<option value='".$key."' selected='selected'>".$value."</option>";
		}else{
			$yearHTML.= "<option value='".$key."' >".$value."</option>";
		}
 	}
 	foreach ($bdaymonthArr as $key => $value) {
 		if(substr($rt['bday'],5,2) == $key){
 			$monthHTML.= "<option value='".$key."' selected='selected'>".$value."</option>";
 		}else{
 			$monthHTML.= "<option value='".$key."' >".$value."</option>";
 		}
 	}
 	foreach ($bdaydayArr as $key => $value) {
 		if(substr($rt['bday'],8,2) == $key){
 			$dayHTML.= "<option value='".$key."' selected='selected'>".$value."</option>";
 		}else{
 			$dayHTML.= "<option value='".$key."' >".$value."</option>";
 		}
 	}
 	
 	switch ($rt['gender']) {
 		case '1' :
 			$sexHTML ="
 			<input type='radio' name='gender' value='0'>保密
 			<input type='radio' name='gender' value='1' checked>男
 			<input type='radio' name='gender' value='2'>女";
 			break;
 		case '2' :
 			$sexHTML ="
 			<input type='radio' name='gender' value='0'>保密
 			<input type='radio' name='gender' value='1'>男
 			<input type='radio' name='gender' value='2' checked>女";
 			break;
 		default:
 			$sexHTML ="
 			<input type='radio' name='gender' value='0' checked>保密
 			<input type='radio' name='gender' value='1'>男
 			<input type='radio' name='gender' value='2'>女";
 		}
 	$db->free_result($rt);
	$bodybackground = 'read';
	wap_header ();
	require_once PrintWAP ('myedit');
	wap_footer ();
}elseif($goto == 'updatemyedit'){
	InitGP (array('uid','gender','bday_year','bday_month','bday_day','regqq','proemail','propwdold','propwdnew'));
	empty($propwdold) && wap_msg('密码不能为空', "index.php?a=my&uid=$uid&goto=myedit");
	$rt = $db->get_one("SELECT password FROM pw_members WHERE uid=".pwEscape($winduid));
	if($propwdnew == $propwdold){
		//wap_msg('输入的新密码和原密码相同，请确认！',"index.php?a=my&uid=$uid&goto=myedit");
	}
	
	if($rt['password'] == md5($propwdold)){
		$bday = $bday_year."-".$bday_month."-".$bday_day;
		$db->update("UPDATE	pw_members SET	gender = '".$gender."',bday = '".$bday."',oicq = '".$regqq."',email = '".$proemail."',password = '".md5($propwdnew)."' WHERE uid = ".S::sqlEscape($winduid));
		wap_msg('修改成功！',"index.php?a=my&uid=$uid&goto=myedit");
	}else{
		wap_msg('原密码输入有误',"index.php?a=my&uid=$uid&goto=myedit");
	}
	
	$bodybackground = 'read';
	wap_header ();
	require_once PrintWAP ('mylist');
	wap_footer ();
}
?>