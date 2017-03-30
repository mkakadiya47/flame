<?php
define("ITEM_ZISE", 20);
class Controller extends Database {

	public $site_url;

	public function __construct() {
		parent::__construct();
 	}
   
    public function addMessage() {
    	$messageData = $_REQUEST["message"];
    	$messageData = explode(':', $messageData);
		$query = "INSERT INTO message_data (id, `token`, `message`, `idnumber`)
		 			values(
		 				null,
		 				:token,
		 				:message,
		 				:idnumber
		 				)";
		$bind = array(
			':token' => $_REQUEST["token"],
			':message' => $messageData[1],
			':idnumber' =>  $messageData[0],
		);
		$this->query($query);
		$this->bind($bind);
		$this->execute();
		$messageId = $this->lastInsertId();
		$results = $this->getResult($messageId, $_REQUEST['token'], $messageData[0]);
		if ($results) {
			$this->api_status = '1';
			$this->api_message = 'SUCCESS';
			$this->api_data = $results;
			$this->deletedMessage($results['id']);
		} else {
			for($i = 1; $i <=15; $i++) {
				sleep(1);
				$results = $this->getResult($messageId, $_REQUEST['token'], $messageData[0]);
				if($results){
					break;
				}
			}
			if ($results) {
				$this->api_status = '1';
				$this->api_message = 'SUCCESS';
				$this->api_data = $results;
				$this->deletedMessage($results['id']);
			}else{
				$this->api_status = '0';
				$this->api_message = 'Error';
				$this->api_data = '';
				$this->deletedMessage($messageId);
			}
		}
		return $this->response();
 	}
 	private function getResult($messageId, $token, $idnumber){
 		$query = "SELECT dm.id, dm.message FROM message_data dm where dm.idnumber= '".$idnumber."' and dm.id != '".$messageId ."' and dm.token != '".$token."'";
		$this->query($query);
		$this->execute();
		$results = $this->single();
		return $results;
 	}
 	private function deletedMessage($messageId){
 		$query = "DELETE FROM message_data where id = '".$messageId."'";
		$this->query($query);
		$this->execute();
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

            $api_data = $this->api_data;
        }
		
				
	 	$this->api_return = array(
			 'status' => $api_status,
			 'message' => $api_message,
			 'data' => $api_data,
		 );
         return json_encode($this->api_return);
  	}
}