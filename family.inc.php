
<?php

$sql = "SELECT * FROM " . DB::table('es_family_member') . " WHERE uid = {$uid} LIMIT 1";
$myfamily_info = DB::fetch_first($sql);


if (!$action) {
    //分页
    $pagesize = 10; // 每页记录数
    $amount = DB::result_first("SELECT COUNT(fid) FROM " . DB::table('es_family')); // 查询记录总数


    $pagecount = $amount ? (($amount < $pagesize) ? 1 : (($amount % $pagesize) ? ((int)($amount / $pagesize) + 1) : ($amount / $pagesize))) : 0; // 计算总页数
    $page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $page = $page > $pagecount ? 1 : $page; // 取得当前页值
    $startlimit = ($page - 1) * $pagesize; // 查询起始的偏移量

    $sql = ("SELECT * FROM " . DB::table('es_family') . " LIMIT {$startlimit}, {$pagesize}"); // 查询记录集
    $query = DB::query($sql);
    $multipage = multi($amount, $pagesize, $page, $basename2, $pagecount); // 显示分页
   // echo $multipage;

    while ($rt = DB::fetch($query)) {
        $f_info[] = $rt;
    }
} elseif ($action == 'join') {
    $fid = $_G["gp_fid"];

    if (!$myfamily_info) {
        $sql = "INSERT INTO " . DB::table('es_family_member') . " SET uid = {$uid}, fid = {$fid},jointime='" . time() . "'";
        DB::query($sql);
    } elseif ($myfamily_info['fid'] == $fid) {
        showmessage('您已经加入这个家族了.');
    } else {
        showmessage('您已经加入其它家族了.');
    }
} elseif ($action == 'myfamily') {
    if (!$myfamily_info) {
        showmessage('您尚未加入任何家族.', $basename2);
        die();
    }
    $method = $_G['gp_method'];
    if ($method == 'signin') {
        $now = time();
        $today = date('Ymd', $now);
        $signin_day = date('Ymd', $myfamily_info['signin']);
        if ($signin_day == $today) {
            showmessage('今天已经签到过了,不能在签到了!');
        } else {
            //签到,并且加分操作
            $rt = DB::update('es_family_member', array('signin' => $now, 'exploit' => $exploit + 1), "uid={$uid}");
            if ($rt)
                showmessage('您已经成功签到!', $basename2 . "&action={$action}");
            else
                showmessage('数据库错误.');
            //日志
            //family_log($winduid, "'签到'");
        }
    } elseif ($method == 'donate') {
        //积分操作,扣除金钱

        //增加家族的金钱
        $donate_money = 1;
        $donate_exploit = 1;
        //$rt = DB::update('es_family', array('f_money' => '5'), "fid={$myfamily_info['fid']}");
        $sql = "UPDATE " . DB::table('es_family') . " SET f_money = f_money+{$donate_money} WHERE fid = {$myfamily_info['fid']} LIMIT 1";
        $rt = DB::query($sql);
        if (!$rt)
            showmessage('增加家族金钱时,数据库错误.');
        //增加功勋值
        $exploit = $myfamily_info['exploit'] + $donate_exploit;
        $rt = DB::update('es_family_member', array('exploit' => $exploit), "uid={$uid}");
        if (!$rt)
            showmessage('增加功勋值时,数据库错误.');
        showmessage('您已经成功的为家族捐赠了!', $basename2 . "&action={$action}");
    } elseif ($method == 'exit') {
        $rt = DB::delete('es_family_member', "uid={$uid}", 1);
        if ($rt)
            showmessage('您已经成功离开了家族!', $basename2 . "&action={$action}");
        else
            showmessage('数据库错误.');
    }

} elseif ($action == 'memberlist') {
    if (!$myfamily_info) {
        showmessage('您尚未加入任何家族.', $basename2);
        die();
    }
    $fid = $myfamily_info['fid'];
    $sql = "SELECT * FROM " . DB::table('es_family_member') . " WHERE fid = {$fid}";
    $query = DB::query($sql);
    while ($rt = DB::fetch($query)) {
        $member[] = $rt;
    }

} elseif ($action == 'missionhall') {
    $sql = "SELECT * FROM " . DB::table('es_family_mission');
    $query = DB::query($sql);
    while ($rt = DB::fetch($query)) {
        $mission[] = $rt;
    }
} elseif ($action == 'mission') {
    $method = $_G['gp_method'];
    $mid = (int)$_G['gp_mid'];
    if (!$myfamily_info)
        showmessage('您尚未加入任何家族.');

    if ($method == 'accept') {
        //检查原有任务
        if ($myfamily_info['mid'])
            showmessage('您尚未完成之前的任务.');
        //任务id检测
        $sql = "SELECT mid,m_time FROM " . DB::table('es_family_mission') . " WHERE mid = {$mid} LIMIT 1";
        $mission = DB::fetch_first($sql);
        if (!$mission)
            showmessage('任务ID错误.');

        $finish_time = strtotime('+' . $mission['m_time'] . ' hours');
        $rt = DB::update('es_family_member', array('mid' => $mid, 'mission_finishtime' => $finish_time), "uid={$uid}");
        if ($rt)
            showmessage('任务已经接受.', $basename2 . "&action=mymission");
        else
            showmessage('数据库错误.');
    } elseif ($method == 'drop') {
        if (!$myfamily_info['mid'])
            showmessage('您当前没有任务.');
        $rt = DB::update('es_family_member', array('mid' => 0), "uid={$uid}");
        if ($rt)
            showmessage('任务已经放弃.', $basename2 . "&action=missionhall");
        else
            showmessage('数据库错误.');
    } elseif ($method == 'finish') {
        if (!$myfamily_info['mid'])
            showmessage('您当前没有任务.');
        $t = time();
        if ($t < $myfamily_info['mission_finishtime'])
            showmessage('您当前的任务还不能完成.');

        $rt = DB::update('es_family_member', array('mid' => 0, 'mission_finishtime' => 0), "uid={$uid}");
        if ($rt)
            showmessage('任务已经完成.', $basename2 . "&action=missionhall");
        else
            showmessage('数据库错误.');
    }
} elseif ($action == 'mymission') {
    $mid = $myfamily_info['mid'];
    $sql = "SELECT * FROM " . DB::table('es_family_mission') . " WHERE mid = {$mid} LIMIT 1";
    $mission = DB::fetch_first($sql);
    if (!$mission)
        showmessage('您当前没有任务.');
} elseif ($action == 'setting') {
    $sql = "SELECT * FROM " . DB::table('es_family') . " WHERE fid = {$myfamily_info[fid]} LIMIT 1";
    $f_info = DB::fetch_first($sql);
    if ($f_info['f_manage_id'] != $uid) showmessage('您没有管理的权限.');
    $step = $_G['gp_step'];
    if ($step == 2) {
        $f_set = $_G['gp_f_set'];
        foreach ($f_set as $key => $val) {
            $f_set[$key] = dhtmlspecialchars($val) . "\n\r";
        }
        $rt = DB::update("es_family", $f_set, "fid={$myfamily_info[fid]}");
        if ($rt)
            showmessage('成功更新数据.', $basename2 . "&action=setting");
        else
            showmessage('数据库错误.');
    }
} else {
    showmessage('未定义的操作.', 'plugin.php?id=websengoku:main');
}

?>