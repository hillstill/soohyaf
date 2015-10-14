
	<div class="row">
		<div class="col-xs-3">
			客服电话：400-800-1234
		</div>
		<div class="col-xs-7">&nbsp;
		</div>
		<div id="div_top_menu" class="col-xs-2">

		</div>

	</div>
	<div class="row">
		<div class="col-sm-6 ">
			<a href="/"><img src="imgs/logo.png"></a>
		</div>
		<div class="col-sm-6 col-md-offset-0">
			<button class="btn btn btn-default" type="button" onclick="top.location.href='/'">首页</button>
			<button class="btn btn btn-default" type="button">。。。。</button>
			<button class="btn btn btn-default" type="button">。。。。</button>
			<button class="btn btn btn-default" type="button">关于我们</button>

		</div>

	</div>

<script type="text/javascript" >
$(document).ready(function(){
	var account= $.cookie('nickname');
	if(typeof(account) === 'undefined'){
		document.getElementById('div_top_menu').innerHTML = "<span><a href='login.tpl.php'>登入<\/a> - <a href='register.tpl.php'>注册<\/a></span>";
	}else{
		document.getElementById('div_top_menu').innerHTML = "<span>welcome:"+account+" <a href='userinfo.tpl.php'>个人中心<\/a>  <a href='logout.tpl.php'>登出<\/a></span>";
	}
});
</script>



