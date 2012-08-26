<?php

/**
* $Id$
* Central profile PHP based API implementation.
* NOTE: this service is using HTTP based API of CentralProfile web service.
*/
class Centralprofile_Local {

  var $UserName 				  = "";
  var $Password 				  = "";
  var $PUID					      = "CP";
  var $PUIDWAP                    = "7gZKT12hyVzaWLKBhr2HiQ==";
  var $MCURL                      = "http://203.202.240.75/mcommerce/service.asmx/";
  var $FirstName				  = "";
  var $LastName				    = "";
  var $MobileNumber			  = "";
  var $SecondMobileNumber = "";
  var $ThirdMobileNumber	= "";
  var $Address					  = "";
  var $City					      = "";
  var $PostCode				    = "";
  var $Country					  = "";
  var $Gender					    = "";
  var $DateOfBirth				= "";
  var $Email					    = "";
  var $NewUserName				= "";
  var $CPURL					    = "http://203.202.240.75/CProfile/CPWebService.asmx";
  var $ErrorCode          = "";

  /**
  * Default constructor whch initiates central profile default configurations
  */
  public function __construct($p_puid = null, $p_wsdl_uri = null) {

  }

  /**
  * Perform web service request and return the responsed content.
  */
  public function CPaction($p_action, $p_params, $p_url = "") {
    $http_client = curl_init();

    # determine action url
    if ($p_url && !empty($p_url)) {
      $action_url = $p_url . $p_action;
    } else {
      $action_url = $this->CPURL . $p_action;
    }
    
    # set request url
    curl_setopt($http_client, CURLOPT_URL, $action_url);

    # set request method as POST
    curl_setopt($http_client, CURLOPT_POST, 1);

    # set post parameters
    curl_setopt($http_client, CURLOPT_POSTFIELDS, $p_params);
    curl_setopt($http_client, CURLOPT_RETURNTRANSFER, 1);

    # execute request
    $response = curl_exec($http_client);
    curl_errno($http_client);

    # cleanup resource
    curl_close($http_client);

    return $response;
  }

  private function CP_ParseResponse($response, $from = "") {
    if ("Input string was not in a correct format." == $response || empty($response)) {
      return 0;
    }

    try {
      $dom = new domDocument;
      $dom->loadXML($response);
      if (!$dom) {
        return 0;
      }

      $s = simplexml_import_dom($dom);
      if ($s->string) {
        if (isset($s->string[1])) {
          $this->ErrorCode = $s->string[1];
        }

        if ($from == "Authentication" && $s->string[0] == 0 && $s->string[1] > 0) {
          return $s->string[1];
        }
        return $s->string[0];
      } else {
        return strip_tags($s, '<boolean>');
      }
    } catch (Exception $e) {
      return 0;
    }
  }

   private function CP_CheckUserName($username)
	{
		$params = "userName=".$username;
		$result = $this->CPaction("CheckUserNameAvailability", $params);
		return $this->CP_ParseResponse($result);
	}
	
   private function CP_CheckUsernameFormat($username)
	{
		$params = "username=".$username;
		$result = $this->CPaction("CheckUsernameFormat", $params);
		return $this->CP_ParseResponse($result);
	}

   private function CP_CheckEmail($email)
	{
		$params = "email=".$email;
		$result = $this->CPaction("IsUserEmailExist", $params);
		return $this->CP_ParseResponse($result);
	}

	private function CP_verifyUserAccount($username)
	{
		$params = "username=".$username."&PUID=".$this->PUID;
		$result = $this->CPaction("VerifyUserAccount", $params);
		return $this->CP_ParseResponse($result);
	}

   private function CP_AuthenticationFromCP($username,$password)
    {

		$params = "PUID=".$this->PUID."&userName=".$username."&pass=".$password;
		$result = $this->CPaction("AuthenticationFromCP", $params);
		return $this->CP_ParseResponse($result ,'Authentication');
    }



	private function CP_Logout($username)
	 {
		$params = "username=".$username;
		$result = $this->CPaction("LogoutFromCP", $params);
		return $this->CP_ParseResponse($result);
	 }

	private function CP_CheckMobileNumber($mobilenumber)
	 {
		$params = "mobileNumber=".$mobilenumber;
		//for the time being call this to local CP only for updated result
		$p_url = 'http://221.120.98.67/cprofile/cpwebservice.asmx/';
		$result = $this->CPaction("CheckUserMobileNumberAvailablity", $params, $p_url);
		return $this->CP_ParseResponse($result);
	 }


	private function CP_UserState($username)
	 {
		$params = "username=".$username;
		$result = $this->CPaction("IsUserLoggedIn", $params);
		return $this->CP_ParseResponse($result);
	 }


	private function CP_ChangeUserName($username, $password, $newusername)
	 {
		$params = "username=".$username."&password=".$password."&newname=".$newusername;
		$result = $this->CPaction("ChangeUserNameForMobileUser", $params);
		return $this->CP_ParseResponse($result);
	 }

	 private function CP_ChangeUserName2($username, $newusername)
	 {
		$params = "currentUsername=".$username."&newUsername=".$newusername."&PUID=".$this->PUID;
		$result = $this->CPaction("ChangeUsername", $params);
		log_message('debug', 'CP:ChangeUserName2 Response - ' . $result);
		return $this->CP_ParseResponse($result);
	 }


	private function CP_RegisterUser($username, $password, $firstname,$lastname,$cellnumber,$cell2,$cell3,$address,$city,$postcode,$country,$gender,$dob,$phone,$email, $regType)
	 {
	 	$PUID = ( 'wap' == $regType ) ? $this->PUIDWAP : $this->PUID;
		
	 	$params = "username=".$username."&password=".$password."&firstName=".$firstname;
		$params .= "&lastName=".$lastname."&cell=".$cellnumber."&cell2=".$cell2;
		$params .= "&cell3=".$cell3."&address=".$address."&city=".$city;
		$params .= "&postcode=".$postcode."&country=".$country."&gender=3";
		$params .= "&dateofbirth=".$dob."&phone=".$phone."&email=".$email."&PUID=".$PUID."&state=''";
		
		$result = $this->CPaction("RegisterUser", $params);
		return $this->CP_ParseResponse($result);
	 }

     private function CP_RegisterUserWap($username, $password, $mobileNumber)
	 {
		$params = "username=".$username."&password=".$password."&mobileNumber=".$mobileNumber;
		$params .= "&PUID=".$this->PUIDWAP;
		$result = $this->CPaction("RegisterUserForWAP", $params);
		return $this->CP_ParseResponse($result);
	 }
         private function CP_GPStartBundle( $mobileNumber)
	 {
		$params = "mobileNumber=".$mobileNumber;
		$result = $this->CPaction("GPStartBundle", $params);
		return $this->CP_ParseResponse($result);
	 }	 

    private function CP_Delete($username)
     {
        $params = "username=".$username."&PUID=".$this->PUID;
        $result = $this->CPaction("DeleteUser", $params);
        return $this->CP_ParseResponse($result);
     }

     private function CP_PassFromUsername($username)
     {
        $params = "userName=".$username;
        $result = $this->CPaction("GetPasswordFromUsername", $params);
        return $this->CP_ParseResponse($result);
     }
     
     private function CP_GetHash($input)
     {
        $params = "input=".$input;
        $result = $this->CPaction("GetHash", $params);
        return $this->CP_ParseResponse($result);
     }


    private function CP_UpdatePassword($username,$oldPassword,$password)
     {
        $params = "userName=".$username."&oldPassword=".$oldPassword."&newPassword=".$password;
        //$result = $this->CPaction("ResetPassword", $params);
        $result = $this->CPaction("UpdatePasswordForUser", $params);
        return $this->CP_ParseResponse($result);
     }

    private function CP_ResetPassword($username,$password)
     {
        $params = "username=".$username."&password=".$password;
        //$result = $this->CPaction("ResetPassword", $params);
        $result = $this->CPaction("ResetPassword", $params);
        return $this->CP_ParseResponse($result);
     }

    private function CP_GetUser($username) {
      $params = "userName=".$username;
      $response = $this->CPaction("GetUserInfomationFromCP", $params);
      $user_attributes = array();
      if ($response) {
        $root = new SimpleXMLElement($response);
        if ($root) {
          $profile_xml = (string) $root;
          $profile_xml = new SimpleXMLElement($profile_xml);
          if ($profile_xml) {
            foreach ($profile_xml->xpath('//UserInfo/*') as $field) {
              $user_attributes[$field->getName()] = (string) $field;
            }
          }
        }
      }
      return $user_attributes;
    }

    private function CP_MergeUserAccount($p_current_user, $p_existing_user, $p_existing_user_password) {
      log_message('debug', "CP:MergeUserAccount - $p_current_user, $p_existing_user, $p_existing_user_password");

      # verify parameters
      if (empty($p_current_user) || empty($p_existing_user) || empty($p_existing_user_password)) {
        return false;
      }

      # verify same user
      if ($p_current_user == $p_existing_user) {
        return false;
      }

      # build request parameter
      $params = "currentUsername=$p_current_user&existingUsername=$p_existing_user&existingPassword=$p_existing_user_password&PUID=" . $this->PUID;
      $response = $this->CPaction("MergeUserAccount", $params);
      log_message('debug', 'CP:MergerUserAccount Response - ' . $response);

      if ($response) {
        return $this->CP_ParseResponse($response);
      }
      return false;
    }

  // all public function for accessing CP functions.
  public function GetUser($p_username) {
    return $this->CP_GetUser($p_username);
  }

  public function CheckUserName($username)
    {
			return $this->CP_CheckUserName($username);
    }
    
  public function CheckUsernameFormat($username)
  {
			return $this->CP_CheckUsernameFormat($username);
  }

   public function CheckMobileNumber($mobilenumber)
    {
			return $this->CP_CheckMobileNumber($mobilenumber);
    }

   public function RegisterUser($username, $password, $firstname,$lastname,$cellnumber,$cell2,$cell3,$address,$city,$postcode,$country,$gender,$dob,$phone,$email, $regType='')
    {
			return $this->CP_RegisterUser($username, $password, $firstname,$lastname,$cellnumber,$cell2,$cell3,$address,$city,$postcode,$country,$gender,$dob,$phone,$email, $regType);
    }

    public function RegisterUserWap($username, $password, $mobileNumber)
    {
        
        return $this->CP_RegisterUserWap($username, $password, $mobileNumber);

    }

    public function GPStartBundle( $mobileNumber)
    {
			return $this->CP_GPStartBundle( $mobileNumber);
    }
   public function ChangeUserName($username, $password, $newusername)
    {
		return $this->CP_ChangeUserName($username, $password, $newusername);
    }

    public function Authentication($username,$password)
    {
		return $this->CP_AuthenticationFromCP($username,$password);
    }

    public function UserState($username)
    {
		$this->CP_UserState($username);
    }

    public function Logout($username)
     {
		return $this->CP_Logout($username);
     }

    public  function UserDelete($username)
     {
       return $this->CP_Delete($username);
     }

    public function GetError()
     {
        return $this->ErrorCode;
     }

    public function UpdatePassword($username,$oldPassword,$password)
     {
        return $this->CP_UpdatePassword($username,$oldPassword,$password);
     }

     public function ResetPassword($username,$password)
     {
        return $this->CP_ResetPassword($username,$password);
     }
    public  function GetPasswordFromUsername($username)
     {
       return $this->CP_PassFromUsername($username);
     }
    public  function GetHash($input)
     {
       return $this->CP_GetHash($input);
     }
    public  function CheckEmail($email)
     {
       return $this->CP_CheckEmail($email);
     }
     
	public  function verifyUserAccount($username)
     {
       return $this->CP_verifyUserAccount($username);
     }

  public function ChangeUserName2($p_username, $p_new_username) {
    return $this->CP_ChangeUserName2($p_username, $p_new_username);
  }

  public function MergeUserAccount($p_current_user, $p_existing_user, $p_existing_user_password) {
    return $this->CP_MergeUserAccount($p_current_user, $p_existing_user, $p_existing_user_password);
  }
  
  
  /*
   * CP WAP registration related methods
   *
   */

  public function RegistrationForWAPWithBundle( $username, $password, $mobileNumber )
  {
  	return $this->CP_RegistrationForWAPWithBundle($username, $password, $mobileNumber);
  }
  
  private function CP_RegistrationForWAPWithBundle($username, $password, $mobileNumber)
  {
  	 $params = "username=".$username."&password=".$password."&mobileNumber=".$mobileNumber;
  	 $params .= "&PUID=".$this->PUIDWAP;
  	 $p_url = $this->MCURL; //for the time being, call to MC web service URL
  	 
  	 $result = $this->CPaction("RegistrationForWAPWithBundle", $params, $p_url);
  	 return $this->CP_ParseResponse($result);
  }


  public function RegistrationForWAPWithoutBundle( $username, $password, $mobileNumber )
  {
  	return $this->CP_RegistrationForWAPWithoutBundle( $username, $password, $mobileNumber );
  }
  
  
  private function CP_RegistrationForWAPWithoutBundle($username, $password, $mobileNumber)
  {
  	$params = "username=".$username."&password=".$password."&mobileNumber=".$mobileNumber;
  	$params .= "&PUID=".$this->PUIDWAP;
  	$p_url = $this->MCURL; //for the time being, call to MC web service URL
  	$result = $this->CPaction("RegistrationForWAPWithoutBundle", $params, $p_url);
  	//echo '<pre>' . print_r($result,true) . '</pre>';
  	return $this->CP_ParseResponse($result);
  }
  
  
  /*
  public function CheckMobileNumberFromMC($mobilenumber)
  { 
  	return $this->CP_CheckMobileNumberFromMC($mobilenumber);
  }

  
  private function CP_CheckMobileNumberFromMC($mobilenumber)
  {
  	$params = "mobileNumber=".$mobilenumber;
  	$result = $this->CPaction("CheckUserMobileNumberAvailablity", $params);
  	return $this->CP_ParseResponse($result);
  }
  */
  
  
  /*
   * it's a sepcial OPEN ID link which is using for
   * wap email registration for sending activation link
   *
   */
 
  public function SendActivationMail($p_username, $p_email)
  {
  	 return $this->CP_SendActivationMail($p_username, $p_email);
  }
  
 
  private function CP_SendActivationMail($username, $email)
  {
  	$params = 'username='.$username.'&email='.$email;
  	//$p_url = "http://profile.somewhereinbangladesh.net/WebService.asmx?op=SendActivationlink";
  	$p_url = "http://profile.somewhereinbangladesh.net/WebService.asmx/SendActivationlink";
  	$p_action = '';
  	$result = $this->CPaction($p_action, $params, $p_url);
  	return $this->CP_ParseResponse($result);
  }

  /*
   * Change user status from admin panel when block from cp checked
   */
  
  public function  CP_ChangeUserActiveStatus($p_username, $p_status){
      $params = "userName=".$p_username."&PUID=".$this->PUID."&status=".$p_status;
      $result = $this->CPaction("changeUserActiveStatus", $params);
      return $this->CP_ParseResponse($result);
  }

}
?>
