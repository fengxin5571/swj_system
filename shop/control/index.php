<?php
/**
 * 默认展示页面
 *
 *
 **by 好商城V3 www.33hao.com 好商城V3 运营版*/


defined('InShopNC') or exit('Access Invalid!');
class indexControl extends BaseHomeControl{ //父类定义了公共头部，以及模板路径等
    public function selfindexOp(){//自定首页
        Model('seo')->type('index')->show();
        Tpl::setDir('duyiwang');
        Tpl::setLayout('home_dw_layout');
        //板块信息
        $model_web_config = Model('web_config');
        $web_html = $model_web_config->getWebHtml('index');
        $condition['web_id']=102;
        $webcode=$model_web_config->getCodeList($condition);
        if($webcode){//获取视频推荐内容
            foreach ($webcode as &$video_recommend){
                $video_recommend[code_info]=$model_web_config->get_array($video_recommend['code_info'],"array");
            }
        }
        //友情链接
        $model_link = Model('link');
        $link_list = $model_link->getLinkList($condition,$page);
        /**
         * 整理图片链接
         */
        if (is_array($link_list)){
            foreach ($link_list as $k => $v){
                if (!empty($v['link_pic'])){
                    $link_list[$k]['link_pic'] = UPLOAD_SITE_URL.'/'.ATTACH_PATH.'/common/'.DS.$v['link_pic'];
                }
            }
        }
        
        /*
         * 指导老师列表
         */
        $member_model=Model('member');
        $condition=array();
        $condition['member_advisor']=1;
        $member_advisor_list=$member_model->getMemberList($condition,"*",0,"member_id desc","9");
        /*
         * 推荐视频
         */
        $video_model=Model("video");
        $condition=array();
        $condition['order']="rand()";
        $condition['limit']=9;
        $video_list=$video_model->getVideoList($condition);
        /*
         * 推荐商品
         */
        $model_booth = Model('p_booth');
        $goods_list = $model_booth->getBoothGoodsList($where, 'goods_id', 0,9,"rand()");
        if (!empty($goods_list)) {
            $goodsid_array = array();
            foreach ($goods_list as $val) {
                $goodsid_array[] = $val['goods_id'];
            }
            $goods_list = Model('goods')->getGoodsList(array('goods_id' => array('in', $goodsid_array)));
        }
        /*
         * 健康知识
         */
        $article_model=Model('article');
        $article_class_model = Model('article_tag');
        $article_class=$article_class_model->getList("",0,"","*","5");
        if($article_class){
            $condition=array();
            $condition['table'] = "article_tag,article";
            $condition['join_on'] = array('article_tag.tag_id=article.tag_id');
            $condition['article_recommend']=1;
            $condition['order']="rand()";
            $article_list=$article_model->getJoinList($condition);
        }
        /*
         * 指导老师问答
         */
        $member_model=Model('member');
        $condition=array();
        $condition['member_advisor']=1;
        $member_advisor_wd_list=$member_model->getMemberList($condition,"*",0,"rand()","4");
        if($member_advisor_wd_list){
            $model_index=Model();
            foreach ($member_advisor_wd_list as &$advisor){
                $on="question.question_id=answer.answer_qid";
                $advisor['answer']=$model_index->table('question,answer')->where("answer_guide=$advisor[member_id]")->join("right")->on($on)->limit(1)->find();
            }
        }
        
        /*
         *  健康问答
         */
        $question_model=Model();
        $question_list=$question_model->table("question")->where("question_status=1")->order("rand()")->limit(3)->select();
        if($question_list){
            foreach ($question_list as &$question){
                $question["answers"]=$question_model->table("answer")->order("answer_id desc")->where("answer_qid=$question[question_id]")->limit(1)->find();
            }
        }
        Tpl::output("question_list",$question_list);
        Tpl::output("member_advisor_wd_list",$member_advisor_wd_list);
        Tpl::output("article_class",$article_class);
        Tpl::output("article_list",$article_list);
        Tpl::output('goods_list',$goods_list);
        Tpl::output('member_advisor_list',$member_advisor_list);
        Tpl::output("video_list",$video_list);
        Tpl::output('link_list',$link_list);
        Tpl::output('video_recommend',$webcode);
        Tpl::output('web_html',$web_html);
        Tpl::showpage('index');
    }
	public function indexOp(){//商城首页
		Language::read('home_index_index');
		Tpl::output('index_sign','index');
		
		//把加密的用户id写入cookie  by 3 3h ao.co m 已换另一个方式，临时去掉此方法
		$uid = intval(base64_decode($_COOKIE['uid']));
		//抢购专区
		Language::read('member_groupbuy');
        $model_groupbuy = Model('groupbuy');
        $group_list = $model_groupbuy->getGroupbuyCommendedList(4);
		Tpl::output('group_list', $group_list);
		//友情链接
		$model_link = Model('link');
		$link_list = $model_link->getLinkList($condition,$page);
		//热门晒单 v3-b12
    	$goods_evaluate_info = Model('evaluate_goods')->getEvaluateGoodsList(6);
    	Tpl::output('goods_evaluate_info', $goods_evaluate_info);
		/**
		 * 整理图片链接
		 */
		if (is_array($link_list)){
			foreach ($link_list as $k => $v){
				if (!empty($v['link_pic'])){
					$link_list[$k]['link_pic'] = UPLOAD_SITE_URL.'/'.ATTACH_PATH.'/common/'.DS.$v['link_pic'];
				}
			}
		}
		Tpl::output('$link_list',$link_list);
		//限时折扣
        $model_xianshi_goods = Model('p_xianshi_goods');
        $xianshi_item = $model_xianshi_goods->getXianshiGoodsCommendList(4);
		Tpl::output('xianshi_item', $xianshi_item);

		//板块信息
		$model_web_config = Model('web_config');
		$web_html = $model_web_config->getWebHtml('index');
		Tpl::output('web_html',$web_html);
		
		//显示订单信息
		if($_SESSION['is_login'])
		{
			 //交易提醒 - 显示数量
			$model_order = Model('order');
			$member_order_info['order_nopay_count'] = $model_order->getOrderCountByID('buyer',$_SESSION['member_id'],'NewCount');
			$member_order_info['order_noreceipt_count'] = $model_order->getOrderCountByID('buyer',$_SESSION['member_id'],'SendCount');
			$member_order_info['order_noeval_count'] = $model_order->getOrderCountByID('buyer',$_SESSION['member_id'],'EvalCount');
			Tpl::output('member_order_info',$member_order_info);
		}

		Model('seo')->type('index')->show();
		Tpl::showpage('index');
	}
    
	//json输出商品分类
	public function josn_classOp() {
		/**
		 * 实例化商品分类模型
		 */
		$model_class		= Model('goods_class');
		$goods_class		= $model_class->getGoodsClassListByParentId(intval($_GET['gc_id']));
		$array				= array();
		if(is_array($goods_class) and count($goods_class)>0) {
			foreach ($goods_class as $val) {
				$array[$val['gc_id']] = array('gc_id'=>$val['gc_id'],'gc_name'=>htmlspecialchars($val['gc_name']),'gc_parent_id'=>$val['gc_parent_id'],'commis_rate'=>$val['commis_rate'],'gc_sort'=>$val['gc_sort']);
			}
		}
		/**
		 * 转码
		 */
		if (strtoupper(CHARSET) == 'GBK'){
			$array = Language::getUTF8(array_values($array));//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
		} else {
			$array = array_values($array);
		}
		echo $_GET['callback'].'('.json_encode($array).')';
	}

   
	
	//闲置物品地区json输出
	public function flea_areaOp() {
		if(intval($_GET['check']) > 0) {
			$_GET['area_id'] = $_GET['region_id'];
		}
		if(intval($_GET['area_id']) == 0) {
			return ;
		}
		$model_area	= Model('flea_area');
		$area_array			= $model_area->getListArea(array('flea_area_parent_id'=>intval($_GET['area_id'])),'flea_area_sort desc');
		$array	= array();
		if(is_array($area_array) and count($area_array)>0) {
			foreach ($area_array as $val) {
				$array[$val['flea_area_id']] = array('flea_area_id'=>$val['flea_area_id'],'flea_area_name'=>htmlspecialchars($val['flea_area_name']),'flea_area_parent_id'=>$val['flea_area_parent_id'],'flea_area_sort'=>$val['flea_area_sort']);
			}
			/**
			 * 转码
			 */
			if (strtoupper(CHARSET) == 'GBK'){
				$array = Language::getUTF8(array_values($array));//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
			} else {
				$array = array_values($array);
			}
		}
		if(intval($_GET['check']) > 0) {//判断当前地区是否为最后一级
			if(!empty($array) && is_array($array)) {
				echo 'false';
			} else {
				echo 'true';
			}
		} else {
			echo json_encode($array);
		}
	}

	//json输出闲置物品分类
	public function josn_flea_classOp() {
		/**
		 * 实例化商品分类模型
		 */
		$model_class		= Model('flea_class');
		$goods_class		= $model_class->getClassList(array('gc_parent_id'=>intval($_GET['gc_id'])));
		$array				= array();
		if(is_array($goods_class) and count($goods_class)>0) {
			foreach ($goods_class as $val) {
				$array[$val['gc_id']] = array('gc_id'=>$val['gc_id'],'gc_name'=>htmlspecialchars($val['gc_name']),'gc_parent_id'=>$val['gc_parent_id'],'gc_sort'=>$val['gc_sort']);
			}
		}
		/**
		 * 转码
		 */
		if (strtoupper(CHARSET) == 'GBK'){
			$array = Language::getUTF8(array_values($array));//网站GBK使用编码时,转换为UTF-8,防止json输出汉字问题
		} else {
			$array = array_values($array);
		}
		echo json_encode($array);
	}
	
   /**
     * json输出地址数组 原data/resource/js/area_array.js
     */
    public function json_areaOp()
    {
        echo $_GET['callback'].'('.json_encode(Model('area')->getAreaArrayForJson()).')';
    }
	
	/**
     * json输出地址数组 v3-b12
     */
    public function json_area_showOp()
    {
        $area_info['text'] = Model('area')->getTopAreaName(intval($_GET['area_id']));
        echo $_GET['callback'].'('.json_encode($area_info).')';
    }
	//判断是否登录
	public function loginOp(){
		echo ($_SESSION['is_login'] == '1')? '1':'0';
	}

	/**
	 * 头部最近浏览的商品
	 */
	public function viewed_infoOp(){
	    $info = array();
		if ($_SESSION['is_login'] == '1') {
		    $member_id = $_SESSION['member_id'];
		    $info['m_id'] = $member_id;
		    if (C('voucher_allow') == 1) {
		        $time_to = time();//当前日期
    		    $info['voucher'] = Model()->table('voucher')->where(array('voucher_owner_id'=> $member_id,'voucher_state'=> 1,
    		    'voucher_start_date'=> array('elt',$time_to),'voucher_end_date'=> array('egt',$time_to)))->count();
		    }
    		$time_to = strtotime(date('Y-m-d'));//当前日期
    		$time_from = date('Y-m-d',($time_to-60*60*24*7));//7天前
		    $info['consult'] = Model()->table('consult')->where(array('member_id'=> $member_id,
		    'consult_reply_time'=> array(array('gt',strtotime($time_from)),array('lt',$time_to+60*60*24),'and')))->count();
		}
		$goods_list = Model('goods_browse')->getViewedGoodsList($_SESSION['member_id'],5);
		if(is_array($goods_list) && !empty($goods_list)) {
		    $viewed_goods = array();
		    foreach ($goods_list as $key => $val) {
		        $goods_id = $val['goods_id'];
		        $val['url'] = urlShop('goods', 'index', array('goods_id' => $goods_id));
		        $val['goods_image'] = thumb($val, 60);
		        $viewed_goods[$goods_id] = $val;
		    }
		    $info['viewed_goods'] = $viewed_goods;
		}
		if (strtoupper(CHARSET) == 'GBK'){
			$info = Language::getUTF8($info);
		}
		echo json_encode($info);
	}
	/**
	 * 查询每月的周数组
	 */
	public function getweekofmonthOp(){
	    import('function.datehelper');
	    $year = $_GET['y'];
	    $month = $_GET['m'];
	    $week_arr = getMonthWeekArr($year, $month);
	    echo json_encode($week_arr);
	    die;
	}
}
