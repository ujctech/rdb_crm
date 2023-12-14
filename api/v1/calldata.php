<?php 
//echo var_dump($_POST)

if($json = json_decode(file_get_contents("php://input"), true)) {
     print_r($json);
    $data = $json;
    echo "data saved successfully...!!!";
 } else {
     print_r($_POST);
     $data = $_POST;
 }
 header('Content-Type: text/event-stream');
 header('Cache-Control: no-cache');

 $time = date('r');
 /*$response["status"] = "success";
 $response["message"] = "The server time is: {$time}\n\n";
 echoResponse(200, $response);*/
 echo "data: The server time is: {$time}\n\n";
 flush();

/*echo "Saving data ...\n";
$url = "https://localhost:5984/incoming";
$meta = &#91;"received" => time(),
  "status" => "new",
    "agent" => $_SERVER['HTTP_USER_AGENT']];
 
 $options = ["http" => [
     "method" => "POST",
    "header" => ["Content-Type: application/json"],
     "content" => json_encode(["data" => $data, "meta" => $meta])]
     ];
 
 $context = stream_context_create($options);
 $response = file_get_contents($url, false, $context);
 */
?>
