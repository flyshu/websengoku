<?php

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
if (!$_G['uid']) {
    showmessage('not_loggedin', '', '', array('login' => 1));
}

$uid = intval($_G["uid"]);

//����û��Ƿ�ע��

$sql = "SELECT * FROM " . DB::table("es_member") . " WHERE uid= " . $uid;

$user_info = DB::fetch_first($sql);
if (!empty($user_info)) {
    showmessage('���Ѿ�ע���˱���Ϸ,��Ҫ���¿�ʼ.', 'plugin.php?id=websengoku:main');
}
//print_r($global_cfg['admincp']);

$step = intval($_G['gp_step']);
$zhiye = intval($_G['gp_zhiye']);
$birth_city = intval($_G['gp_birth_city']);
$main_ability = $_G['gp_main_ability'];
if (!$step) { //��ʾ�û�Э��

} elseif ($step == '2') {
    //$main_ability_arr = expldeo(',',$global_cfg['admincp']);
    //$zhiye_arr = explode(',', $global_cfg['admincp']['zhiye']);
    $sql = 'SELECT * FROM ' . DB::table('es_zhiye');
    $query = DB::query($sql);
    while ($rt = DB::fetch($query)) {
        $zhiye_arr[] = $rt;
    }
    //print_r($zhiye_arr);
    //echo $sql;
} elseif ($step == '3') { //������
    $sql = 'SELECT * FROM ' . DB::table('es_city') . ' LIMIT 0,10';
    $query = DB::query($sql);
    while ($rt = DB::fetch($query)) {
        $city_arr[] = $rt;
    }
    //print_r($city_arr);
} elseif ($step == '4') { //����

    $main_ability_arr['name'] = explode(',', $global_cfg['admincp']['main_ability']);
    $sql = "SELECT z_main_ability FROM " . DB::table('es_zhiye') . " WHERE zid = {$zhiye}";
    $z_main_ability = DB::fetch_first($sql);
    $main_ability_arr['num'] = explode(',', $z_main_ability['z_main_ability']);


} elseif ($step == '5') { //�ύ
    //�ж����ݺϷ���
    $url = 'plugin.php?id=websengoku:main&module=reg';
    $sql = 'SELECT zid FROM ' . DB::table('es_zhiye') . " WHERE `zid` = {$zhiye}";
    $zhiye_num = DB::result_first($sql);

    $sql = 'SELECT count(`cid`) FROM ' . DB::table('es_city') . " WHERE `cid` = {$birth_city}";
    $city_num = DB::result_first($sql);

    //$zhiye_num-=1;
    if ($zhiye_num == 0) {
        showmessage($zhiye . 'ְҵ���ݴ���,������ѡ��.', $url);
    }
    if ($city_num == 0) {
        showmessage($birth_city . '�������ݴ���,������ѡ��.', $url);
    }

    foreach ($main_ability as $key => $val) {
        $val = intval($val);
        if ($val < 0 and $val > 100) {
            showmessage('���ݴ���,������ѡ��.', $url);
            break;
        }
    }
    //$main_ability_str = implode(',',$main_ability);
    $u_ability['main_abi'] = $main_ability;
    
    $time = time();
    $user_info = array('uid' => $uid, 'u_birthcity'=>$birth_city,'u_ability' =>serialize($u_ability),'u_zhiye'=>$zhiye);
    $index = DB::insert("es_member", $user_info);
    /*
    DB::insert('common_credit_log', array(
        'uid' => $uid,
        'operation' => 'ECU',
        'relatedid' => $uid,
        'dateline' => time(),
        'extcredits' . $credit => $amount));*/

    showmessage('ע��ɹ�.', 'plugin.php?id=websengoku:main');


}

?>