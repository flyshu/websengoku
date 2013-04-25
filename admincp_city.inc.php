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
    $city = $_G['gp_city'];
    $update_num = count($city['fid']);

    for ($i = 0; $i < $update_num; $i++) {
        foreach ($city as $key => $val) {
            $data[$key] = $val[$i];
        }
        if ($data['cid'] == '') {
            if ($data['c_name'] != '') {
                DB::insert("es_city", $data);
            }

        } else {
            DB::update("es_city", $data, "cid={$data['cid']}");
        }
    }

    cpmsg('成功更新', 'action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_city', 'succeed');

} else {
    //分页
    $pagesize = 10; // 每页记录数
    $amount = DB::result_first("SELECT COUNT(cid) FROM " . DB::table('es_city')); // 查询记录总数


    $pagecount = $amount ? (($amount < $pagesize) ? 1 : (($amount % $pagesize) ? ((int)($amount / $pagesize) + 1) : ($amount / $pagesize))) : 0; // 计算总页数
    $page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $page = $page > $pagecount ? 1 : $page; // 取得当前页值
    $startlimit = ($page - 1) * $pagesize; // 查询起始的偏移量

    $sql = ("SELECT * FROM " . DB::table('es_city') . " LIMIT {$startlimit}, {$pagesize}"); // 查询记录集
    $query = DB::query($sql);
    $multipage = multi($amount, $pagesize, $page, '?action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_city', $pagecount); // 显示分页
    echo $multipage;

    while ($rt = DB::fetch($query)) {
        $city_info[] = $rt;
    }
    //print_r($f_info);
    showformheader('plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_city', 'repeatsubmit', 'family', 'post');
    showsetting('隐藏值', 'step', '2', 'text', '', 1);
    showtableheader();
    //$edit_arr = $city_info;
    //print_r($edit_arr);
    showtablerow('', array('class="td25"', 'class="td28"'), array(
        '编号',
        '名称',
        '类型',
        '坐标X',
        '坐标Y',
        '城主',
        'npc管理',
        '所在国'));

    foreach ($city_info as $key => $val) {
        $edit_arr = array();
        $edit_arr[] = "<input type='text' name='city[cid][]' value= '{$val['cid']}' readonly='readonly' />";
        $edit_arr[] = "<input type='text' name='city[c_name][]' value= '{$val['c_name']}' />";
        $edit_arr[] = "<input type='text' name='city[c_type][]' value= '{$val['c_type']}' />";
        $edit_arr[] = "<input type='text' name='city[cx][]' value= '{$val['cx']}' />";
        $edit_arr[] = "<input type='text' name='city[cy][]' value= '{$val['cy']}' />";
        $edit_arr[] = "<input type='text' name='city[c_host_id][]' value= '{$val['c_host_id']}' />";
        $edit_arr[] = "<input type='text' name='city[c_isnpc][]' value= '{$val['c_isnpc']}' />";
        $edit_arr[] = "<input type='text' name='city[c_suozai][]' value= '{$val['c_suozai']}' />";
        showtablerow('', array('class="td25"', 'class="td28"'), $edit_arr);
        //print_r($edit_arr);
    }
    $edit_arr = array();
    $edit_arr[] = "<input type='text' name='city[cid][]' value= '' readonly='readonly' />";
    $edit_arr[] = "<input type='text' name='city[c_name][]' value= '' />";
    $edit_arr[] = "<input type='text' name='city[c_type][]' value= '' />";
    $edit_arr[] = "<input type='text' name='city[cx][]' value= '' />";
    $edit_arr[] = "<input type='text' name='city[cy][]' value= '' />";
    $edit_arr[] = "<input type='text' name='city[c_host_id][]' value= '' />";
    $edit_arr[] = "<input type='text' name='city[c_isnpc][]' value= '' />";
    $edit_arr[] = "<input type='text' name='city[c_suozai][]' value= '' />";
    showtablerow('', array('class="td25"', 'class="td28"'), $edit_arr);
    showtablefooter();
    showsubmit('submit', '提交'); //创建提交按钮
    showformfooter(); //创建表单尾

}

//echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=myrepeats&pmod=admincp$extra");


?>