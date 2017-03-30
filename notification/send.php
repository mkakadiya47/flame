<?php
error_reporting(E_ALL | E_STRICT);
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
include 'APNSBase.php';
include 'APNotification.php';
include 'APFeedback.php';
try{
  # Notification Example
  $notification = new APNotification('development');
  $notification->setDeviceToken("c0a74cea582ca29a71d50a8e4340a6193211bdb72da86a4df405377edbb2f80a");
  $notification->setMessage("Test Push By Manoj");
  $notification->setBadge(1);
  $notification->setPrivateKey('Flame.pem');
  $notification->setPrivateKeyPassphrase('admin');
  $notification->send();
}catch(Exception $e){
  echo $e->getLine().': '.$e->getMessage();
}
?>
