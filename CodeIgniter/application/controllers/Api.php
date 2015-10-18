<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 15/7/9
 * Time: 下午7:58
 */
class Api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('act_model','act');
       // $this->load->library('encrypt');
        $this->handle();
    }
    public function index(){
        echo 'xxxxxxxxxxxxx';
    }

    /**
     * 点赞动作
     */
    public function lc_dianzan(){

        $ip = $this->input->ip_address();
        $id = $this->input->post('id',true);
        $key = "lc_dianzan_{$id}_{$ip}";
        if($this->session->userdata($key)){
            $this->ajaxReturn('已经点过赞了~~~',1);
        }

        $ret = $this->act->clickzan($id,1,1);
        if($ret){
           $this->session->set_userdata($key,time());
        }
        $this->ajaxReturn('点赞成功',0,$ret);
    }
    /**
     * 贷款点赞动作
     */
    public function dk_dianzan(){

        $ip = $this->input->ip_address();
        $id = $this->input->post('id',true);
        $key = "dk_dianzan_{$id}_{$ip}";
        if($this->session->userdata($key)){
            $this->ajaxReturn('已经点过赞了~~~',1);
        }

        $ret = $this->act->clickzan($id,2,1);
        if($ret){
            $this->session->set_userdata($key,time());
        }
        $this->ajaxReturn('点赞成功');
    }
    /**
     * 评论点赞
     */
    public function pl_dianzan(){

        $ip = $this->input->ip_address();
        $id = $this->input->post('id',true);
        $key = "pl_dianzan_{$id}_{$ip}";
        if($this->session->userdata($key)){
            $this->ajaxReturn('已经点过赞了~~~',1);
        }

        $ret = $this->act->clickzan($id,3,1);
        if($ret){
            $this->session->set_userdata($key,time());
        }
        $this->ajaxReturn('点赞成功');
    }
    /**
     * 发布评论
     */
    public function lc_comment(){

        $key     = 'user';
        $time    = time();
        $id      = $this->input->post('id',true);
        $content = $this->input->post('content',true);
        $encoded = $this->input->cookie($key,true);
        //$userinfo= $this->encrypt->decode($encoded);
        $userinfo= json_decode($encoded,true);
        if(empty($userinfo)){

            $ip   = $this->input->ip_address();
            $agent= $this->input->user_agent();
            $user = $this->createname($ip);
            $guid = $user['guid'];
            $username = $user['name'];
            $cookie = array(
                'ip'   =>$ip,
                'name' =>$user['name'],
                'time' =>$time,
                'guid' =>$user['guid'],
                'agent'=>$agent
            );
            //$value = $this->encrypt->encode($cookie);
            $value = json_encode($cookie);
            $this->input->set_cookie($key,$value,31536000);

        }else{
            $ip    = $userinfo['ip'];
            $guid  = $userinfo['guid'];
            $username = $userinfo['name'];
        }

        $id = $this->act->comment(1,$id,$username,$guid,$ip,$content);

        $this->ajaxReturn('评论成功',0,array('username'=>$username,'content'=>$content,'time'=>date('Y-m-d H:i:s',$time),'id'=>$id));


    }
    /**
     * 发布评论
     */
    public function dk_comment(){

        $key     = 'user';
        $time    = time();
        $id      = $this->input->post('id',true);
        $content = $this->input->post('content',true);
        $encoded = $this->input->cookie($key,true);
        //$userinfo= $this->encrypt->decode($encoded);
        $userinfo= json_decode($encoded,true);
        if(empty($userinfo)){

            $ip   = $this->input->ip_address();
            $agent= $this->input->user_agent();
            $user = $this->createname($ip);
            $guid = $user['guid'];
            $username = $user['name'];
            $cookie = array(
                'ip'   =>$ip,
                'name' =>$user['name'],
                'time' =>$time,
                'guid' =>$user['guid'],
                'agent'=>$agent
            );
            //$value = $this->encrypt->encode($cookie);
            $value = json_encode($cookie);
            $this->input->set_cookie($key,$value,31536000);

        }else{
            $ip    = $userinfo['ip'];
            $guid  = $userinfo['guid'];
            $username = $userinfo['name'];
        }

        $id = $this->act->comment(2,$id,$username,$guid,$ip,$content);

        $this->ajaxReturn('评论成功',0,array('username'=>$username,'content'=>$content,'time'=>date('Y-m-d H:i:s',$time),'id'=>$id));


    }

    /**
     * 理财点击tag
     */
    public function lc_clicktag(){

        $ip = $this->input->ip_address();
        $id = $this->input->post('id',true);
        $key = "lc_clicktag_{$id}_{$ip}";
        if($this->session->userdata($key)){
            $this->ajaxReturn('已经点过赞了~~~',1);
        }

        $ret = $this->act->clicktag($id,1,1);
        if($ret){
            $this->session->set_userdata($key,time());
        }
        $this->ajaxReturn('点赞成功');
    }

    /**
     * 贷款点击tag
     */
    public function dk_clicktag(){
        $ip = $this->input->ip_address();
        $id = $this->input->post('id',true);
        $key = "dk_clicktag_{$id}_{$ip}";
        if($this->session->userdata($key)){
            $this->ajaxReturn('已经点过赞了~~~',1);
        }

        $ret = $this->act->clicktag($id,2,1);
        if($ret){
            $this->session->set_userdata($key,time());
        }
        $this->ajaxReturn('点赞成功');
    }
    /**
     * 反馈
     */
    public function addfeedback(){

        $ip = $this->input->ip_address();
        $key = "feedback_{$ip}";
        $username = $this->input->post('username',true);
        $usertel = $this->input->post('usertel',true);
        $content  = $this->input->post('neirong',true);
        $recommendation =$this->input->post('jianyi',true);

        if($this->session->userdata($key)){
            $this->ajaxReturn('已经反馈过了亲，请不要频繁提交~~~',1);
        }
        $ret = $this->act->addfeedback($username,$usertel,$content,$recommendation,$ip);
        if($ret){
            $this->session->set_userdata($key,time());
        }
        $this->ajaxReturn('反馈成功');
    }
    /**
     * 产生用户名
     */
    public function createname($ip){

        return array('guid' => guid(),'name'=>'找理财用户_'.substr(md5($ip),0,5).mt_rand(100,999));
    }
    /**
     * 过滤非法提交
     */
    public function handle(){
        if(!$this->input->is_ajax_request()){
            $this->ajaxReturn('非法提交',1);
        }

    }
}
