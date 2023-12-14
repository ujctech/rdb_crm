<?php

@ob_start();
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED); 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/x-www-form-urlencoded');
//require_once 'd:\xampp\htdocs\rhpnew\api\PhpPresentation\src\PhpPresentation\Autoloader.php';
//\PhpOffice\PhpPresentation\Autoloader::register();

//require_once 'd:\xampp\htdocs\rhpnew\api\PhpOffice\Common\src\Common\Autoloader.php';
//\PhpOffice\Common\Autoloader::register();

require_once 'dbHandler.php';
require_once 'passwordHash.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;
date_default_timezone_set('Asia/Kolkata');

require_once 'dataaccess.php';
require_once 'dataaccessnew.php';
require_once 'properties_controller.php';
require_once 'enquiries_controller.php';
require_once 'project_controller.php';
require_once 'contacts_controller.php';
require_once 'agreement_controller.php';
require_once 'utilities.php';
require_once 'hrms.php';

require_once 'others.php';

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields,$request_params) {
    $error = false;
    $error_fields = "";
    foreach ($required_fields as $field) {
        if (!isset($request_params->$field) || strlen(trim($request_params->$field)) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["status"] = "error";
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(200, $response);
        $app->stop();
    }
}


function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}
//$app->get('/area_list_ctrl', 'AreaController::area_list_ctrl');
//$app->get('/session', 'SessionController::session');
$app->run();
?>