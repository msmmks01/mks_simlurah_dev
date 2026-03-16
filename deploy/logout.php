<?php
date_default_timezone_set("Asia/Makassar");
	session_start();
	function url(){
		$base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://".$_SERVER['HTTP_HOST'];
		$base.= preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/';
		return $base;
	}
session_destroy();
echo("<script type=\"text/javascript\">location.href='".url()."';</script>");