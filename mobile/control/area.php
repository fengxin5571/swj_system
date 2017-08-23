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
    public function init_mapOp(){
        $area=new ip_area();
        $ip=getIp()?getIp():"127.0.0.1";
        $city_name=$area->getcity($ip);
        if($city_name[city]=="LAN"){
            $city_name['city']="太原市";
        }
        $data["area"]=$city_name;
        $model_store = Model('store');
        //查询条件
        $condition = array();
        //所需字段
        $fields = "*";
        //排序方式
        $order = "store_id desc";
        $store_list = $model_store->where($condition)->order($order)->select();
        $own_store_list = $store_list;
        $simply_store_list = array();
        foreach ($own_store_list as $key => $value) {
            $simply_store_list[$key]['store_name'] = $own_store_list[$key]['store_name'];
            $simply_store_list[$key]['store_address'] = $own_store_list[$key]['store_address'];
            $simply_store_list[$key]['live_store_address'] = $own_store_list[$key]['live_store_address'];
            $simply_store_list[$key]['live_store_tel'] = $own_store_list[$key]['live_store_tel'];
            $simply_store_list[$key]['store_area_info'] = $own_store_list[$key]['area_info'];
            $simply_store_list[$key]['store_avatar'] =$own_store_list[$key]['store_avatar'];
            $simply_store_list[$key]['goods_count'] = $own_store_list[$key]['goods_count'];
            $simply_store_list[$key]['store_avatar_url'] = UPLOAD_SITE_URL.'/'.ATTACH_COMMON.DS.C('default_store_avatar');
            if($own_store_list[$key]['store_avatar']){
                $simply_store_list[$key]['store_avatar_url'] = UPLOAD_SITE_URL.'/shop/store/'.$own_store_list[$key]['store_avatar'];
            }
        }
        $data['store_list']=$simply_store_list;
        output_data($data);
       
    } 

}
