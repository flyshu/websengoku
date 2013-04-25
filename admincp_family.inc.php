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
    $family = $_G['gp_family'];
    $update_num = count($family['fid']);
    //print_r($family);
    for ($i = 0; $i < $update_num; $i++) {
        foreach ($family as $key => $val) {
            $data[$key] = $val[$i];
        }
        if ($data['fid'] == '') {
            if ($data['f_name'] != '') {
                DB::insert("es_family", $data);
            }
        } else {
            DB::update("es_family", $data, "fid={$data['fid']}");
        }

    }

    cpmsg('成功更新', 'action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_family', 'succeed');
    //DB::update(DB::table('es_family'));
} else {
    //分页
    $pagesize = 10; // 每页记录数
    $amount = DB::result_first("SELECT COUNT(fid) FROM " . DB::table('es_family')); // 查询记录总数


    $pagecount = $amount ? (($amount < $pagesize) ? 1 : (($amount % $pagesize) ? ((int)($amount / $pagesize) + 1) : ($amount / $pagesize))) : 0; // 计算总页数
    $page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $page = $page > $pagecount ? 1 : $page; // 取得当前页值
    $startlimit = ($page - 1) * $pagesize; // 查询起始的偏移量

    $sql = ("SELECT * FROM " . DB::table('es_family') . " LIMIT {$startlimit}, {$pagesize}"); // 查询记录集
    $multipage = multi($amount, $pagesize, $page, '?action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_family', $pagecount); // 显示分页
    echo $multipage;

    $query = DB::query($sql);
    while ($rt = DB::fetch($query)) {

        $f_info[] = $rt;
    }
    //print_r($f_info);
    showformheader('plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_family', 'repeatsubmit', 'family', 'post');
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

    $edit_arr = $f_info;
    //print_r($edit_arr);
    showtablerow('', array('class="td25"', 'class="td28"'), array(
        '编号',
        '名称',
        '家督',
        '笔头家老',
        '介绍',
        '公告',
        '图标',
        '金钱'));

    foreach ($edit_arr as $key => $val) {
        $val['fid'] = "<input type='text' name='family[fid][]' value= '{$val['fid']}' readonly='readonly' />";
        $val['f_name'] = "<input type='text' name='family[f_name][]' value= '{$val['f_name']}' />";
        $val['f_host_id'] = "<input type='text' name='family[f_host_id][]' value= '{$val['f_host_id']}' />";
        $val['f_manage_id'] = "<input type='text' name='family[f_manage_id][]' value= '{$val['f_manage_id']}' />";
        $val['f_intro'] = "<input type='text' name='family[f_intro][]' value= '{$val['f_intro']}' />";
        $val['f_notice'] = "<input type='text' name='family[f_notice][]' value= '{$val['f_notice']}' />";
        $val['f_icon'] = "<input type='text' name='family[f_icon][]' value= '{$val['f_icon']}' />";
        $val['f_money'] = "<input type='text' name='family[f_money][]' value= '{$val['f_money']}' />";
        showtablerow('', array('class="td25"', 'class="td28"'), $val);
    }
    $newform['fid'] = "<input type='text' name='family[fid][]' value= '' readonly='readonly' />";
    $newform['f_name'] = "<input type='text' name='family[f_name][]' value= '' />";
    $newform['f_host_id'] = "<input type='text' name='family[f_host_id][]' value= '' />";
    $newform['f_manage_id'] = "<input type='text' name='family[f_manage_id][]' value= '' />";
    $newform['f_intro'] = "<input type='text' name='family[f_intro][]' value= '' />";
    $newform['f_notice'] = "<input type='text' name='family[f_notice][]' value= '' />";
    $newform['f_icon'] = "<input type='text' name='family[f_icon][]' value= '' />";
    $newform['f_money'] = "<input type='text' name='family[f_money][]' value= '' />";
    showtablerow('', array('class="td25"', 'class="td28"'), $newform);
    showtablefooter();
    showsubmit('submit', '提交'); //创建提交按钮
    showformfooter(); //创建表单尾
}

//echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=myrepeats&pmod=admincp$extra");


?>