<?php
define('DB_SERVER', '10.199.132.11');
define('DB_USER', 'grp5pab');
define('DB_PASSWORD', 'wKtQ898v');
define('DB_NAME', 'grp5pabdb');

//define('DB_SERVER', 'localhost');
//define('DB_USER', 'root');
//define('DB_PASSWORD', 'rootadmin');
//define('DB_NAME', 'test1');

class Mysql {
	private $conn;
	private $count;
	
	function __construct() {
		$this->conn = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME) or 
					  die('There was a problem connecting to the database.');
	}
	
	function verify_Username_and_Pass($un,$email,$pwd) {
				
		$query = "SELECT *
				FROM users
				WHERE  password = ? AND (username = ? OR email = ?)    
				LIMIT 1";
				
		if($stmt = $this->conn->prepare($query)) {
			$stmt->bind_param('sss', $pwd, $un,$email);
			$stmt->execute();
			
			if($stmt->fetch()) {
				$stmt->close();
				return true;
			}
		}
		
	}

	function existe_Username_email($un,$email) {
				
		$query = "SELECT COUNT(password) FROM users WHERE username = ? OR email = ? ;";
				
		if($stmt = $this->conn->prepare($query)) {
			$stmt->bind_param('ss', $un,$email);
			$stmt->execute();
			
			$stmt->bind_result($count);

			if($stmt->fetch()) {
				$stmt->close();
				return $count;
			}
		}
		
	}

	function create_account($un,$email,$pwd) {
				
		$query = "INSERT INTO `users` (`id`, `username`, `password`, `email`) VALUES (NULL, ?, ?, ?);";
				
		if($stmt = $this->conn->prepare($query)) {
			$stmt->bind_param('sss', $un,$pwd, $email);
			$stmt->execute();
			
			if($stmt->fetch()) {
				$stmt->close();
				return true;
			}
		}
		
	}

}
?>
