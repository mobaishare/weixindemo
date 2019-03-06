<?php

include 'view/login.html';
if (isset($_GET['login']) && $_GET['login'] == 'post') {
	return $_POST['password'];
}