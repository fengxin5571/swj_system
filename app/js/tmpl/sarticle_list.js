//v3-b11
$(function(){
	var ac_id = getQueryString('ac_id')
	
	if (ac_id=='') {
		ac_id=8;
    	//window.location.href = WapSiteUrl + '/index.html';
    	//return;
	}
	
		$.ajax({
			url:ApiUrl+"/index.php?act=article&op=article_list",
			type:'get',
			data:{ac_id:ac_id},
			jsonp:'callback',
			dataType:'jsonp',
			success:function(result){
				var data = result.datas;
				data.WapSiteUrl = WapSiteUrl;
				if(data.article_list){
					$.each(data.article_list,function(k,v){
						v.article_time=new Date(parseInt(v.article_time) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
					});
				}
				var html = template.render('article-list', data);
				$("#article_list_show").html(html);
			}
		});
	
});