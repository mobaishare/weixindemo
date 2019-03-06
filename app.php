<?php

//设置常量
// defined('weixin');

//判断是否存在OPENID值
if(isset($_REQUEST['openid']) || isset($_GET['echostr'])){
	//加载微信类
	include './lib/message.php';
	new weixin();
}


if (isset($_GET['menu']) && $_GET['menu'] == 'yes') {
	//加载菜单类
	include './lib/menu.php';
	$menu = new Menu();
	$data = '{
				"button":[
				{    
				  "type":"click",
				  "name":"一级菜单",
				  "key":"V1001_TODAY_MUSIC"
				},
				{
				   "name":"一级菜单",
				   "sub_button":[
				   {    
				       "type":"view",
				       "name":"二级菜单",
				       "url":"http://www.soso.com/"
				    },
				    {
				         "type":"miniprogram",
				         "name":"二级菜单",
				         "url":"http://mp.weixin.qq.com",
				         "appid":"wx286b93c14bbf93aa",
				         "pagepath":"pages/lunar/index"
				     },
				    {
				       "type":"click",
				       "name":"二级菜单",
				       "key":"V1001_GOOD"
				    }]
				}]
			}';
	$res = $menu->createMenu($data);
	if ($res != 0) {
		return '操作失败';
	}
}
if ( isset( $_GET['code'] ) ){
	include './lib/QRcode.php';
	$code = new QRcode();
	// echo $code->index();
}