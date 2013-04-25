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

    cpmsg('�ɹ�����', 'action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_npc', 'succeed');
    //DB::update(DB::table('es_family'));
} else {
    //��ҳ
    $pagesize = 10; // ÿҳ��¼��
    $amount = DB::result_first("SELECT COUNT(nid) FROM " . DB::table('es_npc')); // ��ѯ��¼����


    $pagecount = $amount ? (($amount < $pagesize) ? 1 : (($amount % $pagesize) ? ((int)($amount / $pagesize) + 1) : ($amount / $pagesize))) : 0; // ������ҳ��
    $page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $page = $page > $pagecount ? 1 : $page; // ȡ�õ�ǰҳֵ
    $startlimit = ($page - 1) * $pagesize; // ��ѯ��ʼ��ƫ����

    $sql = ("SELECT * FROM " . DB::table('es_npc') . " LIMIT {$startlimit}, {$pagesize}"); // ��ѯ��¼��
    $query = DB::query($sql);
    $multipage = multi($amount, $pagesize, $page, '?action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_npc', $pagecount); // ��ʾ��ҳ
    echo $multipage;

    while ($rt = DB::fetch($query)
        ) {

            $npc_info[] = $rt;
        }
        //print_r($f_info);
        showformheader('plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_npc', 'repeatsubmit', 'npc', 'post');
    showsetting('����ֵ', 'step', '2', 'text', '', 1);
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
        '���',
        '����',
        'ְҵ',
        '״̬',
        '����',
        '���',
        '����',
        'ͼ��',
        '�۸�'));

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
    showsubmit('submit', '�ύ'); //�����ύ��ť
    showformfooter(); //������β
}

//echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=myrepeats&pmod=admincp$extra");


?>