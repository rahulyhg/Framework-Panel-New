<?php

//define db user name
define('USERNAME', 'instappy_master');
//define db password
define('PASSWORD', 'rer5676(*&^JHJ');
//define db hostname
define('HOST', 'framework.cuogjeymw1h7.us-west-2.rds.amazonaws.com');
//define database name
define('DB', 'instappy_production');
//define db class

define('USERNAME_OPENCART', 'instappy_master');
//define db password
define('PASSWORD_OPENCART', 'rer5676(*&^JHJ');
//define db hostname
define('HOST_OPENCART', 'framework.cuogjeymw1h7.us-west-2.rds.amazonaws.com');
//define database name
define('DB_OPENCART', 'ecommerce_app_new');

class DB {

    private $urL = "http://www.instappy.com/";
    private $catalogue_urL = "http://www.instappy.com/";
    private $reseller_url = "http://52.41.29.218/crm_reseller/";
    public function dbconnection() {
        $user = USERNAME;
        $pass = PASSWORD;
        $host = HOST;
        $db = DB;
        try {
            $db = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->exec("SET time_zone = 'Asia/Kolkata'");
            return $db;
        } catch (PDOException $e) {

            echo "DB Error" . $e->getMessage();
        }
    }

    public function dbconnection_opencart() {
        $user = USERNAME_OPENCART;
        $pass = PASSWORD_OPENCART;
        $host = HOST_OPENCART;
        $db = DB_OPENCART;
        try {
            $db = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->exec("SET time_zone = 'Asia/Kolkata'");
            return $db;
        } catch (PDOException $e) {
            echo "DB Error" . $e->getMessage();
        }
    }

    public function siteurl() {
        return $this->urL;
    }

    public function resellerurl() {
        return $this->reseller_url;
    }

    function xss_clean($data) {
        // Fix &entity\n;
        $data = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }

//    public function ip_details($IPaddress) {
//        $json = file_get_contents("http://www.freegeoip.net/json/{$IPaddress}");
//       
//        $details = json_decode($json);
//       
//        return $details;
//    }
//    public function ip_details($IPaddress) {        
//        $json = file_get_contents("http://ipinfo.io/".$IPaddress);        
//        $details = json_decode($json);
//        return $details;
//    }

    public function getcurrencyid($country) {
        $db = $this->dbconnection();
        $sql = "SELECT * FROM currency_type WHERE country='$country'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        return $results;
    }

    public function get_country() {
        //  include_once 'geoiploc.php';
        session_start();
        // $IPaddress = $_SERVER['REMOTE_ADDR'];         
        //  $country = getCountryFromIP($IPaddress, "code");
        $countryset = 0;
        $country = "US";
        if ($country == "IN") {
            $country = "IN";
            $countryName = $this->getcurrencyid($country);
            $_SESSION['country'] = $countryName['country'];
            $_SESSION['currencyid'] = $countryName['id'];
            $_SESSION['currency'] = $countryName['Name'];
            $checkcountry = $countryName['id'];
            $currency = $countryName['id'];
            $currencyIcon = $countryName['Name'];
            $countryset = 1;
        } else if ($country != "IN") {
            $country = "US";
            $countryName = $this->getcurrencyid($country);
            $_SESSION['country'] = $countryName['country'];
            $_SESSION['currencyid'] = $countryName['id'];
            $_SESSION['currency'] = $countryName['Name'];
            $checkcountry = $countryName['id'];
            $currency = $countryName['id'];
            $currencyIcon = $countryName['Name'];
            $countryset = 1;
        } else {
            $country = "US";
            $countryName = $this->getcurrencyid($country);
            $_SESSION['country'] = $countryName['country'];
            $_SESSION['currencyid'] = $countryName['id'];
            $_SESSION['currency'] = $countryName['Name'];
            $checkcountry = $countryName['id'];
            $currency = $countryName['id'];
            $currencyIcon = $countryName['Name'];
        }
    }

//    public function get_country() {
//      session_start();
//      $IPaddress = $_SERVER['REMOTE_ADDR'];
////      echo "<input text='hidden' value='$IPaddress'/>";
//        // $details    =   ip_details("182.74.217.98");
////        $details = $this->ip_details("12.215.42.19");
////        
//      $details = $this->ip_details($IPaddress);
//      $country = $details->country_code;     
//      
////    $country = "US";
////    $country = "IN";
//        $countryset = 0;
//        if ($country == "IN") {
////            echo 'IN'.'--';
////            echo $countryName['country'];
//            $countryName = $this->getcurrencyid($country);
//            $_SESSION['country'] = $countryName['country'];
//            $_SESSION['currencyid'] = $countryName['id'];
//            $_SESSION['currency'] = $countryName['Name'];
//            $checkcountry = $countryName['id'];
//            $currency = $countryName['id'];
//            $currencyIcon = $countryName['Name'];
//            $countryset = 1;
//        } 
//        else if ($country == "US") {
////            echo 'USA'.'--';
////            echo $countryName['country'];
//            $countryName = $this->getcurrencyid($country);
//            $_SESSION['country'] = $countryName['country'];
//            $_SESSION['currencyid'] = $countryName['id'];
//            $_SESSION['currency'] = $countryName['Name'];
//            $checkcountry = $countryName['id'];
//            $currency = $countryName['id'];
//            $currencyIcon = $countryName['Name'];
//            $countryset = 1;
//        } 
//        else if ($country == "GB") {
////            echo 'USA'.'--';
////            echo $countryName['country'];
//            $countryName = $this->getcurrencyid($country);
//            $_SESSION['country'] = $countryName['country'];
//            $_SESSION['currencyid'] = $countryName['id'];
//            $_SESSION['currency'] = $countryName['Name'];
//            $checkcountry = $countryName['id'];
//            $currency = $countryName['id'];
//            $currencyIcon = $countryName['Name'];
//            $countryset = 1;
//        } 
//        if($countryset==0){
//           $country = "IN";
//            $countryName = $this->getcurrencyid($country);
//            $_SESSION['country'] = $countryName['country'];
//            $_SESSION['currencyid'] = $countryName['id'];
//            $_SESSION['currency'] = $countryName['Name'];
//            $checkcountry = $countryName['id'];
//            $currency = $countryName['id'];
//            $currencyIcon = $countryName['Name'];
//        }
//    }

    public function catalogue_url() {
        return $this->catalogue_urL;
    }

    public function restrictPages() {
        $pages = array(
            'panel.php', 'catalogue.php'
        );
        return $pages;
    }

    public function meta_tags($page, $tag) {
        $db = $this->dbconnection();
        $sql = "select $tag from app_meta_tags where url='$page'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);
        return $results[$tag];
    }

    public function service_tax() {
        $db = $this->dbconnection();
        $sql = "SELECT perc_tax FROM service_tax 
    WHERE DATE(NOW()+INTERVAL 5 HOUR+INTERVAL 30 MINUTE)>=implementation_date OR is_active=1
    ORDER BY implementation_date DESC,id DESC
    LIMIT 1 ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_OBJ);
        return $results->perc_tax;
    }

    public function get_current_currency($part_id = 0) {
        $cust_id = $_SESSION['custid'];
        $db = $this->dbconnection();
        if ($part_id > 0) {
            echo $sql = "SELECT mp.currency_type_id as currency_type_id  FROM master_payment_part as mpp 
  join  master_payment as  mp  on mpp.master_payment_id=mp.id
  WHERE mpp.id=$part_id
  LIMIT 1";
        } else {
            $sql = "SELECT mp.currency_type_id FROM master_payment as mp
join author_payment as ap on mp.id=ap.master_payment_id
  WHERE mp.author_id in(select id from author where custid=$cust_id) and mp.payment_done=0 and ap.payment_done=0
  ORDER BY mp.id DESC
  LIMIT 1";
        }
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_OBJ);
        $array = array();
        $array['currency_type_id'] = $results->currency_type_id;
        $array['currency_icon'] = $this->get_current_currency_icon($results->currency_type_id);
        return $array;
    }

    public function get_current_currency_icon($curency_id) {

        $db = $this->dbconnection();
        $sql = "SELECT NAME FROM currency_type WHERE id='$curency_id'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_OBJ);
        return $results->NAME;
    }

}

?>