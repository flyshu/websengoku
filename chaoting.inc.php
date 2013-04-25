<?php



if (!$action) {
    /*
    if ($action == "shenqing") {
    $gid = intval($_G['gp_gid']);
    if ($gid <= 0) {
    showmessage('申请的官位不存在' . $action, $thisurl);
    } else {
    //$sql = "UPDATE es_guan SET ";
    }
    } 
    */
    //分页
    $pagesize = 10; // 每页记录数
    $sql = "SELECT COUNT(gid) FROM " . DB::table("es_guan");
    $amount = DB::result_first($sql); // 查询记录总数


    $pagecount = $amount ? (($amount < $pagesize) ? 1 : (($amount % $pagesize) ? ((int)($amount / $pagesize) + 1) : ($amount / $pagesize))) : 0; // 计算总页数
    $page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $page = $page > $pagecount ? 1 : $page; // 取得当前页值
    $startlimit = ($page - 1) * $pagesize; // 查询起始的偏移量

    $sql = "SELECT * FROM " . DB::table("es_guan") . " ORDER BY g_sort ASC " . " LIMIT {$startlimit}, {$pagesize}"; // 查询记录集
    $query = DB::query($sql);
    $multipage = multi($amount, $pagesize, $page, $basename2, $pagecount); // 显示分页


    //echo $sql;
    $query = DB::query($sql);
    while ($guan_info = DB::fetch($query)) {
        $guan_info_array[] = $guan_info;
    }
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
    $sql = "SELECT shenqing FROM " . DB::table("es_guan") . " WHERE g_name = '" . $g_name .
        "'";

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
        $sql = "UPDATE " . DB::table("es_guan") . " SET shenqing = '" . $shenqing .
            "' WHERE g_name = '" . $g_name . "'";
        //echo $sql;
        DB::query($sql);
        showmessage('申请成功.', $basename2);

    } else {
        showmessage('不能重复申请.', $basename2);
    }

} elseif ($action == "delete") {
    $gid = intval($_G['gp_gid']);
    if ($gid < 0) showmessage('数据错误.', $basename2); 
    DB::delete('es_guan', "gid='$gid'");
    showmessage('数据删除成功.', $basename2);
} elseif ($action == "edit") {

    $gid = intval($_G['gp_gid']);
    $step = intval($_G['gp_step']);

    if ($gid < 0) showmessage('数据错误.', $basename2); 



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