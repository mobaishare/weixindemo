<?php

//微信公众号接入
/**
 * 
 */
class weixin {
	//设置XML信息模版
	private $config_xml = [];
	private $config = [];
	private $obj;

	//构造函数
	public  function __construct(){

		if (isset($_GET['echostr'])) {

			echo $this->checkSignature();
		}else{
			//加载配置文件
			$this->config = include 'config.php';
			//加载XML消息配置文件
			$this->config_xml = include 'config_xml.php';

			//执行消息处理函数
			$this->acceptMesage();
		}
	} 
	/*
	*接受消息处理
	*/
	private function acceptMesage(){
		//获取公众平台发送过来的XML数据
		$xml = file_get_contents('php://input');

		 // print_r($xml);
		//把XML转为object对象
		$this->obj = simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);

		// print_r($this->obj);
		// 消息类型
		$type = $this->obj->MsgType;
		// echo $type;
		$msg = '';
		// 动态方法
		$funName = $type.'Fun';

		echo $msg = call_user_func([$this,$funName]);
		// echo $msg = $this->$funName();

		// 写发送日志
		if (!empty($msg)) {
			$this->wirtelog($msg,1);
		}

	}
	/**
	 * 文本消息处理方法
	 */
	private function textFun(){
		$content = (string)$this->obj->Content;
		if (stristr($content,'图文-')) {
			// 被动回复回文消息
			return $this->createImage($content,$content,'https://mmbiz.qpic.cn/mmbiz_png/r0wsGpZ3cnocH1jzZfw7e8VcTJONKaM9rvXxA31k8lzXca133yM8FichNrQNnk4MnhR5d5UTichK5RCibqKCXicOlQ/0?wx_fmt=png');
		}
		// 响应给公众号服务器
		return $this->createText($content);
	}
	/**
	 * 文本消息处理方法
	 */
	private function imageFun(){
		// var_dump($this->obj->MediaId);
		$PicUrl = (string)$this->obj->PicUrl;
		$MsgId = (string)$this->obj->MsgId;
		$MediaId = (string)$this->obj->MediaId;
		
		return $this->createImage($PicUrl,$MsgId,$MediaId);

	}
	/**
	 * 生成文本消息的xml
	 */
	private function createText(string $content){
		return sprintf($this->config_xml['text'],$this->obj->FromUserName,$this->obj->ToUserName,time(),"服务器：".$content);
	}
	/**
	 * 生成图文消息的xml
	 */
	private function createImage($title,$Description,$picurl){
		return sprintf($this->config_xml['image'],$this->obj->FromUserName,$this->obj->ToUserName,time(),$title,$Description,$picurl);
	}
	/*
	*	写日志
	*	@param string $xml xml数据
	*	@param int|integer $flag  0接受 1发送
	*/
	private function wirtelog(string $xml,int $flag = 0){
		//
		$title = $flag == 0 ? '接受' : '发送';
		//
		$dtime = date('Y年m月d日 H:i:s');
		# 日志内容
		$log = $title."【{$dtime}】\n";
		$log .= "-------------------------------------------------------------------------\n";
		$log .= $xml."\n";
		$log .= "-------------------------------------------------------------------------\n";

		// 写日志 追加记录日志
		file_put_contents('log/wx.xml',$log,FILE_APPEND);
	}


 	/*
 	 *初次接入
 	 * */
	private function checkSignature()
	{

	   $signature = $_GET["signature"];
	   $timestamp = $_GET["timestamp"];
	   $nonce = $_GET["nonce"];
	   $echostr = $_GET['echostr'];

	   $tmpArr['token'] = $this->config['weixin'];
	   $tmpArr['timestamp'] = $timestamp;
	   $tmpArr['nonce'] = $nonce;

		//tmpArr = array(timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );

		if($tmpStr == $signature){
			return $echostr;
		}
		
		return '';
		
	}

}