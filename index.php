<?php
//启动SESSION
@session_start();

if(!isset($_SESSION['weixin'])){
	header('location:./login.php');
}
include 'view/index.html';
