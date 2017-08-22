$(function() {

    var headerClone = $('#header').clone();
    $(window).scroll(function(){
        if ($(window).scrollTop() <= $('#main-container1').height()) {
            headerClone = $('#header').clone();
            $('#header').remove();
            headerClone.addClass('transparent').removeClass('');
            headerClone.prependTo('.nctouch-home-top');
        } else {
            headerClone = $('#header').clone();
            $('#header').remove();
            headerClone.addClass('').removeClass('transparent');
            headerClone.prependTo('body');
        }
    });
    $.ajax({
        url: ApiUrl + "/index.php?act=index",
        type: 'get',
        dataType: 'json',
        success: function(result) {
            var data = result.datas;
            var html = '';

            $.each(data, function(k, v) {
                $.each(v, function(kk, vv) {
                    switch (kk) {
                        case 'adv_list':
                        case 'home3':
                            $.each(vv.item, function(k3, v3) {
                                vv.item[k3].url = buildUrl(v3.type, v3.data);
                            });
                            break;

                        case 'home1':
                            vv.url = buildUrl(vv.type, vv.data);
                            break;
                        case 'goods1':
                        	 $.each(vv.item, function(k3, v3) {
                                 vv.item[k3].goods_name = vv.item[k3].goods_name.substr(0,7);
                             });
                        	 
                        	 vv.end_time = vv.end_time*1000 - (new Date().getTime());
                            break;
                        case 'goods_self':
                       	 $.each(vv.item, function(k3, v3) {
                                vv.item[k3].goods_name = vv.item[k3].goods_name.substr(0,6);
                            });
                           break;
                        case 'home2':
                        case 'home4':
                            vv.square_url = buildUrl(vv.square_type, vv.square_data);
                            vv.rectangle1_url = buildUrl(vv.rectangle1_type, vv.rectangle1_data);
                            vv.rectangle2_url = buildUrl(vv.rectangle2_type, vv.rectangle2_data);
                            break;
                        case 'nav_list':
                        	$.each(vv.nav,function(k3,v3){
                        	  if(v3.nav_type==2){//如果是文章类型导航
                        		  vv.nav[k3].nav_url=buildUrl(v3.nav_type,v3.item_id);
                        	  }else if(v3.nav_type==4){//视频类型导航
                        		  vv.nav[k3].nav_url=buildUrl(v3.nav_type,v3.item_id);
                        	  }
                        	});
                            break;
                        case "area":
                        	$(".positionFont_qty").html(vv.city);
                        	break;
                        
                    }
                    if (k == 0) {
                       $("#main-container1").html(template.render(kk, vv));
                    } else {
                      
                      html += template.render(kk, vv);
                      
                    }
                    
                    return false;
                });
            });
           
            
            $("#main-container2").html(html);
            $('.swiper-container').each(function(){
            	// banner按钮
           	    var swiper = new Swiper(this, {
           	        
           	        slidesPerView: 1,
           	        paginationClickable: true,
           	        loop: true,
           	        autoplay : 3000,
           	        autoplayDisableOnInteraction : false,
           	    });
            });
            // 限时秒杀
    	    var swiper1 = new Swiper('.swiper-containerBox', {
    	        slidesPerView: 2.5,
    	        paginationClickable: false,
    	        spaceBetween: 30
    	    });
    	    // 营养套餐专区
    	    var swiper2 = new Swiper('.swiper-containerBoxs', {
    	        slidesPerView: 2.25,
    	        paginationClickable: false,
    	        spaceBetween: 30
    	    });
    	    function sale_end_time(){
    	    	
    	        $('#sale_end_time').each(function(){
    	        	
    	            var obj = $(this);
    	            
    	            var tms = obj.attr("count_down");
    	            if (tms>0) {
    	                tms = parseInt(tms)-1;
    	                var hours = Math.floor(tms / (1 * 60 * 60)) % 24;
    	                var minutes = Math.floor(tms / (1 * 60)) % 60;
    	                var seconds = Math.floor(tms / 1) % 60;
    	                if (hours < 0) hours = 0;
    	                if (minutes < 0) minutes = 0;
    	                if (seconds < 0) seconds = 0;
    	                obj.html(hours+":"+minutes+":"+seconds);
    	                
    	                obj.attr("count_down",tms);
    	            }
    	        });

    	   }
    	   setInterval(sale_end_time,1000);
        }
    });
   
});



//v4 返利
var uid = window.location.href.split("#V3");
var  fragment = uid[1];
if(fragment){
	if (fragment.indexOf("V3") == 0) {document.cookie='uid=0';}
		else {document.cookie='uid='+uid[1];}
	}
