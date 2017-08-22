<?php 
/**
 * 指导老师管理
 *
 *
 *
 **by 太常系统 www.sxtaichang.com*/
defined('InShopNC') or exit('Access Invalid!');

class member_advisorControl extends SystemControl{
    public function __construct(){
        parent::__construct();
        Language::read('member');
    }
    /*
     * 指导老师审核列表
     */
    public function member_verifyOp(){
        $member_advisor_verifyModel=Model("member_advisor_verify");
        /**
    	 * 删除审核
    	 */
		if (chksubmit()){
			if (is_array($_POST['del_id']) && !empty($_POST['del_id'])){
			    foreach ($_POST['del_id'] as $k=>$v){
			        $condition['verify_id']=$v;
			        $member_advisor_verifyModel->drop($condition);
			    }
				$this->log('删除审核列表[ID:'.implode(',',$_POST['del_id']).']',null);
				showMessage("删除成功");
			}else {
				showMessage("删除失败");
			}
		}
        /**
         * 检索条件
         */
        $condition['verify_status'] = intval($_GET['verify_status']);
        $condition['member_name'] = trim($_GET['member_name']);
        $condition['join_type'] ="right join";
        /**
         * 分页
         */
        $page	= new Page();
        $page->setEachNum(10);
        $page->setStyle('admin');
        /**
         * 列表
         */
        $member_advisor_verify_list=$member_advisor_verifyModel->getJoinList($condition,$page);
        Tpl::output("member_advisor_verify_list",$member_advisor_verify_list);
        Tpl::output("page",$page->show());
        Tpl::showpage("member_verify.index");

    }
    /**
     * 操作审核
     */
    public function  operateOp(){
        $flag=$_REQUEST['flag'];
        $verify_id=$_REQUEST['verify_id'];
        if(empty($flag)||empty($verify_id)){
            showMessage("参数错误","html","error");
            exit;
        }
        $member_advisor_verifyModel=Model("member_advisor_verify");
        $condition['verify_id']=$verify_id;
        $update['verify_status']=$flag=="pass"?1:2;
        $msg=$flag=="pass"?"已同意此审核":"已拒绝此审核";
        if($member_advisor_verifyModel->modify($update,$condition)){
            $condition=array();
            $condition['member_id']=$_REQUEST['member_id'];
            if($update['verify_status']==1){
                $model_member = Model('member');
                $model_member->editMember($condition,array('member_advisor'=>1));//修改会员为指导老师
                /**
                 * 实例化相册模型
                 */
                $model = Model ();
                
                $insert = array ();
                $insert ['ac_name'] = '荣誉墙';
                $insert ['member_id'] = $_REQUEST['member_id'];
                $insert ['ac_des'] = '此相册为指导老师荣誉展示用';
                $insert ['ac_sort'] = '';
                $insert ['upload_time'] = time ();
                $insert ['is_default'] = 2;
                $return = $model->table ( 'sns_albumclass' )->insert ( $insert );
            }
            showMessage($msg);
            exit();
        }
        showMessage("操作失败","html","error");
        exit;
        
    }
}
?>