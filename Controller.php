<?php
header('Content-Type: application/json');
error_reporting(E_ALL); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(727379969);
ini_set('max_execution_time', 84400);
ini_set('max_input_time', 84400);
ini_set('post_max_size', '5120M');
ini_set('max_file_uploads', 100);
ini_set('upload_max_filesize', '5120M');

ini_set('default_socket_timeout', 84400);
ini_set('session.gc_maxlifetime', 84400);
ini_set('memory_limit','5120M');
extract($_POST);
extract($_GET);
extract($_REQUEST);
include 'PushNotification.php';
	
               $pushNotification = new PushNotification();
               $userData = json_decode($_REQUEST['userData'], true);
	       $pushNotification->ios($userData, $userData['r_device_token']);