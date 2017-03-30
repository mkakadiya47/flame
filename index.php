<?php
/*******************************************************************************/
header('Content-Type: application/json');
error_reporting(E_ALL); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
set_time_limit(727379969);
ini_set('upload_max_filesize', '2048M');
ini_set('post_max_size', '2048M');
ini_set('max_execution_time', 100000);
ini_set('memory_limit','2048M');
extract($_POST);
extract($_GET);
extract($_REQUEST);
/*******************************************************************************/
// include 'Security.php';
// $security = new Security();
// $checkLogin = $security->checkLogin($act);
// if( $checkLogin !== true){
// 	echo $checkLogin;
// 	die;
// }
include 'config.php';
include 'MailManager.php';
include 'Database.php';
include 'Controller.php';
/*******************************************************************************/

$controller = new Controller();
if(function_exists($controller->{$act}()) && $_REQUEST['token']) { 
    echo $controller->{$act}();
} else {
    echo $controller->response();
}
/*******************************************************************************/