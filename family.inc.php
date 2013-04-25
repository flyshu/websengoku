
<?php

$sql = "SELECT * FROM " . DB::table('es_family_member') . " WHERE uid = {$uid} LIMIT 1";
$myfamily_info = DB::fetch_first($sql);


if (!$action) {
    //��ҳ
    $pagesize = 10; // ÿҳ��¼��
    $amount = DB::result_first("SELECT COUNT(fid) FROM " . DB::table('es_family')); // ��ѯ��¼����


    $pagecount = $amount ? (($amount < $pagesize) ? 1 : (($amount % $pagesize) ? ((int)($amount / $pagesize) + 1) : ($amount / $pagesize))) : 0; // ������ҳ��
    $page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $page = $page > $pagecount ? 1 : $page; // ȡ�õ�ǰҳֵ
    $startlimit = ($page - 1) * $pagesize; // ��ѯ��ʼ��ƫ����

    $sql = ("SELECT * FROM " . DB::table('es_family') . " LIMIT {$startlimit}, {$pagesize}"); // ��ѯ��¼��
    $query = DB::query($sql);
    $multipage = multi($amount, $pagesize, $page, $basename2, $pagecount); // ��ʾ��ҳ
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
        showmessage('���Ѿ��������������.');
    } else {
        showmessage('���Ѿ���������������.');
    }
} elseif ($action == 'myfamily') {
    if (!$myfamily_info) {
        showmessage('����δ�����κμ���.', $basename2);
        die();
    }
    $method = $_G['gp_method'];
    if ($method == 'signin') {
        $now = time();
        $today = date('Ymd', $now);
        $signin_day = date('Ymd', $myfamily_info['signin']);
        if ($signin_day == $today) {
            showmessage('�����Ѿ�ǩ������,������ǩ����!');
        } else {
            //ǩ��,���Ҽӷֲ���
            $rt = DB::update('es_family_member', array('signin' => $now, 'exploit' => $exploit + 1), "uid={$uid}");
            if ($rt)
                showmessage('���Ѿ��ɹ�ǩ��!', $basename2 . "&action={$action}");
            else
                showmessage('���ݿ����.');
            //��־
            //family_log($winduid, "'ǩ��'");
        }
    } elseif ($method == 'donate') {
        //���ֲ���,�۳���Ǯ

        //���Ӽ���Ľ�Ǯ
        $donate_money = 1;
        $donate_exploit = 1;
        //$rt = DB::update('es_family', array('f_money' => '5'), "fid={$myfamily_info['fid']}");
        $sql = "UPDATE " . DB::table('es_family') . " SET f_money = f_money+{$donate_money} WHERE fid = {$myfamily_info['fid']} LIMIT 1";
        $rt = DB::query($sql);
        if (!$rt)
            showmessage('���Ӽ����Ǯʱ,���ݿ����.');
        //���ӹ�ѫֵ
        $exploit = $myfamily_info['exploit'] + $donate_exploit;
        $rt = DB::update('es_family_member', array('exploit' => $exploit), "uid={$uid}");
        if (!$rt)
            showmessage('���ӹ�ѫֵʱ,���ݿ����.');
        showmessage('���Ѿ��ɹ���Ϊ���������!', $basename2 . "&action={$action}");
    } elseif ($method == 'exit') {
        $rt = DB::delete('es_family_member', "uid={$uid}", 1);
        if ($rt)
            showmessage('���Ѿ��ɹ��뿪�˼���!', $basename2 . "&action={$action}");
        else
            showmessage('���ݿ����.');
    }

} elseif ($action == 'memberlist') {
    if (!$myfamily_info) {
        showmessage('����δ�����κμ���.', $basename2);
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
        showmessage('����δ�����κμ���.');

    if ($method == 'accept') {
        //���ԭ������
        if ($myfamily_info['mid'])
            showmessage('����δ���֮ǰ������.');
        //����id���
        $sql = "SELECT mid,m_time FROM " . DB::table('es_family_mission') . " WHERE mid = {$mid} LIMIT 1";
        $mission = DB::fetch_first($sql);
        if (!$mission)
            showmessage('����ID����.');

        $finish_time = strtotime('+' . $mission['m_time'] . ' hours');
        $rt = DB::update('es_family_member', array('mid' => $mid, 'mission_finishtime' => $finish_time), "uid={$uid}");
        if ($rt)
            showmessage('�����Ѿ�����.', $basename2 . "&action=mymission");
        else
            showmessage('���ݿ����.');
    } elseif ($method == 'drop') {
        if (!$myfamily_info['mid'])
            showmessage('����ǰû������.');
        $rt = DB::update('es_family_member', array('mid' => 0), "uid={$uid}");
        if ($rt)
            showmessage('�����Ѿ�����.', $basename2 . "&action=missionhall");
        else
            showmessage('���ݿ����.');
    } elseif ($method == 'finish') {
        if (!$myfamily_info['mid'])
            showmessage('����ǰû������.');
        $t = time();
        if ($t < $myfamily_info['mission_finishtime'])
            showmessage('����ǰ�����񻹲������.');

        $rt = DB::update('es_family_member', array('mid' => 0, 'mission_finishtime' => 0), "uid={$uid}");
        if ($rt)
            showmessage('�����Ѿ����.', $basename2 . "&action=missionhall");
        else
            showmessage('���ݿ����.');
    }
} elseif ($action == 'mymission') {
    $mid = $myfamily_info['mid'];
    $sql = "SELECT * FROM " . DB::table('es_family_mission') . " WHERE mid = {$mid} LIMIT 1";
    $mission = DB::fetch_first($sql);
    if (!$mission)
        showmessage('����ǰû������.');
} elseif ($action == 'setting') {
    $sql = "SELECT * FROM " . DB::table('es_family') . " WHERE fid = {$myfamily_info[fid]} LIMIT 1";
    $f_info = DB::fetch_first($sql);
    if ($f_info['f_manage_id'] != $uid) showmessage('��û�й����Ȩ��.');
    $step = $_G['gp_step'];
    if ($step == 2) {
        $f_set = $_G['gp_f_set'];
        foreach ($f_set as $key => $val) {
            $f_set[$key] = dhtmlspecialchars($val) . "\n\r";
        }
        $rt = DB::update("es_family", $f_set, "fid={$myfamily_info[fid]}");
        if ($rt)
            showmessage('�ɹ���������.', $basename2 . "&action=setting");
        else
            showmessage('���ݿ����.');
    }
} else {
    showmessage('δ����Ĳ���.', 'plugin.php?id=websengoku:main');
}

?>