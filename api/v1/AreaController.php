
<?php
class AreaController {
    public static function area_list_ctrl($name) {
        
        $sql  = "";
        $db = new DbHandler();
        $session = $db->getSession();
        if ($session['username']=="Guest")
        {
            return;
        }
        $sql = "SELECT a.*,b.city, c.state, CONCAT(d.country,'-',d.country_code)  as country  from areas as a LEFT JOIN city as b on a.city_id = b.city_id LEFT JOIN state as c ON b.state_id = c.state_id LEFT JOIN country as d ON c.country_id = d.country_id";
        $areas = $db->getAllRecords($sql);
        echo json_encode($areas);
    }
}
?>