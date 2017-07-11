<?php
define("ITEM_ZISE", 20);
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
			$query = 'SELECT u.id, u.email, u.first_name, u.last_name, u.image, u.address, u.username, u.country
			FROM user u ' . $where;
			$this->query($query);
			$this->bind($bind);
			$this->execute();
			if ($this->rowCount() > 0) {
				$result1 = $this->single();
				if(isset($_REQUEST['device_token']) && isset($_REQUEST['device'])){
					$query = "UPDATE user u set u.device_token= :device_token, u.device = :device where u.id=".$result1['id'];
					$bind = array(
						':device_token' => $_REQUEST["device_token"],
						':device' => $_REQUEST["device"]
					);
					$this->query($query);
					$this->bind($bind);
					$this->execute();
				}
				$this->api_status = '1';
				$this->api_message = 'Login successfully';
				$this->api_data = $result1;
			}else{
				$this->api_status = '0';
				$this->api_message = 'Email and password combination is not correct.';
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
				$this->api_message = 'This email is already registered with us please try to register with different email.';
				$this->api_data = '';
			} else {
				$query = 'INSERT INTO user SET 
							first_name = :first_name,
							last_name = :last_name, 
							email = :email, 
							username = :username, 
							password = :password,
							device = :device,
							device_token = :device_token,
							country = :country,
							address = :address';
				$bind = array(
					':email' => urldecode($_REQUEST["email"]),
					':password' => $this->passwordMd5(urldecode($_REQUEST["password"])),
					':username' => $_REQUEST['username'],
					':first_name' => $_REQUEST['first_name'],
					':last_name' => $_REQUEST['last_name'],
					':address' => $_REQUEST['address'],
					':device' => $_REQUEST['device'],
					':device_token' => $_REQUEST['device_token'],
					':country' => $_REQUEST['country'],
				);
				$this->query($query);
				$this->bind($bind);
				$this->execute();
				$query = 'SELECT u.id, u.first_name, u.last_name, u.email, u.username, u.country, u.address, u.image
					FROM user u where u.email="' .$_REQUEST["email"].'"';
				$this->query($query);
				$this->execute();
				$result = $this->single();

				$this->api_status = '1';
				$this->api_message = 'Register successfully';
				$this->api_data = $result;
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
					$this->api_data = '';
				}else {
					$this->api_status = '0';
					$this->api_message = 'Old Password does not match';
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
		 	$where = ' WHERE id = :id';
			
			$bind = array();
			$image = null;
			if(isset($_REQUEST['image']) && $_REQUEST['image']){
				$image = $this->createUserImage($_REQUEST['image']);
			}
			$query = 'UPDATE user SET 
						first_name = :first_name, 
						last_name = :last_name, 
						email = :email, 
						username = :username,
						image = :image,
						country = :country,
						address =:address';
			$query .= $where;
			$bind[':id'] = urldecode($_REQUEST["id"]);
			$bind[':email'] = urldecode($_REQUEST["email"]);
			$bind[':username'] = $_REQUEST['username'];
			$bind[':first_name'] = $_REQUEST['first_name'];
			$bind[':country'] = $_REQUEST['country'];
			$bind[':last_name'] = $_REQUEST['last_name'];
			$bind[':address'] = $_REQUEST['address'];
			$bind[':image'] = $image;
			$this->query($query);
			$this->bind($bind);
			$this->execute();
			$where = ' WHERE u.id = '.urldecode($_REQUEST["id"]);
			$query = 'SELECT u.id, u.first_name, u.last_name, u.email, u.username, u.address, u.image
				FROM user u ' . $where;
			$this->query($query);
			$this->execute();
			$result = $this->single();
			$this->api_status = '1';
			$this->api_message = 'Profile updated successfully';
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

					// $userData['to'] = $to;
					// $userData['subject'] = $subject;
					// $userData['message'] = $message;
					// $userData = json_encode($userData);

					// $postdata = http_build_query(
					//     array(
					//         'userData' => $userData
					//     )
					// );

					// $opts = array('http' =>
					//     array(
					//         'method'  => 'POST',
					//         'header'  => 'Content-type: application/x-www-form-urlencoded',
					//         'content' => $postdata
					//     )
					// );

					// $context  = stream_context_create($opts);

					// $result = file_get_contents('http://flame.appsextent.com/sendEmail.php', false, $context);
					// var_dump($result);exit;
					$this->mailManager->sendEmail($to, $subject, $message);
					// var_dump($tt);
					$this->api_data = '';
					$this->api_status = '1';
					$this->api_message = "Your Passoword is sent to your register email address.";
					return $this->response();
			 	} 
		 	} else {
				$this->api_status = '0';
				$this->api_message = 'We are not able to find you email address in our record.';
				$this->api_data = '';
				return $this->response();
		  	}
   	  	}else{
	  		 $this->api_status = '0';
			 $this->api_message = "Please provide required value";
			 $this->api_data = '';
			 return $this->response();
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
        if ($this->api_data != ''){

            $api_data = $this->arrayRemoveNull($this->api_data);
        }
        if (!isset($this->totalRecords))
            $this->totalRecords = 0;
		
				
	 	$this->api_return = array(
			 'status' => $api_status,
			 'message' => $api_message,
			 'flame_image_url' => FLAME_IMAGE_DOWNLOAD_URL,
			 'flame_audio_url' => FLAME_AUDIO_DOWNLOAD_URL,
			 'flame_video_url' => FLAME_VIDEO_DOWNLOAD_URL,
			 'user_image_url' => USER_IMAGE_DOWNLOAD_URL,
			 'totalRecords' => $this->totalRecords,
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

	public function addFlame(){
		$query = 'INSERT INTO flame SET 
					created_at = NOW(),
					updated_at = NOW(),
					title = :title,
					website = :website, 
					category_id = :category_id, 
					mobile = :mobile, 
					address = :address, 
					latitude = :latitude,
					country = :country,
					longitude = :longitude';
		$bind = array(
			':title' => $_REQUEST["title"],
			':website' => $_REQUEST["website"],
			':mobile' => $_REQUEST['mobile'],
			':address' => $_REQUEST['address'],
			':latitude' => $_REQUEST['latitude'],
			':longitude' => $_REQUEST['longitude'],
			':country' => $_REQUEST['country'],
			':category_id' => $_REQUEST['category_id'],
		);
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$flameId = $this->lastInsertId();
		$this->setUserFlame($flameId, true);
		$this->api_status = '1';
		$this->api_message = 'Flame added successfully';
		$this->api_data = '';
 		return $this->response();
	}

	public function addUserFlame(){
		$this->setUserFlame($_REQUEST['flame_id']);
		$this->api_status = '1';
		$this->api_message = 'Flame added successfully';
		$this->api_data = '';
 		return $this->response();
	}
	public function addFlameImage(){
		$this->setFlameImage($_REQUEST['user_flame_id']);
		$this->api_status = '1';
		$this->api_message = 'Flame image added successfully';
		$this->api_data = '';
 		return $this->response();
	}
	public function addFlameAudio(){
		$this->setFlameAudio($_REQUEST['user_flame_id']);
		$this->api_status = '1';
		$this->api_message = 'Flame added successfully';
		$this->api_data = '';
 		return $this->response();
	}
	public function addFlameVideo(){
		$this->setFlameVideo($_REQUEST['user_flame_id']);
		$this->api_status = '1';
		$this->api_message = 'Flame video added successfully';
		$this->api_data = '';
 		return $this->response();
	}

	public function addFollower(){
		$query = 'INSERT INTO follower SET 
							user_id = :user_id,
							follower_id = :follower_id
						';
		$bind = array(
			':user_id' => $_REQUEST["user_id"],
			':follower_id' => $_REQUEST['follower_id'],
		);
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$this->api_status = '1';
		$this->api_message = 'Added successfully';
		$this->api_data = '';
 		return $this->response();
	}

	public function removeFollower(){
		$query = 'DELETE FROM follower WHERE 
							user_id = :user_id and 
							follower_id = :follower_id
						';
		$bind = array(
			':user_id' => $_REQUEST["user_id"],
			':follower_id' => $_REQUEST['follower_id'],
		);
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$this->api_status = '1';
		$this->api_message = 'Remove successfully';
		$this->api_data = '';
 		return $this->response();
	}

	public function addLike(){
		$query = 'INSERT INTO user_like SET 
							user_id = :user_id,
							user_flame_id = :user_flame_id
						';
		$bind = array(
			':user_id' => $_REQUEST["user_id"],
			':user_flame_id' => $_REQUEST['user_flame_id'],
		);
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$this->api_status = '1';
		$this->api_message = 'Added successfully';
		$this->api_data = '';
 		return $this->response();
	}
	public function removeLike(){
		$query = 'DELETE from user_like where 
							user_id = :user_id and
							user_flame_id = :user_flame_id
						';
		$bind = array(
			':user_id' => $_REQUEST["user_id"],
			':user_flame_id' => $_REQUEST['user_flame_id'],
		);
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$this->api_status = '1';
		$this->api_message = 'Remove successfully';
		$this->api_data = '';
 		return $this->response();
	}

	private function setUserFlame($flameId, $isSetOwner = false){
		$query = 'INSERT INTO user_flame SET 
							flame_id = :flameId,
							user_id = :user_id, 
							description = :description
						';
		$bind = array(
			':flameId' => $flameId,
			':user_id' => $_REQUEST["user_id"],
			':description' => $_REQUEST['description'],
		);
		$this->query($query);
		$this->bind($bind);
		$this->execute();

		$userFlameId = $this->lastInsertId();
		$this->setFlameImage($userFlameId);
		$this->setFlameAudio($userFlameId);
		$this->setFlameVideo($userFlameId);
		if($isSetOwner){
			$query = 'UPDATE flame f SET 
							owner_id = :owner_id 
							Where f.id = :flameId
						';
			$bind = array(
				':flameId' => $flameId,
				':owner_id' => $_REQUEST["user_id"]
			);
			$this->query($query);
			$this->bind($bind);
			$this->execute();
		}
		
	}

	private function setFlameImage($userFlameId){
		if(isset($_REQUEST['images']) && $_REQUEST['images']){
			$images = json_decode($_REQUEST['images'], true);
			foreach ($images as $key => $image) {
				$imageName = $this->createImage($image);
				$query = 'INSERT INTO flame_image SET 
							image = :image,
							user_id = :user_id, 
							user_flame_id = :user_flame_id
						';
				$bind = array(
					':image' => $imageName,
					':user_id' => $_REQUEST["user_id"],
					':user_flame_id' => $userFlameId,
				);
				$this->query($query);
				$this->bind($bind);
				$this->execute();
			}
		}
	}

	private function setFlameAudio($userFlameId){
		if(isset($_REQUEST['audio']) && $_REQUEST['audio']){
			$audioName = $this->createAudio($_REQUEST['audio']);
			$query = 'INSERT INTO flame_audio SET 
						audio = :audio,
						user_id = :user_id, 
						user_flame_id = :user_flame_id
					';
			$bind = array(
				':audio' => $audioName,
				':user_id' => $_REQUEST["user_id"],
				':user_flame_id' => $userFlameId,
			);
			$this->query($query);
			$this->bind($bind);
			$this->execute();
		}
	}

	private function setFlameVideo($userFlameId){
		if(isset($_REQUEST['video']) && $_REQUEST['video']){
			$videoName = $this->createVideo($_REQUEST['video']);
			$query = 'INSERT INTO flame_video SET 
						video = :video,
						user_id = :user_id, 
						user_flame_id = :user_flame_id
					';
			$bind = array(
				':video' => $videoName,
				':user_id' => $_REQUEST["user_id"],
				':user_flame_id' => $userFlameId,
			);
			$this->query($query);
			$this->bind($bind);
			$this->execute();
		}
	}

	private function createUserImage($image){
		$format = isset($_REQUEST['image_format']) ? $_REQUEST['image_format'] : 'png';
		$imageName = $this->generateUniqueName().'.'. $format ;
		$img = base64_decode($image);
		file_put_contents(USER_IMAGE_UPLOAD_URL.$imageName, $img);
		return $imageName;
	}
	private function createImage($image){
		$format = isset($_REQUEST['image_format']) ? $_REQUEST['image_format'] : 'png';
		$imageName = $this->generateUniqueName().'.'. $format ;
		$img = base64_decode($image);
		file_put_contents(FLAME_IMAGE_UPLOAD_URL.$imageName, $img);
		return $imageName;
	}
	private function createAudio($audio){
		$format = isset($_REQUEST['audio_format']) ? $_REQUEST['audio_format'] : 'mp3';
		$audioName = $this->generateUniqueName().'.'. $format ;
		$audio = base64_decode($audio);
		file_put_contents(FLAME_AUDIO_UPLOAD_URL.$audioName, $audio);
		return $audioName;
	}
	private function createVideo($video){
		$format = isset($_REQUEST['video_format']) ? $_REQUEST['video_format'] : 'mp4';
		$videoName = $this->generateUniqueName().'.'. $format ;
		$video = base64_decode($video);
		file_put_contents(FLAME_VIDEO_UPLOAD_URL.$videoName, $video);
		return $videoName;
	}

   	public function getFlames() {
   		$currentDate = new \DateTime('now');
   		$currentDate->modify('-1 day');
   		$pageNo = isset($_REQUEST['page'])?$_REQUEST['page']:1;
   		$allRecord = isset($_REQUEST['all_record'])?$_REQUEST['all_record']:0;
		$query = 'SELECT 
						f.id,
						f.title,
						f.website,
						f.mobile,
						f.address,
						f.latitude,
						f.longitude,
						f.owner_id,
						f.owner_id,
						f.country,
						f.created_at,
						f.updated_at,
						( select count(v.id) from flame_video v join user_flame uf on uf.id = v.user_flame_id where uf.flame_id = f.id) as video_counter,
						( select count(fa.id) from flame_audio fa join user_flame uf on uf.id = fa.user_flame_id where uf.flame_id = f.id) as audio_counter,
						unix_timestamp(f.updated_at) as updated_timestamp,
						f.view_counter,
						LENGTH(f.view_counter) - LENGTH(REPLACE(f.view_counter, ",", "")) + 1 as viewer,
						(select fi.image from flame_image fi join user_flame uf on uf.id = fi.user_flame_id where uf.flame_id = f.id limit 1) as image, 
						f.category_id,
						(select count(uf.id) as flamedBy from user_flame uf where uf.flame_id = f.id ) as flamedBy,
						(
							SELECT count(DISTINCT l.id) as likes FROM user_like l 
							JOIN user_flame cuf on cuf.id = l.user_flame_id 
							where cuf.flame_id=f.id
						) AS likes
						';
		$fromQuery = ' From flame f where f.updated_at >= "'.$currentDate->format('Y-m-d H:i:s').'"';
		if(isset($_REQUEST['category_id']) && $_REQUEST['category_id']){
			$fromQuery .= ' and f.category_id = '.$_REQUEST['category_id'];
		}
		if(isset($_REQUEST['country']) && $_REQUEST['country']){
			$fromQuery .= ' and f.country = "'.$_REQUEST['country'].'"';
		}
   		if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'recent'){
			$fromQuery .= ' order by f.updated_at DESC';
   		}elseif(isset($_REQUEST['type']) && $_REQUEST['type'] == 'nearby'){
   			if( !isset($_REQUEST['latitude']) || !isset($_REQUEST['latitude']) ){
   				$this->api_status = '0';
				$this->api_message = 'Please provide lat long';
				$this->api_data = '';
			 	return $this->response();
   			}
   			// miles 3959
   			//km 6371
   			$query .= ",( 3959 * acos( cos( radians(".$_REQUEST['latitude'].") ) * cos( radians( f.latitude ) ) * 
							cos( radians( f.longitude ) - radians(".$_REQUEST['longitude'].") ) + sin( radians(".$_REQUEST['latitude']." ) ) * 
							sin( radians( f.latitude) ) ) ) AS distance";
   			
			$fromQuery .= ' HAVING distance < 25 ORDER BY distance';
   		}elseif(isset($_REQUEST['type']) && $_REQUEST['type'] == 'popular'){
			$fromQuery .= ' HAVING likes > 0 ORDER BY likes DESC';
   		}
   		$query .= $fromQuery;
		$this->query($query);
		$this->execute();
		$this->totalRecords = $this->rowCount();
		if(!$allRecord){
			$offset = ITEM_ZISE * ($pageNo-1);
			$query .= " limit ".$offset .", ".ITEM_ZISE;
			$this->query($query);
			$this->execute();
		}
		$results = $this->resultset();
		if (count($results) > 0) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			foreach($results as $key => $value)
			{
				$results[$key]['owner_detail']=$this->getUser($value['owner_id']);
			}
			$this->api_data = $results;
		} else {
			$this->api_status = '0';
			$this->api_message = 'Flame not found';
			$this->api_data = '';
		}
	 	return $this->response();
  	}

   	public function getCategory() {
		$query = 'SELECT 
						c.id,
						c.name FROM category c';
		$this->query($query);
		$this->execute();
		$results = $this->resultset();
		if (count($results) > 0) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $results;
		} else {
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
  	}

   	public function getFlameUsers() {
   		$query = 'SELECT f.view_counter FROM flame f where f.id = '.$_REQUEST['flame_id'];
		$this->query($query);
		$this->execute();
		$result = $this->single();
		$viewer = explode(',', $result['view_counter']);
		$viewer[] = $_REQUEST['user_id'];
		$query = 'UPDATE flame f set f.view_counter = :view_counter where f.id = '.$_REQUEST['flame_id'];
		$bind = array(
					'view_counter' => implode(',', array_unique(array_filter($viewer)))
				);
		$this->query($query);
		$this->bind($bind);
		$this->execute();

   		$pageNo = isset($_REQUEST['page'])?$_REQUEST['page']:1;

		$query = 'SELECT 
						uf.id,
						uf.description,
						uf.user_id,
						u.username,
						u.first_name, 
						u.last_name, 
						u.address,
						f.country,
						f.created_at,
						f.updated_at,
						unix_timestamp(f.updated_at) as updated_timestamp,
						f.view_counter,
						u.image,
						f.website,
						f.title,
						f.latitude,
						f.longitude,
						f.category_id,
						f.address as flame_address,
						f.mobile,
						(select fi.image from flame_image fi where fi.user_flame_id = uf.id limit 1) as flame_image,
						(
							SELECT count(DISTINCT l.id) as likes FROM user_like l
							where l.user_flame_id=uf.id
						) AS likes,
						(select fa.audio from flame_audio fa where fa.user_flame_id = uf.id limit 1) as flame_audio,
						(select fv.video from flame_video fv where fv.user_flame_id = uf.id limit 1) as flame_video,
						(select count(DISTINCT ul.user_flame_id) as userCount from user_like ul where ul.user_id = '.$_REQUEST['user_id'].' and ul.user_flame_id = uf.id) as like_status
						FROM user_flame uf 
						JOIN flame f ON f.id = uf.flame_id
						JOIN user u on u.id = uf.user_id 
						where uf.flame_id='.$_REQUEST['flame_id'].' order by uf.id DESC';
		$this->query($query);
		$this->execute();
		$this->totalRecords = $this->rowCount();
		$offset =ITEM_ZISE *($pageNo-1);
		$query .= " limit ".$offset .", ".ITEM_ZISE;
		$this->query($query);
		$this->execute();
		$results = $this->resultset();
		
		if (count($results) > 0) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $results;
		} else {	
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
  	}

   	public function getUserFlames() {
   		$pageNo = isset($_REQUEST['page'])?$_REQUEST['page']:1;
		$query = 'SELECT 
						uf.id,
						uf.description,
						uf.user_id,
						u.username,
						u.first_name, 
						u.last_name, 
						u.address,
						u.country,
						u.image,
						f.website,
						f.title,
                        f.id as flame_id,
						f.owner_id,
						f.latitude,
						f.longitude,
						f.category_id,
						f.country,
						f.created_at,
						f.updated_at,
						unix_timestamp(f.updated_at) as updated_timestamp,
						f.view_counter,
						f.address as flame_address,
						f.mobile,
						(
							SELECT count(DISTINCT l.id) as likes FROM user_like l
							where l.user_flame_id=uf.id
						) AS likes,
						(select fa.audio from flame_audio fa where fa.user_flame_id = uf.id limit 1) as flame_audio,
						(select fv.video from flame_video fv where fv.user_flame_id = uf.id limit 1) as flame_video,
						(select count(DISTINCT ul.user_flame_id) as userCount from user_like ul where ul.user_id = '.$_REQUEST['login_user_id'].' and ul.user_flame_id = uf.id) as like_status
						FROM user_flame uf 
						JOIN flame f ON f.id = uf.flame_id
						JOIN user u on u.id = uf.user_id 
						where uf.user_id='.$_REQUEST['user_id'].' order by uf.id DESC';
		$this->query($query);
		$this->execute();
		$this->totalRecords = $this->rowCount();
		$offset =ITEM_ZISE *($pageNo-1);
		$query .= " limit ".$offset .", ".ITEM_ZISE;
		$this->query($query);
		$this->execute();
		$results = $this->resultset();

		$arrayResults = array();
		foreach ($results as $result) {
			$query = "select fi.image from flame_image fi where fi.user_flame_id = ".$result['id'];
			$this->query($query);
			$this->execute();
			$result['flame_image'] = array_map('current', $this->resultset());
			$arrayResults[] = $result;
		}
		if (count($arrayResults) > 0) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $arrayResults;
		} else {
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
  	}

   	public function getLikes() {
		$query = 'SELECT 
						count(DISTINCT l.id) as likes
						FROM user_like l 
						JOIN user_flame uf on uf.id = l.user_flame_id 
						where uf.flame_id='.$_REQUEST['flame_id'];
		$this->query($query);
		$this->execute();
		$results = $this->single();
		if (count($results) > 0) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $results;
		} else {
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
  	}

   	public function getFollower() {
		$query = 'SELECT 
						u.id,
						u.username,
						u.first_name, 
						u.last_name, 
						u.address, 
						u.image,
						u.email,
						u.country
						FROM user u
						JOIN follower f on f.user_id = u.id where f.follower_id ='.$_REQUEST['user_id'];
		$this->query($query);
		$this->execute();
		$results = $this->resultset();
		if (count($results) > 0) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $results;
		} else {
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
  	}

   	public function getFollowing() {
		$query = 'SELECT 
						u.id,
						u.username,
						u.first_name, 
						u.last_name, 
						u.address, 
						u.image,
						u.email,
						u.country
						FROM user u
						JOIN follower f on f.follower_id = u.id where f.user_id ='.$_REQUEST['user_id'];
		$this->query($query);
		$this->execute();
		$results = $this->resultset();
		if (count($results) > 0) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $results;
		} else {
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
  	}

   	public function getComments() {
		$query = 'SELECT 
						uc.id,
						uc.user_flame_id,
						uc.user_id, 
						u.username, 
						u.first_name, 
						u.last_name, 
						u.email, 
						u.address,
						u.country,
						uc.comment_date, 
						uc.comment
						FROM user_comment uc
						JOIN user u on u.id = uc.user_id where uc.user_flame_id ='.$_REQUEST['user_flame_id'];
		$this->query($query);
		$this->execute();
		$results = $this->resultset();
		if (count($results) > 0) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $results;
		} else {
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
  	}

   	public function getImages() {
		$query = 'SELECT 
						i.id,
						i.user_id, 
						i.image
						FROM flame_image i where i.user_flame_id ='.$_REQUEST['user_flame_id'];
		$this->query($query);
		$this->execute();
		$results = $this->resultset();
		if (count($results) > 0) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $results;
		} else {
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
  	}

   	public function getAudio() {
		$query = 'SELECT 
						i.id,
						i.user_id, 
						i.audio
						FROM flame_audio i where i.user_flame_id ='.$_REQUEST['user_flame_id'];
		$this->query($query);
		$this->execute();
		$results = $this->resultset();
		if (count($results) > 0) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $results;
		} else {
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
  	}

   	public function getVideo() {
		$query = 'SELECT 
						i.id,
						i.user_id, 
						i.video
						FROM flame_video i where i.user_flame_id ='.$_REQUEST['user_flame_id'];
		$this->query($query);
		$this->execute();
		$results = $this->resultset();
		if (count($results) > 0) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $results;
		} else {
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
  	}

   	public function getViewer() {
		$query = 'SELECT 
						f.view_counter
						FROM flame f where f.id ='.$_REQUEST['flame_id'];
		$this->query($query);
		$result = $this->single();
		if ($result) {
			$viewer = $result['view_counter'];
			$viewerDetails = array();
			if($viewer){
				$viewers = explode(',', $viewer);
				foreach ($viewers as $key => $viewer) {
					$viewerDetails[] = $this->getUser($viewer);

				}
			}

			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $viewerDetails;
			$this->totalRecords = count($viewerDetails);
		} else {
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
  	}

	public function addComment(){
		date_default_timezone_set('UTC');
		// var_dump($date);exit;
		$query = 'INSERT INTO user_comment SET 
							user_id = :user_id,
							user_flame_id = :user_flame_id,
							comment = :comment,
							comment_date = :comment_date
						';
		$bind = array(
			':user_id' => $_REQUEST["user_id"],
			':user_flame_id' => $_REQUEST['user_flame_id'],
			':comment' => urldecode($_REQUEST['comment']),
			':comment_date' => date("Y-m-d H:i:s"),
		);
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$this->api_status = '1';
		$this->api_message = 'Added successfully';
		$this->api_data = '';
 		return $this->response();
	}
	public function sendMessage(){
		date_default_timezone_set('UTC');
		$query = 'INSERT INTO user_message SET 
							sender_id = :sender_id,
							receiver_id = :receiver_id,
							message = :message,
							message_date = :message_date
						';
		$messageDate = date("Y-m-d H:i:s");
		$bind = array(
			':sender_id' => $_REQUEST["sender_id"],
			':receiver_id' => $_REQUEST['receiver_id'],
			':message' => urldecode($_REQUEST['message']),
			':message_date' => $messageDate,
		);
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$this->api_status = '1';
		$this->api_message = 'Message send successfully';
		$this->api_data = '';
		$sender = $this->getUser($_REQUEST['sender_id']);
		$receiver = $this->getUser($_REQUEST['receiver_id']);
		//$ch = curl_init();
		$userData = $this->getUser($_REQUEST['sender_id']);
		$userData['message_date'] = $messageDate;
		$userData['message'] = urldecode($_REQUEST['message']);
		$userData['r_device_token'] = $receiver['device_token'];
		
		$pushNotification = new PushNotification();
		$data['s_first_name'] = $sender['first_name'];
		$data['message'] = $_REQUEST['message'];
		$data['sender_id'] = $sender['id'];
		$data['receiver_id'] = $receiver['id'];
		if($receiver['device'] == 'android'){
			$pushNotification->android($data, $receiver['device_token']);
		}else{
			$pushNotification->ios($data, $receiver['device_token']);
		}

 		return $this->response();
	}

	public function getSingleUser(){
		$results =  $this->getUser($_REQUEST['user_id']);
		$this->api_status = '1';
		$this->api_message = 'Message send successfully';
		$this->api_data = $results;
		return $this->response();
	}
	private function getUser($userId){
		$query = 'SELECT u.id, u.first_name, u.last_name, u.email, u.username, u.email, u.address, u.image, u.device_token, u.device, u.country';
		if(isset($_REQUEST['login_user_id'])){
			$query .= ', (select count(DISTINCT f.user_id) from follower f where f.follower_id=' .$userId. ' and f.user_id = '.$_REQUEST['login_user_id'].') as followingStatus';
		}
		$query .= ' FROM user u where u.id=' .$userId;
		$this->query($query);
		$this->execute();
		$result = $this->single();
		return $result;
	}

	public function getUserMessages(){
		$query = 'SELECT 
						um.id,
						s.id as s_id, 
						r.id as r_id, 
						s.first_name as s_first_name, 
						r.first_name as r_first_name, 
						s.country as s_country, 
						r.country as r_country, 
						s.last_name as s_last_name, 
						r.last_name as r_last_name, 
						s.email as s_email, 
						r.email as r_email, 
						s.username as s_username, 
						r.username as r_username, 
						s.address as s_address,
						r.address as r_address,
						s.image as s_image, 
						r.image as r_image,
						um.message,
						um.message_date
					FROM user_message um
					JOIN user s on s.id = um.sender_id
					JOIN user r on r.id = um.receiver_id
					WHERE (um.receiver_id = :receiver_id or um.receiver_id = :sender_id)
					and (um.sender_id = :receiver_id or um.sender_id = :sender_id)
					order by um.id ASC
					';
		$bind = array(
			':sender_id' => $_REQUEST["sender_id"],
			':receiver_id' => $_REQUEST['receiver_id'],
		);
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$results = $this->resultset();
		if (count($results) > 0) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $results;
		} else {
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
	}
	public function getMessages(){
		$query = 'SELECT 
						s.id as s_id, 
						r.id as r_id, 
						s.first_name as s_first_name, 
						r.first_name as r_first_name, 
						s.country as s_country, 
						r.country as r_country, 
						s.last_name as s_last_name, 
						r.last_name as r_last_name, 
						s.email as s_email, 
						r.email as r_email, 
						s.username as s_username, 
						r.username as r_username, 
						s.address as s_address,
						r.address as r_address,
						s.image as s_image, 
						r.image as r_image,
						um.message,
						um.message_date
					FROM user_message um
					JOIN user s on s.id = um.sender_id
					JOIN user r on r.id = um.receiver_id
					WHERE (um.receiver_id = :user_id or um.sender_id = :user_id)
					group by s.id, r.id
					order by um.id DESC
					';
		$bind = array(
			':user_id' => $_REQUEST["user_id"]
		);
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$results = $this->resultset();
		if (count($results) > 0) {
			$commentMessageGroups[] = array('s_id' => '', 'r_id' => '');
			foreach ($results as $key => $value) {
				$needToRemove = false;
				foreach ($commentMessageGroups as $commentMessageGroup) {
					if( ($commentMessageGroup['s_id'] == $value['s_id'] || $commentMessageGroup['s_id'] == $value['r_id']) 
						&& ($commentMessageGroup['r_id'] == $value['s_id'] || $commentMessageGroup['r_id'] == $value['r_id'])
					){
						$needToRemove = true;
					}
				}
				if($needToRemove){
					unset($results[$key]);
				}else{
					$commentMessageGroups[] = array('s_id' => $value['s_id'], 'r_id' => $value['r_id']);
				}
			}
			$filteredResults = array();
			foreach ($results as $key => $value) {
				if($value['s_id'] == $_REQUEST["user_id"]){
					$filteredResults[] = array(
												'id' => $value['r_id'],
												'first_name' => $value['r_first_name'],
												'country' => $value['r_country'],
												'last_name' => $value['r_last_name'],
												'email' => $value['r_email'],
												'username' => $value['r_username'],
												'address' => $value['r_address'],
												'image' => $value['r_image'],
												'message' => $value['message'],
												'message_date' => $value['message_date'],
											);
				}else{
					$filteredResults[] = array(
												'id' => $value['s_id'],
												'first_name' => $value['s_first_name'],
												'country' => $value['s_country'],
												'last_name' => $value['s_last_name'],
												'email' => $value['s_email'],
												'username' => $value['s_username'],
												'address' => $value['s_address'],
												'image' => $value['s_image'],
												'message' => $value['message'],
												'message_date' => $value['message_date'],
											);
				}
			}
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $filteredResults;
		} else {
			$this->api_status = '0';
			$this->api_message = 'Data not found';
			$this->api_data = '';
		}
	 	return $this->response();
	}
	public function deleteFlame($flameId = null){
		$query = 'DELETE FROM flame where id= :flame_id';
		$bind = array(
			':flame_id' => $flameId ? $flameId : $_REQUEST["flame_id"]
		);
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$this->api_status = '1';
		$this->api_message = 'Flame deleted successfully';
		$this->api_data = '';
	 	return $this->response();
	}
	public function deleteFlameUser(){
		$bind = array(
			':user_flame_id' => $_REQUEST["user_flame_id"]
		);
		$query = "SELECT count(DISTINCT cuf.id) as flameUser, f.id from user_flame uf 
					join flame f on f.id = uf.flame_id 
					join user_flame cuf on cuf.flame_id = f.id
					where uf.id = :user_flame_id";
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$result = $this->single();
		if($result['flameUser'] == 1){
			$this->deleteFlame($result['id']);
		}else{
			$query = 'DELETE FROM user_flame where id= :user_flame_id';
			
			$this->query($query);
			$this->bind($bind);
			$this->execute();
		}
		$this->api_status = '1';
		$this->api_message = 'Flame user deleted successfully';
		$this->api_data = '';
	 	return $this->response();
	}
	public function arrayRemoveNull($array) {
	    foreach ($array as $key => $value)
	    {
	        if(is_null($value)){
	        	$array[$key] = '';
	        }
	            
	        if(is_array($value)){
	            $array[$key] = $this->arrayRemoveNull($value);
	        }
	    }
	    return $array;
	}

	public function editFlame(){
		$userFlameId = $_REQUEST["user_flame_id"];
		$bind = array(
			':user_flame_id' => $_REQUEST["user_flame_id"],
			':description' => $_REQUEST["description"],
		);
		$query = "UPDATE user_flame uf set uf.description = :description where uf.id=:user_flame_id";
		$updateMainFlameQuery = "UPDATE flame f set f.updated_at = NOW() where f.id=".$_REQUEST["flame_id"];
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$this->query($updateMainFlameQuery);
		$this->execute();
		if($_REQUEST['edit_type'] == 'Flame'){
			$flameId = $_REQUEST["flame_id"];
			$bind = array(
				':flame_id' => $_REQUEST["flame_id"],
				':title' => $_REQUEST["title"],
				':website' => $_REQUEST["website"],
				':mobile' => $_REQUEST["mobile"],
				':country' => $_REQUEST["country"],
				':address' => $_REQUEST["address"],
				':latitude' => $_REQUEST["latitude"],
				':longitude' => $_REQUEST["longitude"],
                                ':category_id' => $_REQUEST["category_id"],
			);
			$query = "UPDATE flame f 
						set 
							f.title = :title,
							f.website = :website,
							f.mobile = :mobile,
							f.address = :address,
							f.country = :country,
							f.latitude = :latitude,
							f.longitude = :longitude,
                                                        f.category_id = :category_id
						where f.id=:flame_id";
			$this->query($query);
			$this->bind($bind);
			$this->execute();
		}

		$this->setFlameImage($userFlameId);
		$this->setFlameAudio($userFlameId);
		$this->setFlameVideo($userFlameId);
		$this->deleteFlameImage();
		$this->deleteFlameAudio();
		$this->deleteFlameVideo();
		$this->api_status = '1';
		$this->api_message = 'Flame updated successfully';
		$this->api_data = '';
	 	return $this->response();
	}

	private function deleteFlameImage(){
		if(isset($_REQUEST['deletedImages']) && $_REQUEST['deletedImages']){
			$images = json_decode($_REQUEST['deletedImages'], true);
			foreach ($images as $key => $image) {
				$query = 'DELETE from flame_image WHERE 
							image = :image
						';
				$bind = array(
					':image' => $image
				);
				$this->query($query);
				$this->bind($bind);
				$this->execute();
			}
		}
	}

	private function deleteFlameAudio(){
		if(isset($_REQUEST['deletedAudio']) && $_REQUEST['deletedAudio']){
			$query = 'DELETE FROM flame_audio where 
						audio = :deletedAudio
					';
			$bind = array(
				':deletedAudio' => $_REQUEST["deletedAudio"]
			);
			$this->query($query);
			$this->bind($bind);
			$this->execute();
		}
	}

	private function deleteFlameVideo(){
		if(isset($_REQUEST['deletedVideo']) && $_REQUEST['deletedVideo']){
			$query = 'DELETE FROM flame_video where 
						video = :deletedVideo
					';
			$bind = array(
				':deletedVideo' => $_REQUEST["deletedVideo"]
			);
			$this->query($query);
			$this->bind($bind);
			$this->execute();
		}
	}
}
