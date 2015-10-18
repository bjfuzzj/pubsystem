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
        $this->load->model('act');
    }
    public function index(){
        echo 'xxxxxxxxxxxxx';
    }

    /**
     * 点赞动作
     */
    public function lc_dianzan(){
        $this->handle();
        $lcid = $this->input->post('id',true);
        $this->act->clickzan($lcid,1,1);

        $this->ajaxReturn('点赞成功');
    }
    /**
     * 贷款点赞动作
     */
    public function dk_dianzan(){
        $this->handle();
        $id = $this->input->post('id',true);
        $this->act->clickzan($id,2,1);

        $this->ajaxReturn('点赞成功');
    }
    /**
     * 发布评论
     */
    public function lc_comment(){
        $this->handle();
        $id = $this->input->post('id',true);
        $content = $this->input->post('content',true);
        $username=

    }
    /**
     * 产生用户名
     */
    public function createname(){

    }
    /**
     *
     */
    public function handle(){
        if($this->input->is_ajax_request()){
            $this->ajaxReturn('非法提交',1);
        }

    }
}
