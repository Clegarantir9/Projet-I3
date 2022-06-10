<?php

require 'Mysql.php';

class Membership {
	private $existe;
	
	function validate_user($un, $pwd) {
		$mysql = New Mysql();
		$ensure_credentials = $mysql->verify_Username_and_Pass($un,$un, md5($pwd));
		
		if($ensure_credentials) {
			$_SESSION['status'] = 'authorized';
			header("location: index.php");
		} else return "veuillez entrer un login ou un email correct avec le mots de passe qui vas bien"; 
		
	} 
	
	function log_User_Out() {
		if(isset($_SESSION['status'])) {
			unset($_SESSION['status']);
			
			if(isset($_COOKIE[session_name()])) 
				setcookie(session_name(), '', time() - 1000);
				session_destroy();
		}
	}
	
	function confirm_Member() {
		session_start();
		if($_SESSION['status'] !='authorized') header("location: login.php");
	}

	function create_user($un,$email, $pwd) {
		$mysql = New Mysql();
		$existe = $mysql->existe_Username_email($un,$email);
	
		if($existe>0) {
			return "compte deja éxistant email ou pseudo deja utilisé";
		} else {
			$mysql->create_account($un,$email, md5($pwd));
			$_SESSION['status'] = 'authorized';
			header("location: index.php");

		}

	
	
	} 
	
}
?>