<?php

if (!$action) {
    $sql = 'SELECT u_power,u_ability FROM ' . DB::table('es_member') . ' WHERE uid = ' . $uid;
    $info = DB::fetch_first($sql);
    $u_ability = unserialize($info['u_ability']);
    $u_power = $info['u_power'];
    
    //print_r($global_cfg);

} elseif ($action == "xiulian") {


    $xl = intval($_G['gp_xiulian']);
    $abi_num = count(explode(',',$global_admincp['main_ability']));
    if ($xl < 0 || $xl > $abi_num-1)
        showmessage('���ݴ���.' . $t, $basename2);
    else {
        $sql = "SELECT u_power,u_ability FROM " . DB::table('es_member') . " WHERE uid = {$uid}";
        $info = DB::fetch_first($sql);
        $u_power = $info['u_power'];
        $u_ability = unserialize($info['u_ability']);
        
        //����ֵ�ж�
        if ($u_power < $global_admincp['xl_power']){
            showmessage('����ֵ����!');
            die();
        }else{
            $u_power -= $global_admincp['xl_power'];
        }
        
        //��Ǯ�ж�
        
        
        //����ֵ����
        $exp = $global_admincp['xl_exp'];
        $exp_max = $global_admincp['main_exp'];
        
        $u_ability['main_exp'][$xl] += $exp;

        if ($u_ability['main_exp'][$xl]>= $exp_max) {
            $u_ability['main_exp'][$xl] -= $exp_max;
            $u_ability['main_abi'][$xl] += 1;
        }
        //��������
        $data['u_power'] = $u_power;
        //
        $data['u_ability'] = serialize($u_ability);
        DB::UPDATE("es_member", $data, array('uid' => $uid));
    }
    showmessage('�����ɹ�', $basename2);

} elseif ($action == "delete") {

} elseif ($action == "edit") {


} else {
    showmessage('δ����Ĳ���.', $basename);
}

?>