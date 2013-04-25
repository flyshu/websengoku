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
    /**
     * 更新一条或者多条数据记录
     *
     * @param string $table 原始表名
     * @param array $data 数据field-value
     * @param string $condition 条件语句，不需要写WHERE
     * @param boolean $unbuffered 迅速返回？
     * @param boolan $low_priority 延迟更新？
     * @return result
     *
     * function update($table, $data, $condition, $unbuffered = false, $low_priority = false) {
     * ......
     * }*/
    $npc = $_G['gp_npc'];
    $update_num = count($npc['nid']);
    //print_r($family);
    for ($i = 0; $i < $update_num; $i++) {
        foreach ($npc as $key => $val) {
            $data[$key] = $val[$i];
        }
        //print_r($data);
        if ($data['nid'] == '') {
            if ($data['npc_name'] != '') {
                DB::insert("es_npc", $data);
            }

        } else {
            DB::update("es_npc", $data, "nid={$data['nid']}");
        }

    }

    cpmsg('成功更新', 'action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_npc', 'succeed');
    //DB::update(DB::table('es_family'));
} else {
    //分页
    $pagesize = 10; // 每页记录数
    $amount = DB::result_first("SELECT COUNT(nid) FROM " . DB::table('es_npc')); // 查询记录总数


    $pagecount = $amount ? (($amount < $pagesize) ? 1 : (($amount % $pagesize) ? ((int)($amount / $pagesize) + 1) : ($amount / $pagesize))) : 0; // 计算总页数
    $page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $page = $page > $pagecount ? 1 : $page; // 取得当前页值
    $startlimit = ($page - 1) * $pagesize; // 查询起始的偏移量

    $sql = ("SELECT * FROM " . DB::table('es_npc') . " LIMIT {$startlimit}, {$pagesize}"); // 查询记录集
    $query = DB::query($sql);
    $multipage = multi($amount, $pagesize, $page, '?action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_npc', $pagecount); // 显示分页
    echo $multipage;

    while ($rt = DB::fetch($query)
        ) {

            $npc_info[] = $rt;
        }
        //print_r($f_info);
        showformheader('plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_npc', 'repeatsubmit', 'npc', 'post');
    showsetting('隐藏值', 'step', '2', 'text', '', 1);
    showtableheader();
    /*
    showformheader('plugins&operation=config&do=' . $pluginid . '&identifier=gongqinyuan&pmod=admincp',
    'repeatsubmit');
    //showsubmit('repeatsubmit', $Plang['search'], $lang['username'] .
    ': <input name="srchusername" value="' . htmlspecialchars($_GET['srchusername']) .
    '" class="txt" />&nbsp;&nbsp;' . $Plang['repeat'] . ': <input name="srchrepeat" value="' .
    htmlspecialchars($_GET['srchrepeat']) . '" class="txt" />', $searchtext);
    //showformfooter();
    */

    $edit_arr = $npc_info;
    //print_r($edit_arr);
    showtablerow('', array('class="td25"', 'class="td28"'), array(
        '编号',
        '姓名',
        '职业',
        '状态',
        '介绍',
        '身份',
        '能力',
        '图标',
        '价格'));

    foreach ($edit_arr as $key => $val) {
        $val['nid'] = "<input type='text' name='npc[nid][]' value= '{$val['nid']}' readonly='readonly' />";
        $val['npc_name'] = "<input type='text' name='npc[npc_name][]' value= '{$val['npc_name']}' />";
        $val['npc_zhiye'] = "<input type='text' name='npc[npc_zhiye][]' value= '{$val['npc_zhiye']}' />";
        $val['npc_status'] = "<input type='text' name='npc[npc_status][]' value= '{$val['npc_status']}' />";
        $val['npc_desc'] = "<input type='text' name='npc[npc_desc][]' value= '{$val['npc_desc']}' />";
        $val['npc_shenfen'] = "<input type='text' name='npc[npc_shenfen][]' value= '{$val['npc_shenfen']}' />";
        $val['npc_main'] = "<input type='text' name='npc[npc_main][]' value= '{$val['npc_main']}' />";
        $val['npc_icon'] = "<input type='text' name='npc[npc_icon][]' value= '{$val['npc_icon']}' />";
        $val['npc_price'] = "<input type='text' name='npc[npc_price][]' value= '{$val['npc_price']}' />";
        showtablerow('', array('class="td25"', 'class="td28"'), $val);
    }
    $newform['nid'] = "<input type='text' name='npc[nid][]' value= '' readonly='readonly' />";
    $newform['npc_name'] = "<input type='text' name='npc[npc_name][]' value= '' />";
    $newform['npc_zhiye'] = "<input type='text' name='npc[npc_zhiye][]' value= '' />";
    $newform['npc_status'] = "<input type='text' name='npc[npc_status][]' value= '' />";
    $newform['npc_desc'] = "<input type='text' name='npc[npc_desc][]' value= '' />";
    $newform['npc_shenfen'] = "<input type='text' name='npc[npc_shenfen][]' value= '' />";
    $newform['npc_main'] = "<input type='text' name='npc[npc_main][]' value= '' />";
    $newform['npc_icon'] = "<input type='text' name='npc[npc_icon][]' value= '' />";
    $newform['npc_price'] = "<input type='text' name='npc[npc_price][]' value= '' />";
    showtablerow('', array('class="td25"', 'class="td28"'), $newform);
    showtablefooter();
    showsubmit('submit', '提交'); //创建提交按钮
    showformfooter(); //创建表单尾
}

//echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=myrepeats&pmod=admincp$extra");


?>