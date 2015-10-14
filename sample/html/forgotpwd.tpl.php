<?php
$page_title='密码找回';
$page_desc='描述';
$page_keywords='key';
$page_identifier='login-page';
$page_head = '<link href="css/signin.css" rel="stylesheet">';
include 'php/header.inc.php';
//echo '<body><div class="container">';
include 'php/top.inc.php';
?>
<script src="js/ie-emulation-modes-warning.js"></script>
<form class="form-signin" ng-app="myLogin" ng-controller="myLoginCtrl">
	<h2 class="form-signin-heading">注册</h2>
	<label for="inputEmail" class="sr-only">输入账号</label>
	<input type="string" id="inputEmail" ng-model="usr" class="form-control" placeholder="输入账号" required autofocus>
	<label for="inputPassword" class="sr-only">输入密码</label>
	<input type="password" id="inputPassword" ng-model="pwd1" class="form-control" placeholder="输入密码" required>
	<label for="inputPassword" class="sr-only">再次输入</label>
	<input type="password" id="inputPassword" ng-model="pwd2" class="form-control" placeholder="再次输入" required>	
	<label for="inputValidCode"  class="sr-only">校 验 码</label>
	<input type="validcode" id="inputValidCode" ng-model="validcode" class="form-control" placeholder="输入校验码" >
	<img id="validCodeImg" src="http://127.0.0.1/soohsample/index.php?__=passport/validimg" onclick="this.src='http://127.0.0.1/soohsample/index.php?__=passport/validimg&'+Math.random();">
	<a href="#" onclick="document.getElementById('validCodeImg').src='http://127.0.0.1/soohsample/index.php?__=passport/validimg&'+Math.random();">看不清，换一张</a>
	<!--<div class="checkbox"><label><input type="checkbox" value="remember-me"> Remember me</label></div>-->
	<button class="btn btn-lg btn-primary btn-block" type="submit" ng-click="doLogin()" >登入</button>
</form>


<script type="text/javascript">
// the main (app) module
var myLogin = angular.module("myLogin", []);

// add a controller
myLogin.controller("myLoginCtrl", function($scope,$http) {
	$scope.usr = "输入账号";
	$scope.pwd1 = "输入密码";
	$scope.pwd2 = "再次输入";
	$scope.msg = '';
	$scope.validcode='';


	$scope.doLogin = function() { 
		    $http({		method: 'JSONP',  
						url: api_host+'passport/login&loginname='+$scope.usr+'&passwd='+$scope.pwd+'&__VIEW__=jsonp&jsonp=JSON_CALLBACK'})
				.success(function(data, status, headers, config) {  // data contains the response  // status is the HTTP status  // headers is the header getter function  // config is the object that was used to create the HTTP request
				if(data.code===200){
					$scope.CheckinBookDone=data.checkinBook.todaychked;
				}else{
					alert(data.code+"#"+data.msg);
				}
				})
				.error(function(data, status, headers, config) {
					alert('connect_error');
				}); 
	};

});
</script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
	<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
<?php
include 'php/footer.inc.php';