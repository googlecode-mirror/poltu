<?php
class Actions {
	function register($params) {

		$username = $params['username'];
		$password = $params['password'];
		$email  = $params['email'];
		
		$firstname = "";
		$lastname = "";
		$cellnumber = "";
		$cell2 = "";
		$cell3 = "";
		$address = "";
		$city = "";
		$postcode = "";
		$country = "";
		$gender = "";
		$dob = "";
		$phone = "";
		$regType = "web";
		$result = array();
		return $result;
	}
} 
?>