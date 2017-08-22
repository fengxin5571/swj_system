//v3-b11
$(function(){
	var article_id = getQueryString('article_id')
	
	if (article_id=='') {
    	window.location.href = WapSiteUrl + '/index.html';
    	return;
	}
	else {
		$.ajax({
			url:ApiUrl+"/index.php?act=article&op=article_show",
			type:'get',
			data:{article_id:article_id},
			jsonp:'callback',
			dataType:'jsonp',
			success:function(result){
				var data = result.datas;
				$("title").html(data.article_title);
				$('.stock_zn').html(data.article_title.substr(0,15));
				$('.detail_box_zn').html(data.article_content);
				var ac_id=data.ac_id;
				$.ajax({
					url:ApiUrl+"/index.php?act=article&op=article_list",
					type:'get',
					data:{ac_id:ac_id},
					jsonp:'callback',
					dataType:'jsonp',
					success:function(result){
						var data = result.datas;
						if(data.article_list){
							$.each(data.article_list,function(k,v){
								
								v.article_time=new Date(parseInt(v.article_time) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
							});
						}
						var html=template.render('article_list', data);
						$('.relative_recommend_zn').html(html);
					}
				});
				
			}
		});
	}	
});