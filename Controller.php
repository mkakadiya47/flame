<?php
class Controller extends Database {

	public $site_url;

	public function __construct() {
		parent::__construct();
		$this->site_state = 0;
 		$this->site_url = SITE_URL;
 		$this->mailManager = new MailManager();
 	}

	public function passwordMd5($password){
		return md5($password);
	}
   
    public function login() {
		if($_REQUEST["email"]!='' && $_REQUEST["password"]!='')
		{ 
			$where = 'WHERE (u.email = :email or u.username = :email) AND u.password = :password';
			$bind = array(
				':email' => urldecode($_REQUEST["email"]),
				':password' => $this->passwordMd5(urldecode($_REQUEST["password"]))
			);
			$query = 'SELECT u.id, u.email, u.first_name, u.last_name
			FROM user u ' . $where;
			$this->query($query);
			$this->bind($bind);
			$this->execute();
			if ($this->rowCount() > 0) {
				$result1 = $this->single();
				$this->api_status = '1';
				$this->api_message = 'Login successfully';
				$this->api_data = $result1;
				$_SESSION['username'] = urldecode($_REQUEST["email"]);
				$_SESSION['username'] = $result1["id"];
			}else{
				$this->api_status = '0';
				$this->api_message = 'Please check email and password';
				$this->api_data = '';
			}
		}else{
			$this->api_status = '0';
			$this->api_message = 'Please provide required value';
			$this->api_data = '';
		} 
		return $this->response();
 	}

   	public function register() {
		if($_REQUEST["email"]!='')
	  	{ 
		 	$where = 'WHERE u.email = :email or u.username = :username';
			$bind = array(
				':email' => urldecode($_REQUEST["email"]),
				':username' => urldecode($_REQUEST["username"])
			);
			$query = 'SELECT u.email, u.username
							FROM user u ' . $where;
			$this->query($query);
			$this->bind($bind);
			$this->execute();
			if ($this->rowCount() > 0) {
				$this->api_status = '0';
				$this->api_message = 'Email or username allready exists';
				$this->api_data = '';
			} else {
				$query = 'INSERT INTO user SET 
							first_name = :first_name,
							last_name = :last_name, 
							email = :email, 
							username = :username, 
							password = :password,
							address = :address';
				$bind = array(
					':email' => urldecode($_REQUEST["email"]),
					':password' => $this->passwordMd5(urldecode($_REQUEST["password"])),
					':username' => $_REQUEST['username'],
					':first_name' => $_REQUEST['first_name'],
					':last_name' => $_REQUEST['last_name'],
					':address' => $_REQUEST['address'],
				);
				$this->query($query);
				$this->bind($bind);
				$this->execute();
				$this->api_status = '1';
				$this->api_message = 'Register successfully';
				$this->api_data = '';
	 		}
	  	}else{
			$this->api_status = '0';
			$this->api_message = 'Please provide required value';
			$this->api_data = '';
		} 
 		return $this->response();
 	}
   	public function changePassword() {
		if($_REQUEST["id"]!='')
	  	{ 
		 	$where = 'WHERE u.id = :id';
			$bind = array(
				':id' => urldecode($_REQUEST["id"])
			);
			$query = 'SELECT u.email, u.password
							FROM user u ' . $where;
			$this->query($query);
			$this->bind($bind);
			//$this->debugQuery($query, $bind); exit;
			$this->execute();
			$result = $this->single();
			if ($this->rowCount() > 0) {
				if(isset($_REQUEST["password"])
					&& isset($_REQUEST["old_password"])
					&& $result['password'] == $this->passwordMd5($_REQUEST["old_password"])){
					$query = 'UPDATE user SET password = :password where id=:id';
					$bind = array(
						':id' => urldecode($_REQUEST["id"]),
						':password' => $this->passwordMd5(urldecode($_REQUEST["password"]))
					);
					$this->query($query);
					$this->bind($bind);
					$this->execute();
					$this->api_status = '1';
					$this->api_message = 'Password updated successfully';
					$this->api_data = 'null';
				}else {
					$this->api_status = '0';
					$this->api_message = 'Password not matched';
					$this->api_data = '';
		 		}
			} else {

				$this->api_status = '0';
				$this->api_message = 'User not found';
				$this->api_data = '';
	 		}
	  	}else{
			$this->api_status = '0';
			$this->api_message = 'Please provide required value';
			$this->api_data = '';
		} 
 		return $this->response();
 	}

   	public function editUser() {
		if($_REQUEST["id"]!='')
	  	{ 
		 	$where = 'WHERE id = :id';
			
			$bind = array();
			$query = 'UPDATE user SET 
						first_name = :first_name, 
						last_name = :last_name, 
						email = :email, 
						username = :username,
						address =:address';
			$query .= $where;
			$bind[':id'] = urldecode($_REQUEST["id"]);
			$bind[':email'] = urldecode($_REQUEST["email"]);
			$bind[':username'] = $_REQUEST['username'];
			$bind[':first_name'] = $_REQUEST['first_name'];
			$bind[':last_name'] = $_REQUEST['last_name'];
			$bind[':address'] = $_REQUEST['address'];
			$this->query($query);
			$this->bind($bind);
			$this->execute();
			$where = ' WHERE u.id = '.urldecode($_REQUEST["id"]);
			$query = 'SELECT u.id, u.first_name, u.last_name, u.email, u.username, u.address
				FROM user u ' . $where;
			$this->query($query);
			$this->execute();
			$result = $this->single();
			$this->api_status = '1';
			$this->api_message = 'User updated successfully';
			$this->api_data = $result;
	  	}else{
			$this->api_status = '0';
			$this->api_message = 'Please provide required value';
			$this->api_data = '';
		} 
 		return $this->response();
 	}	

  	public function randomPassword() 
  	{
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 6; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}

  	public function forgotPassword()
  	{
	  	if($_REQUEST['email']!='')
	  	{
			$where = ' WHERE (u.email = :email)';
			$bind = array(
			  ':email' => urldecode($_REQUEST["email"]),
			);
			$query = 'SELECT u.id, u.email FROM user u ' . $where;
			$this->query($query);
			$this->bind($bind);
			$this->execute();
		  	if ($this->rowCount() > 0) {
			 	$result = $this->single();
			 	if ($result["email"] <> '') 
			 	{
					//$from = "info@local.videoapi.mk";
					$subject = "Forgot Password";
					$to = $result['email'];
					$new_pass = $this->randomPassword();
					
					$message =  $this->generateEmailtemplate($new_pass); 
					$updquery = 'UPDATE user SET password = "'.$this->passwordMd5($new_pass).'" where email ="'.$_REQUEST['email'].'"';
					$this->query($updquery);
					$this->execute();

					$this->mailManager->sendEmail($to, $subject, $message);
					// var_dump($tt);
					$this->api_data = '';
					$this->api_status = '1';
					$this->api_message = "Your Passoword is sent to your register email address.";
			 	} 
		 	} else {
				$this->api_status = '0';
				$this->api_message = EMAIL_NOT_EXIST;
				$this->api_data = '';
				return $this->response();
		  	}
   	  	}else{
	  		 $this->api_status = '0';
			 $this->api_message = "Please provide required value";
			 $this->api_data = '';
	  	}
  	}
 
   	public function response() { // Prepare a json response to the App
        $api_status = '';
        if ($this->api_status != '')
            $api_status = $this->api_status;

        $api_message = '';
        if ($this->api_message != '')
            $api_message = $this->api_message;

        $api_data = '';
        if ($this->api_data != '')
            $api_data = $this->api_data;
		
				
	 	$this->api_return = array(
			 'status' => $api_status,
			 'message' => $api_message,
			 'flame_image_url' => FLAME_IMAGE_DOWNLOAD_URL,
			 'flame_audio_url' => FLAME_AUDIO_DOWNLOAD_URL,
			 'flame_video_url' => FLAME_VIDEO_DOWNLOAD_URL,
			 'data' => $api_data,
		 );
         return json_encode($this->api_return);
  	}
 
   	public function generateUniqueName() {
        return uniqid() . '_' . md5(mt_rand());
  	}
  	
  	public function generateEmailtemplate($new_pass="")
  	{
		$output='<h2>You recently requested a new password.</h2><b>Your new password is : </b>'.$new_pass.'</b>';
		return $output;
	}
}