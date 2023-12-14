
<?php
class SessionController {
    public static function session() {
        
        $db = new DbHandler();
        $session = $db->getSession();
        $response["user_id"] = $session['user_id'];
        $response["bo_id"] = $session['bo_id'];
        $response["bo_name"] = $session['bo_name'];
        $response["username"] = $session['username'];
        $response["emp_name"] = $session['emp_name'];
        $response["emp_image"] = $session['emp_image'];

        $response["email_id"] = $session['email_id'];
        $response["role"] = $session['role'];
        $response["permissions"] = $session['permissions'];
        echoResponse(200, $response);
    }
}
?>