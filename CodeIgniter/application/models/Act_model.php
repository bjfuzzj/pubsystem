<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 15/7/9
 * Time: 下午5:14
 */
class Act_model extends CI_Model{

    public function __construct(){
        parent::__construct();
    }

    /**
     * @param $lcid
     * @param int $step
     * @return mixed
     * @
     */
    public function clickzan($id,$type,$step=1){
        if($type==1) {
            $table = 'lc_detail';
            $field = 'lc_dianzan';
        }elseif($type==2){
            $table = 'dk_detail';
            $field = 'dk_dianzan';
        }elseif($type==3){
            $table = 'comment';
            $field = 'zan';
        }
        $this->db->set($field, $field.'+'.$step,false);
        $this->db->where('d_id', $id);
        $this->db->update($table);
        return $this->db->affected_rows();
    }
    public function clicktag($id,$type,$step=1){
        if($type==1) {
            $table = 'lctag_detail';
            $field = 'lctag_num';
        }elseif($type==2){
            $table = 'dktag_detail';
            $field = 'dktag_num';
        }
        $this->db->set($field, $field.'+'.$step,false);
        $this->db->where('d_id', $id);
        $this->db->update($table);
        return $this->db->affected_rows();
    }
    /**
     * @param $id  理财产品id
     * @param $username 评价用户
     * @param $ip 评价用户的ip
     * @param $content 评价内容
     * @param $time 评价时间
     */
    public function comment($type,$id,$username,$guid,$ip,$content){
        $data =array(
            'type'=>$type,
            'pro_id'=>$id,
            'user'=>$username,
            'guid'=>$guid,
            'ip'=>$ip,
            'content'=>$content,
            'zan'=>0,
            'status'=>0,
            'createdatetime'=>date('Y-m-d H:i:s')

        );
        $this->db->insert('comment',$data);
        return $this->db->insert_id();
    }

    /**
     * @param $name
     * @param $mobile
     * @param $content
     * @param $recommendation
     *
     */
    public function addfeedback($name,$mobile,$content,$recommendation,$ip){
        $data = array(
            'username' =>$name,
            'usertel'  =>$mobile,
            'neirong'  =>$content,
            'jianyi'   =>$recommendation,
            'ip'       =>$ip,
            'createdatetime'=>date('Y-m-d H:i:s')

        );
        $this->db->insert('fankui',$data);
        return $this->db->insert_id();
    }



}