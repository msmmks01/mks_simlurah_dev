<?php
session_start();
function url(){
	$base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://".$_SERVER['HTTP_HOST'];
	$base.= preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/';
	return $base;
}
if (isset($_POST['password'])) {
	if ($_POST['password']=='ftp_234') {
		$_SESSION['login'] = true;
		echo("<script type=\"text/javascript\">location.href='".url()."';</script>");
	}else{
		echo("<script type=\"text/javascript\">alert('Password salah');location.href='".url()."';</script>");
	}
}else{
	echo("<script type=\"text/javascript\">alert('Non Submit');location.href='".url()."';</script>");
}