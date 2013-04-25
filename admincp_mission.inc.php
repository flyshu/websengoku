<?php

/**
 *      [Discuz!] (C)2001-2099 Comsenz Inc.
 *      This is NOT a freeware, use is subject to license terms
 *
 *      $Id: admincp.inc.php 29364 2012-04-09 02:51:41Z monkey $
 */

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

$action = trim($_G['gp_action']);

$step = $_G['gp_step'];
if ($step == 2) {
    $mission = $_G['gp_mission'];
    $update_num = count($mission['mid']);
    //print_r($family);
    for ($i = 0; $i < $update_num; $i++) {
        foreach ($mission as $key => $val) {
            $data[$key] = $val[$i];
        }
        //print_r($data);
        if ($data['mid'] == '') {
            if ($data['m_name'] != '') {
                DB::insert("es_family_mission", $data);
            }

        } else {
            DB::update("es_family_mission", $data, "mid={$data['mid']}");
        }

    }

    cpmsg('成功更新', 'action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_mission', 'succeed');
    //DB::update(DB::table('es_family'));
} else {
    //分页
    $pagesize = 10; // 每页记录数
    $amount = DB::result_first("SELECT COUNT(mid) FROM " . DB::table('es_family_mission')); // 查询记录总数


    $pagecount = $amount ? (($amount < $pagesize) ? 1 : (($amount % $pagesize) ? ((int)($amount / $pagesize) + 1) : ($amount / $pagesize))) : 0; // 计算总页数
    $page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $page = $page > $pagecount ? 1 : $page; // 取得当前页值
    $startlimit = ($page - 1) * $pagesize; // 查询起始的偏移量

    $sql = ("SELECT * FROM " . DB::table('es_family_mission') . " LIMIT {$startlimit}, {$pagesize}"); // 查询记录集
    $query = DB::query($sql);
    $multipage = multi($amount, $pagesize, $page, '?action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_mission', $pagecount); // 显示分页
    echo $multipage;

    while ($rt = DB::fetch($query)) {
        $miss_info[] = $rt;
    }
    //print_r($f_info);
    showformheader('plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_mission', 'repeatsubmit', 'npc', 'post');
    showsetting('隐藏值', 'step', '2', 'text', '', 1);
    showtableheader();
    
    showtablerow('', array('class="td25"', 'class="td28"'), array(
        '编号',
        '任务名称',
        '任务介绍',
        '消耗体力',
        '任务奖励',
        '消耗时间',
        '发布人',
        ));
    foreach ($miss_info as $key => $val) {
        $edit_arr = array();
        $edit_arr[] = "<input type='text' name='mission[mid][]' value= '{$val['mid']}' readonly='readonly' />";
        $edit_arr[] = "<input type='text' name='mission[m_name][]' value= '{$val['m_name']}' />";
        $edit_arr[] = "<input type='text' name='mission[m_intro][]' value= '{$val['m_intro']}' />";
        $edit_arr[] = "<input type='text' name='mission[m_power][]' value= '{$val['m_power']}' />";
        $edit_arr[] = "<input type='text' name='mission[m_award][]' value= '{$val['m_award']}' />";
        $edit_arr[] = "<input type='text' name='mission[m_time][]' value= '{$val['m_time']}' />";
        $edit_arr[] = "<input type='text' name='mission[m_host][]' value= '{$val['m_host']}' />";
        showtablerow('', array('class="td25"', 'class="td28"'), $edit_arr);
    }
        $edit_arr = array();
        $edit_arr[] = "<input type='text' name='mission[mid][]' value= '' readonly='readonly' />";
        $edit_arr[] = "<input type='text' name='mission[m_name][]' value= '' />";
        $edit_arr[] = "<input type='text' name='mission[m_intro][]' value= '' />";
        $edit_arr[] = "<input type='text' name='mission[m_power][]' value= '' />";
        $edit_arr[] = "<input type='text' name='mission[m_award][]' value= '' />";
        $edit_arr[] = "<input type='text' name='mission[m_time][]' value= '' />";
        $edit_arr[] = "<input type='text' name='mission[m_host][]' value= '' />";
        showtablerow('', array('class="td25"', 'class="td28"'), $edit_arr);
    showtablefooter();
    showsubmit('submit', '提交');
    //创建提交按钮
    showformfooter(); //创建表单尾
}

//echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=myrepeats&pmod=admincp$extra");


?>