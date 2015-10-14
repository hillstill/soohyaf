var glang = {
	'invalidCode':'验证码错误，请重试',
	'account_or_password_error':'账号或密码错误'
};

function formatGLang(fmt)
{
	if (glang[fmt]) return glang[fmt];
	else return fmt;
}