<?php
$page_title='注册';
$page_desc='描述';
$page_keywords='key';
$page_identifier='login-page';
$page_head = '<link href="css/signin.css" rel="stylesheet">';
include 'php/header.inc.php';
//echo '<body><div class="container">';
include 'php/top.inc.php';
?>
<script src="js/ie-emulation-modes-warning.js"></script>
<form class="form-signin" ng-app="myLogin" ng-controller="myLoginCtrl" ng-submit="processForm()">
	<h2 class="form-signin-heading">注册</h2>
	<label for="inputEmail" class="sr-only">输入账号</label>
	<input type="string"	id="inputEmail"		ng-model="usr"		class="form-control" placeholder="输入账号" required autofocus>
	<label for="inputPassword" class="sr-only">输入密码</label>
	<input type="password"	id="inputPassword"	ng-model="pwd1"		class="form-control" placeholder="输入密码" required>
	<label for="inputPassword" class="sr-only">再次输入</label>
	<input type="password"	id="inputPassword"	ng-model="pwd2"		class="form-control" placeholder="再次输入" required>	
	<label for="inputValidCode"  class="sr-only">校 验 码</label>
	<input type="validcode" id="inputValidCode" ng-model="validcode" class="form-control" placeholder="输入校验码" required>
	<img id="validCodeImg" src="" onclick="this.src=api_host+'passport/validimg&'+Math.random();">
	<a href="#" onclick="document.getElementById('validCodeImg').src=api_host+'passport/validimg&'+Math.random();">看不清，换一张</a>
	<!--<div class="checkbox"><label><input type="checkbox" value="remember-me"> Remember me</label></div>-->
	<button class="btn btn-lg btn-primary btn-block" type="submit" >登入</button>
	没有账号？去<a href="register.tpl.php" >注册</a>。忘记密码？去<a href="forgotpwd.tpl.php" >找回</a>。
</form>


<script type="text/javascript">
// the main (app) module
var myLogin = angular.module("myLogin", []);

// add a controller
myLogin.controller("myLoginCtrl", function($scope,$http) {
	$scope.usr = "";
	$scope.pwd1 = "";
	$scope.pwd2 = "";
	$scope.msg = '';
	$scope.validcode='';

	$scope.processForm = function() {
			if($scope.pwd1!==$scope.pwd2){
				alert(formatGLang(data.msg));
			}else{
				$http({		method: 'JSONP',  
							url: api_host+'passport/register&loginname='+encodeURI($scope.usr)+'&passwd='+encodeURI($scope.pwd1)+'&valid='+encodeURI($scope.validcode)+'&__VIEW__=jsonp&jsonp=JSON_CALLBACK'})
					.success(function(data, status, headers, config) {  // data contains the response  // status is the HTTP status  // headers is the header getter function  // config is the object that was used to create the HTTP request
					if(data.code===200){
						top.location.href='/';
					}else{
						alert(formatGLang(data.msg));
					}
					})
					.error(function(data, status, headers, config) {
						alert('connect_error');
					}); 
			}
	}

});
document.getElementById('validCodeImg').src=api_host+'passport/validimg&'+Math.random();
</script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
	<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
<?php
include 'php/footer.inc.php';