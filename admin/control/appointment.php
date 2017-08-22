<?php 
/**
 * 预约体验
 *
 *
 *
 **by 太常系统 www.sxtaichang.com*/

defined('InShopNC') or exit('Access Invalid!');
class appointmentControl extends SystemControl{
    public function __construct(){
        parent::__construct();
    }
    /*
     * 预约体验列表
     */
    public function indexOp(){
        $appoint_model=Model("appointment");//获取预约表模型
        $model=Model();
        $appoint_items=$model->table("appointment_item")->select();
        /**
         * 删除
         */
        if (chksubmit()){
            if (is_array($_POST['del_id']) && !empty($_POST['del_id'])){
                foreach ($_POST['del_id'] as $k => $v){
                    $v = intval($v);
                    $condition['appointment_id']=$v;
                    $appoint_model->drop($condition);
                    $this->log('删除预约信息[ID:'.implode(',',$_POST['del_id']).']',null);
                    showMessage("删除预约信息成功");
                }
            }else {
				showMessage("请选择要删除的内容");
			}
        }
        Tpl::output("appoint_items",$appoint_items);
        /**
         * 检索条件
         */
        $condition['appointment_item'] = intval($_GET['search_item_id']);
        $condition['appointment_name'] = trim($_GET['search_title']);
        
        /**
         * 分页
         */
        $page	= new Page();
        $page->setEachNum(10);
        $page->setStyle('admin');
        $appoints=$appoint_model->getJoinList($condition,$page);
        Tpl::output("appoints",$appoints);
        Tpl::showpage("appointment.index");

    }
    /*
     * 短信通知用户预约
     * 
     */
    public function send_sms_noticeOp(){
      if(!preg_match('/^0?(13|15|17|18|14)[0-9]{9}$/i',$_REQUEST['mobile'])||empty($_REQUEST['mobile'])){
           showMessage("手机为空！或者手机号码不正确");
       }
       $sms = new Sms();
       $sms_content='【'.C('site_name').'】 您好，您的预约请求我们已经收到，届时会有工作人员联系您，请保持电话畅通！感谢您选择我们';
       $result = $sms->send($_REQUEST['mobile'],$sms_content);
       if($result){
           $appoint_model=Model("appointment");//获取预约表模型
           $update['appointment_status']=1;
           $condition['appointment_id']=$_REQUEST['appointment_id'];
           $appoint_model->modify($update,$condition);
           showMessage("通知成功，请尽快安排预约给客户！");
       }else{
           showMessage("短信通道异常，请稍后再试");
       }
    }
}


?>