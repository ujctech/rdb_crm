<?php

    $curl = curl_init();
    $post_fields = array();
    $post_fields["method"] = "sendMessage";
    $post_fields["send_to"] = "9823041486";
    //$post_fields["send_to"] = "9823041486";
    $post_fields["msg"] = "Warm greetings from R.D Brothers Properties! Please note that Registration Office address has been sent on your registered email Id or contact  9823041486 for further details";
    $post_fields["msg_type"] = "TEXT";
    //$post_fields["userid"] = "2000195702";
    //$post_fields["password"] = "Rdbrothers@123";

    $post_fields["userid"] = "2000196081";
    $post_fields["password"] = "Rdbrothers@123";
    $post_fields["v"] = "1.1";
    $post_fields["auth_scheme"] = "PLAIN";
    $post_fields["format"] = "JSON";
    curl_setopt_array($curl, array(CURLOPT_URL => "https://enterprise.smsgupshup.com/GatewayAPI/rest",CURLOPT_RETURNTRANSFER => true,CURLOPT_ENCODING => "",CURLOPT_MAXREDIRS => 10,CURLOPT_TIMEOUT =>30,CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,CURLOPT_CUSTOMREQUEST => "POST",CURLOPT_POSTFIELDS => $post_fields));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if ($err) {
        echo "cURL Error #:" . $err;
    } else 
    {
        echo $response;
    }

?>