<?php
$page_title='首页';
$page_desc='描述';
$page_keywords='key';
$page_identifier='homepage';
include 'php/header.inc.php';
//echo '<body><div class="container">';
include 'php/top.inc.php';
?>

<div data-ride="carousel" class="carousel slide" id="carousel-example-generic">
	<ol class="carousel-indicators">
	  <li class="" data-slide-to="0" data-target="#carousel-example-generic"></li>
	  <li data-slide-to="1" data-target="#carousel-example-generic" class="active"></li>
	  <li data-slide-to="2" data-target="#carousel-example-generic" class=""></li>
	</ol>
	<div role="listbox" class="carousel-inner">
	  <div class="item">
		  <img alt="First slide [1920x500]" data-src="holder.js/1140x500/auto/#777:#555/text:First slide" src="imgs/homepage/banner1.jpg" data-holder-rendered="true">
	  </div>
	  <div class="item active">
		<img alt="Second slide [1920x500]" data-src="holder.js/1140x500/auto/#666:#444/text:Second slide" src="imgs/homepage/banner2.jpg" data-holder-rendered="true">
	  </div>
	  <div class="item">
		<img alt="Third slide [1920x500]" data-src="holder.js/1140x500/auto/#555:#333/text:Third slide" src="imgs/homepage/banner3.jpg" data-holder-rendered="true">
	  </div>
	</div>
	<a data-slide="prev" role="button" href="#carousel-example-generic" class="left carousel-control"><span aria-hidden="true" class="glyphicon glyphicon-chevron-left"></span><span class="sr-only">Previous</span></a>
	<a data-slide="next" role="button" href="#carousel-example-generic" class="right carousel-control"><span aria-hidden="true" class="glyphicon glyphicon-chevron-right"></span><span class="sr-only">Next</span></a>
</div>

<div class="" ng-app="myApp" ng-controller="myCtrl">
	<table border="1">
    <caption>最近挂失票据一览</caption>
    <tr>
        <th>序号</th>
        <th>商品</th>
        <th>单价</th>
        <th>数量</th>
        <th>金额</th>
        <!--<th>操作</th>-->
    </tr>
    <tr ng-repeat="item in items">
        <td>{{$index + 1}}</td>
        <td>{{item.name}}</td>
        <td>{{item.price | currency}}</td>
        <td><input ng-model="item.quantity"></td>
        <td>{{item.quantity * item.price | currency}}</td>
        <!--<td><button ng-click="remove($index)">Remove</button></td>-->
    </tr>
	</table>
</div>

<script type="text/javascript">
// the main (app) module
var myApp = angular.module("myApp", []);

// add a controller
myApp.controller("myCtrl", function($scope,$http) {
	$scope.usr = "enter your account";
	$scope.pwd = "enter your password";
	$scope.msg = 'to do';
	$scope.CheckinBookDone='unknown';
	$scope.items = [
            {name: " ", quantity: 0, price: 0.00},
            {name: " ", quantity: 0, price: 0.00},
			{name: " ", quantity: 0, price: 0.00}
        ];
 
	$scope.remove = function (index) {
		$scope.items.splice(index, 1);
	}
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
	$scope.refreshItems = function () {
		 $http({		method: 'JSONP',  	url: api_host+'loger/openpage&data=test&__VIEW__=jsonp&jsonp=JSON_CALLBACK',})
				.success(function(data, status, headers, config) {  // data contains the response  // status is the HTTP status  // headers is the header getter function  // config is the object that was used to create the HTTP request
					if(data.code==200){
						$scope.items = [
							{name: "2雷柏（Rapoo） V500 机械游戏键盘 机械黄轴", quantity: 1, price: 199.00},
							{name: "2雷柏（Rapoo） V20 光学游戏鼠标 黑色烈焰版", quantity: 1, price: 139.00},
							{name: "2AngularJS权威教程", quantity: 2, price: 84.20}
						];
					}else{
						alert(data.code+"#"+data.msg);
					}
				})
				.error(function(data, status, headers, config) {
					alert('connect_error');
				}); 
	};
	$scope.refreshItems();
});
</script>
<?php
include 'php/footer.inc.php';