<?php
/**
 * 地区
 *
 * @好商城V4 (c) 2015-2016 33hao Inc.
 * @license    http://www.33hao.com
 * @link       交流群号：216611541
 * @since      好商城提供技术支持 授权请购买shopnc授权
 */

defined('InShopNC') or exit('Access Invalid!');
class areaControl extends mobileHomeControl{

    public function __construct() {
        parent::__construct();
    }

    public function indexOp() {
        $this->area_listOp();
    }

    /**
     * 地区列表
     */
    public function area_listOp() {
        $area_id = intval($_GET['area_id']);

        $model_area = Model('area');

        $condition = array();
        if($area_id > 0) {
            $condition['area_parent_id'] = $area_id;
        } else {
            $condition['area_deep'] = 1;
        }
        $area_list = $model_area->getAreaList($condition, 'area_id,area_name');
        output_data(array('area_list' => $area_list));
    }
    /*
     * 找营养初始化地图
     */
    public function get_cityOp(){
        $area=new ip_area();
        $ip=getIp()?getIp():"127.0.0.1";
        $city_name=$area->getcity($ip);
        if($city_name[city]=="LAN"){
            $city_name['city']="太原市";
        }
        
       
    } 

}
