//header("Access-Control-Allow-Origin：*")
var api_host = 'http://192.168.56.110:8082/API001/index.php?__=';//passport/login
var img_host = 'http://192.168.56.110/';

function isLoginedByChkCode(code)
{
	if(code===401){
		top.location.href='/';
	}
}