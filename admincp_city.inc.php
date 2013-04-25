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
     * ����һ�����߶������ݼ�¼
     *
     * @param string $table ԭʼ����
     * @param array $data ����field-value
     * @param string $condition ������䣬����ҪдWHERE
     * @param boolean $unbuffered Ѹ�ٷ��أ�
     * @param boolan $low_priority �ӳٸ��£�
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

    cpmsg('�ɹ�����', 'action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_city', 'succeed');

} else {
    //��ҳ
    $pagesize = 10; // ÿҳ��¼��
    $amount = DB::result_first("SELECT COUNT(cid) FROM " . DB::table('es_city')); // ��ѯ��¼����


    $pagecount = $amount ? (($amount < $pagesize) ? 1 : (($amount % $pagesize) ? ((int)($amount / $pagesize) + 1) : ($amount / $pagesize))) : 0; // ������ҳ��
    $page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $page = $page > $pagecount ? 1 : $page; // ȡ�õ�ǰҳֵ
    $startlimit = ($page - 1) * $pagesize; // ��ѯ��ʼ��ƫ����

    $sql = ("SELECT * FROM " . DB::table('es_city') . " LIMIT {$startlimit}, {$pagesize}"); // ��ѯ��¼��
    $query = DB::query($sql);
    $multipage = multi($amount, $pagesize, $page, '?action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_city', $pagecount); // ��ʾ��ҳ
    echo $multipage;

    while ($rt = DB::fetch($query)) {
        $city_info[] = $rt;
    }
    //print_r($f_info);
    showformheader('plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_city', 'repeatsubmit', 'family', 'post');
    showsetting('����ֵ', 'step', '2', 'text', '', 1);
    showtableheader();
    //$edit_arr = $city_info;
    //print_r($edit_arr);
    showtablerow('', array('class="td25"', 'class="td28"'), array(
        '���',
        '����',
        '����',
        '����X',
        '����Y',
        '����',
        'npc����',
        '���ڹ�'));

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
    showsubmit('submit', '�ύ'); //�����ύ��ť
    showformfooter(); //������β

}

//echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=myrepeats&pmod=admincp$extra");


?>