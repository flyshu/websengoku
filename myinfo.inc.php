<?php

if (!$action) {
    $sql = 'SELECT * FROM ' . DB::table('es_member') . ' WHERE uid = ' . $uid;
    $userinfo = DB::fetch_first($sql);
    $u_ability_array = unserialize($userinfo['u_ability']);
    //print_r($u_ability_array);
    //echo serialize($tmp);
    //$newguan['u_ability'] = serialize($tmp);
    //DB::UPDATE("es_member", $newguan, array('uid' => $uid));
} elseif ($action == "shenqing") {


    /*
    //sendpm($toid, $subject, $message, $fromid = '');
    $toid= 1;
    $subject = "�����λ";
    $message = "���������λ:" . urldecode(dhtmlspecialchars($_G["gp_name"]));
    $result = sendpm($toid, $subject, $message, $uid);
    if ($result > 0){
    showmessage('����ɹ�.', 'plugin.php?id=websengoku:main&module=chaoting');
    }else{
    showmessage('����ʧ��,�������.' . $result, 'plugin.php?id=websengoku:main&module=chaoting');
    }
    */

    //DB::insert("es_guan", $guan);
    $g_name = dhtmlspecialchars($_G["gp_name"]);
    $sql = "SELECT shenqing FROM " . DB::table("es_guan") . " WHERE g_name = '" . $g_name . "'";

    $query = DB::fetch_first($sql);
    $shenqing = $query["shenqing"];
    $shenqing_array = explode(',', $shenqing);

    if ($shenqing == "" or !in_array($uid, $shenqing_array)) {
        if ($shenqing == "") {
            $shenqing = $uid;

        } else {
            $shenqing_array[] = $uid;
            $shenqing = implode(',', $shenqing_array);
        }
        //DB::UPDATE("es_guan", $shenqing_array[0], array('g_name' => $g_name));
        $sql = "UPDATE " . DB::table("es_guan") . " SET shenqing = '" . $shenqing . "' WHERE g_name = '" . $g_name . "'";
        //echo $sql;
        DB::query($sql);
        showmessage('����ɹ�.', $basename2);

    } else {
        showmessage('�����ظ�����.', $basename2);
    }

} elseif ($action == "delete") {
    $gid = intval($_G['gp_gid']);
    if ($gid < 0)
        showmessage('���ݴ���.', $basename2);
    DB::delete('es_guan', "gid='$gid'");
    showmessage('����ɾ���ɹ�.', $basename2);
} elseif ($action == "edit") {

    $gid = intval($_G['gp_gid']);
    $step = intval($_G['gp_step']);

    if ($gid < 0)
        showmessage('���ݴ���.', $basename2);


    if ($step == 2) {
        $guan = array();
        $guan["g_name"] = dhtmlspecialchars($_G["gp_name"]);
        $guan["g_sort"] = dhtmlspecialchars($_G["gp_sort"]);
        $guan["g_level"] = dhtmlspecialchars($_G["gp_level"]);
        $guan["g_desc"] = dhtmlspecialchars($_G["gp_desc"]);
        $guan["g_color"] = dhtmlspecialchars($_G["gp_color"]);
        $guan["g_icon"] = dhtmlspecialchars($_G["gp_icon"]);
        $guan["g_limit"] = intval($_G["gp_limit"]);

        $guan["g_owner"] = dhtmlspecialchars($_G["gp_owner"]);


        //print_r($guan);

        //�ж��������ݺϷ���

        //
        $newguan = intval($_G["gp_newguan"]); //�Ƿ��²����λ
        //�������ݻ����²���

        if ($newguan == 1 or $gid == 0) {
            DB::insert("es_guan", $guan);
            showmessage('���ݸ��³ɹ�.', 'plugin.php?id=websengoku:main&module=chaoting');
        } else {
            DB::UPDATE("es_guan", $guan, array('gid' => $gid));
            showmessage('���ݸ��³ɹ�.', 'plugin.php?id=websengoku:main&module=chaoting');
        }
    } else {
        $sql = "SELECT * FROM " . DB::table("es_guan") . " WHERE gid = " . $gid;
        $guan = DB::fetch_first($sql);
    }

} else {
    showmessage('δ����Ĳ���.', 'plugin.php?id=websengoku:main&module=chaoting');
}

?>