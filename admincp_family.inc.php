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

    cpmsg('�ɹ�����', 'action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_family', 'succeed');
    //DB::update(DB::table('es_family'));
} else {
    //��ҳ
    $pagesize = 10; // ÿҳ��¼��
    $amount = DB::result_first("SELECT COUNT(fid) FROM " . DB::table('es_family')); // ��ѯ��¼����


    $pagecount = $amount ? (($amount < $pagesize) ? 1 : (($amount % $pagesize) ? ((int)($amount / $pagesize) + 1) : ($amount / $pagesize))) : 0; // ������ҳ��
    $page = !empty($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $page = $page > $pagecount ? 1 : $page; // ȡ�õ�ǰҳֵ
    $startlimit = ($page - 1) * $pagesize; // ��ѯ��ʼ��ƫ����

    $sql = ("SELECT * FROM " . DB::table('es_family') . " LIMIT {$startlimit}, {$pagesize}"); // ��ѯ��¼��
    $multipage = multi($amount, $pagesize, $page, '?action=plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_family', $pagecount); // ��ʾ��ҳ
    echo $multipage;

    $query = DB::query($sql);
    while ($rt = DB::fetch($query)) {

        $f_info[] = $rt;
    }
    //print_r($f_info);
    showformheader('plugins&operation=config&do=' . $pluginid . '&identifier=websengoku&pmod=admincp_family', 'repeatsubmit', 'family', 'post');
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

    $edit_arr = $f_info;
    //print_r($edit_arr);
    showtablerow('', array('class="td25"', 'class="td28"'), array(
        '���',
        '����',
        '�Ҷ�',
        '��ͷ����',
        '����',
        '����',
        'ͼ��',
        '��Ǯ'));

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
    showsubmit('submit', '�ύ'); //�����ύ��ť
    showformfooter(); //������β
}

//echo multi($count, $ppp, $page, ADMINSCRIPT."?action=plugins&operation=config&do=$pluginid&identifier=myrepeats&pmod=admincp$extra");


?>