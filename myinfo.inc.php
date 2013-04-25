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
    $subject = "申请官位";
    $message = "向您申请官位:" . urldecode(dhtmlspecialchars($_G["gp_name"]));
    $result = sendpm($toid, $subject, $message, $uid);
    if ($result > 0){
    showmessage('申请成功.', 'plugin.php?id=websengoku:main&module=chaoting');
    }else{
    showmessage('申请失败,错误代码.' . $result, 'plugin.php?id=websengoku:main&module=chaoting');
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
        showmessage('申请成功.', $basename2);

    } else {
        showmessage('不能重复申请.', $basename2);
    }

} elseif ($action == "delete") {
    $gid = intval($_G['gp_gid']);
    if ($gid < 0)
        showmessage('数据错误.', $basename2);
    DB::delete('es_guan', "gid='$gid'");
    showmessage('数据删除成功.', $basename2);
} elseif ($action == "edit") {

    $gid = intval($_G['gp_gid']);
    $step = intval($_G['gp_step']);

    if ($gid < 0)
        showmessage('数据错误.', $basename2);


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

        //判断输入数据合法性

        //
        $newguan = intval($_G["gp_newguan"]); //是否新插入官位
        //更新数据或者新插入

        if ($newguan == 1 or $gid == 0) {
            DB::insert("es_guan", $guan);
            showmessage('数据更新成功.', 'plugin.php?id=websengoku:main&module=chaoting');
        } else {
            DB::UPDATE("es_guan", $guan, array('gid' => $gid));
            showmessage('数据更新成功.', 'plugin.php?id=websengoku:main&module=chaoting');
        }
    } else {
        $sql = "SELECT * FROM " . DB::table("es_guan") . " WHERE gid = " . $gid;
        $guan = DB::fetch_first($sql);
    }

} else {
    showmessage('未定义的操作.', 'plugin.php?id=websengoku:main&module=chaoting');
}

?>