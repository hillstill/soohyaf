<?php
$page_title='个人中心';
$page_desc='描述';
$page_keywords='key';
$page_identifier='login-page';
$page_head = '<link href="css/signin.css" rel="stylesheet">';
include 'php/header.inc.php';
//echo '<body><div class="container">';
include 'php/top.inc.php';
?>
<div class="" ng-app="myLogin" ng-controller="myLoginCtrl">
	<table border="1">
    <caption>TODO</caption>
    <tr>
        <th>名称</th>
        <th>时间</th>
        <!--<th>操作</th>-->
    </tr>
    <tr >
        <td>{{userInfo.name}}</td>
        <td>{{userInfo.lastIP}}</td>
        <!--<td><button ng-click="remove($index)">Remove</button></td>-->
    </tr>
	</table>
</div>


<script type="text/javascript">
// the main (app) module
var myLogin = angular.module("myLogin", []);

// add a controller
myLogin.controller("myLoginCtrl", function($scope,$http) {
	$scope.userInfo = {name: "(name-todo)", lastIP: '(ymd-todo)'};

	$scope.processForm = function() {
			$http({		method: 'JSONP',  
						url: api_host+'user/info&__VIEW__=jsonp&jsonp=JSON_CALLBACK'})
				.success(function(data, status, headers, config) {  // data contains the response  // status is the HTTP status  // headers is the header getter function  // config is the object that was used to create the HTTP request
				isLoginedByChkCode(data.code);
				if(data.code===200){
					$scope.userInfo = data.UserInfo;
				}else{
					alert(formatGLang(data.msg));
				}
				})
				.error(function(data, status, headers, config) {
					alert('connect_error');
				});
	};
	$scope.processForm();
});

</script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>
	<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
<?php
include 'php/footer.inc.php';