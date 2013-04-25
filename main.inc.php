<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
if (!$_G['uid']) {
    showmessage('not_loggedin', '', '', array('login' => 1));
}
require ('es_config.inc.php');
$action = dhtmlspecialchars($_G["gp_action"]);
$index = dhtmlspecialchars($_G['gp_module']);
$basename = 'plugin.php?id=websengoku:main';
$basename2 = "plugin.php?id=websengoku:main&module={$index}";
//print_r($_G);
$uid = intval($_G["uid"]);
//检查用户是否注册
$sql = "SELECT * FROM " . DB::table("es_member") . " WHERE uid= " . $uid;
$user_info = DB::fetch_first($sql);
//print_r($user_info);
if (empty($user_info) and $index != "reg") {
    showmessage('您尚未注册本游戏,下面将引导您注册.', 'plugin.php?id=websengoku:main&module=reg');
}

if($index=='logout'){
    $sql = "DELETE FROM " . DB::table('es_member') . " WHERE uid = " . $uid;
    DB::query($sql);
    
}
$global_cfg['admincp'] = $_G['cache']['plugin']['websengoku'];
$global_admincp = $global_cfg['admincp'];
if ($index) {
    require ($index . '.inc.php');
}
//var_dump($_G['cache']['plugin']['websengoku']);

//print_r($global_cfg);
/*
if ($index)
{
switch ($index)
{
case 'reg':
//        $sql = "SELECT uid FROM es_user WHERE uid= " . $_G['uid'];
//        $user_info = DB::fetch_first($sql);
//    	if (!empty($user_info)){
//    		showmessage('您已经注册了本游戏,不能重新注册','plugin.php?id=websengoku:main');
//    	}
//$sql = "SELECT zhiye FROM es_config"
$config_array = parse_ini_file("es_config.inc.php");
$zhiye_array = explode(",", $config_array['zhiye']);
$birth_city_array = explode(",", $config_array['birth_city']);
$nengli_array = explode(",", $config_array['nengli']);
//print_r ($zhiye_array);
break;
case 'myinfo':
require ('myinfo.inc.php');
break;
case 'chaoting':
require ('chaoting.inc.php');
//print_r($guan_info_array);
break;
default:
require ($index . '.inc.php');
//echo $index . '.inc.php';
break;
}
}*/
include template('websengoku:main');
//include template('websengoku:chaoting');


?>