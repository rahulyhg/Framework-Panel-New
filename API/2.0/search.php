<?php

require 'Slim/Slim.php';
require 'includes/db.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->post('/search', 'searchData');

function authCheck($authToken, $device_id) {
    $dbCon = content_db();
    //  $auth_query = "select id from user_appdata where user_name=$device_id and auth_token='" . $authToken . "'";
    $auth_query = "select u.id from users u left join user_appdata uad on u.id=uad.user_id where uad.auth_token=:authtoken and u.device_id=:deviceid";
    $auth_queryExecution = $dbCon->prepare($auth_query);
    $auth_queryExecution->bindParam(':authtoken', $authToken, PDO::PARAM_STR);
    $auth_queryExecution->bindParam(':deviceid', $device_id, PDO::PARAM_STR);
    $auth_queryExecution->execute();
    $result_auth = $auth_queryExecution->rowCount(PDO::FETCH_NUM);

    return $result_auth;
}

function contentapp_search($primaryapp_id, $searchItem, $offset, $limit) {
    $dbCon = content_db();
    $appQueryData = "select *  from app_data where app_id=:appid";
    $app_screenData = $dbCon->prepare($appQueryData);
    $app_screenData->bindParam(':appid', $primaryapp_id, PDO::PARAM_STR);
    $app_screenData->execute();
    $result_screenData = $app_screenData->fetch(PDO::FETCH_OBJ);
    if ($result_screenData != '') {
        $app_id = $result_screenData->id;
    } else {
        $app_id = 0;
    }
    $app_typenew = $result_screenData->type_app;
    $componentResultSet = [];

    $response = array("result" => 'success', "msg" => '');
    $appEventAllQuery = "SELECT 
    cv.id,
    cv.app_id,
    cv.component_type_id,
    cv.componentfieldoption_id,
    ct.NAME AS component_type_name,
    cd.customname AS component_attr_name,
    cd.field_type,
    ct.is_except,
    cd.field_attributes,
    cd.list_type,
    cv.componentarraylink_id,
    cv.screen_id,
    cv.linkto_screenid,
    cv.list_no,
    cv.component_no,
    cv.defaultselect,
    cv.component_position,
    cv.description,
    cv.datevalue,
   (SELECT image_url FROM componentfieldvalue WHERE description='image' AND app_id =cv.app_id 
AND component_position=cv.component_position AND deleted='0' LIMIT 1) AS image_url,
    cv.video_url,
    cv.height,
    cv.width,
    cv.item_orientation,
    cv.background_type,
    cv.backgroundcolor,
    cv.texttodisplay,
    cv.font_color,
    cv.font_typeface,
    cv.font_size,
    cv.display,
    cv.visibility,
    cv.auto_update,
    cv.card_elevation,
    cv.card_corner_radius,
    cv.action_type_id,
    cv.action_data,
    cv.is_preference,
    cv.action_message,
    cd.top_level
FROM
    componentfieldvalue cv
        LEFT JOIN
    customfielddata cd ON (cv.component_type_id = cd.component_type_id
        AND cv.componentfieldoption_id = cd.componentfieldoption_id)
        JOIN
    component_type ct ON cv.component_type_id = ct.id
WHERE
    app_id = :appid AND cv.description LIKE '%" . $searchItem . "%'   AND cv.list_no != ''
       group by app_id,screen_id,component_type_id 
ORDER BY component_no , list_no , componentarraylink_id limit $offset,$limit
";

    $app_AlleventNavigationquery = $dbCon->prepare($appEventAllQuery);
    $app_AlleventNavigationquery->bindParam(':appid', $app_id, PDO::PARAM_INT);

    $app_AlleventNavigationquery->execute();

    $result_eventAll = $app_AlleventNavigationquery->fetchAll(PDO::FETCH_OBJ);

    foreach ($result_eventAll as $resultAllEventSet) {
        $textlen = strlen($resultAllEventSet->description);
        $testShow = substr("$resultAllEventSet->description", 0, 30);
        $images=$resultAllEventSet->image_url;
        if($images == '' || $images == null)
        {
           $images='http://www.instappy.com/images/search.jpg'; 
        }
        if ($textlen > 30) {
            $finalText = $testShow . "......";
        } else {
            $finalText = $testShow;
        }
        $searchText = $finalText;
        $arraycomp_elements["image"] = array("media_type" => 'image', "image_url" => $images, "height" => '24', "width" => '24');
        $arraycomp_elements["heading"] = array("text" => strip_tags($searchText), "font_size" => "16", "font_color" => "#000000", "font_typeface" => "Arial");
        $arraycomp_elements["subheading"] = array("text" => "", "font_size" => $resultAllEventSet->font_size, "font_color" => $resultAllEventSet->font_color, "font_typeface" => $resultAllEventSet->font_typeface);
        $arraycomp_elements["middle_icon"] = array("text" => "e604", "font_size" => '20', "font_color" => '#7c7c7c', "font_typeface" => 'icomoon_merge.ttf', "action_type_id" => '4', "action_data" => '234', "app_type" => $app_typenew, "action_message" => 'wqerty');

        $arraycomp_elements["addedToWishlist"] = "0";
        $componentResultSet[] = array("comp_id" => $resultAllEventSet->id, "app_type" => $app_typenew, "background_color" => $resultAllEventSet->backgroundcolor, "card_elevation" => $resultAllEventSet->card_elevation, "card_corner_radius" => $resultAllEventSet->card_corner_radius, "comp_type" => '1002', "linkto_screenid" => $resultAllEventSet->screen_id, "comp_elements" => $arraycomp_elements);
    }
    return $componentResultSet;
}

function retailapp_search($primaryapp_id, $searchItem, $offset, $sortorder, $orderby, $limit) {
    $filterquery = '';
    $dbCon = content_db();
    $appQueryData = "select *  from app_data where app_id=:appid";
    $app_screenData = $dbCon->prepare($appQueryData);
    $app_screenData->bindParam(':appid', $primaryapp_id, PDO::PARAM_STR);
    $app_screenData->execute();
    $result_screenData = $app_screenData->fetch(PDO::FETCH_OBJ);
    if ($result_screenData != '') {
        $app_id = $result_screenData->id;
        $type_app = $result_screenData->type_app;
    } else {
        $app_id = 0;
        $type_app = 0;
    }
    $componentResultSet = [];
    $componentResultSet2 = [];
    $arraycomp_elements3 = [];
    $app_typenew = $result_screenData->type_app;

    $baseUrl = baseUrl();
    $dbCon2 = retail_db();
    $finalquery = $brandquery = $sizequery = "";
    /* SOrt Order */
    if ($sortorder == 5) {
        $sortorder = "Order by amount asc";
    } elseif ($sortorder == 4) {
        $sortorder = "Order by amount desc";
    } elseif ($sortorder == 1) {
        $sortorder = "Order by views desc";
    } elseif ($sortorder == 2) {
        $sortorder = "Order by itemid desc";
    } elseif ($sortorder == 3) {
        $sortorder = "Order by ops.discount desc";
    } elseif ($sortorder == 0 || $sortorder == '') {
        $sortorder = "order by IF(instr(pc.name, '$searchItem'), instr(pc.name, '$searchItem'), 65535) asc";
    }

    /* Sort Order */

    if (isset($_POST['filterquery'])) {
        $filterquery = $_POST['filterquery'];
    } else {
        $filterquery = " ";
    }
////////////////
//$query_str = parse_url($filterquery, PHP_URL_QUERY);
    $size = "";
    $brand = "";
    $filterquery = explode(' AND ', $filterquery);
//print_r($abccc);
    $query_params = [];
    foreach ($filterquery as $filter) {
        parse_str($filter, $query_params);
        if (array_key_exists("brand", $query_params)) {
            $brand = $query_params['brand'];
        }
        if (array_key_exists("size", $query_params)) {
            $size = $query_params['size'];
        }
    }

    if ($brand != '') {
        //$brand =explode(',',$brand);
        $brand = explode(",", $brand);
        $prefix = $brandquery = '';
        foreach ($brand as $brandl) {
            $brandquery .= $prefix . "'" . $brandl . "'";
            $prefix = ',';
        }
        $brandquery = ' AND brand IN(' . $brandquery . ')';
    }

    if ($size != '') {
        //$brand =explode(',',$brand);
        $size = explode(",", $size);
        $prefix = $sizequery = '';
        $prefixsize = "AND (";
        foreach ($size as $size1) {
            $sizequery .= $prefixsize . " FIND_IN_SET('" . $size1 . "',size) > 0";
            $prefixsize = ' OR';
        }
        $sizequery = $sizequery . " )";
    }
    $finalquery = $brandquery . " " . $sizequery;


    $cquery = "SELECT DISTINCT p.product_id AS itemid ,IF(IFNULL(ops.price,0)='', p.price, ops.price) as amount, pc.NAME AS itemheading, p.image AS imageurl, COALESCE(p.price, '') AS actualprice,pvc.viewed as views , COALESCE(ops.price, '') AS special_price, ops.discount,oprospec.size as size,oprospec.brand as brand,AVG(rr.rating) AS 'rating' FROM oc_product p LEFT JOIN oc_product_description pc ON p.product_id=pc.product_id LEFT JOIN oc_product_to_category opc ON p.product_id=opc.product_id LEFT JOIN oc_product_special ops ON p.product_id=ops.product_id LEFT JOIN oc_app_id ai ON ai.product_id=p.product_id LEFT JOIN oc_product_specs oprospec ON oprospec.product_id=p.product_id  LEFT JOIN product_view_count pvc ON pvc.product_id=p.product_id  LEFT OUTER JOIN review_rating rr ON rr.product_id=p.product_id  WHERE (pc.name LIKE '%" . $searchItem . "%' OR pc.description LIKE '%" . $searchItem . "%' OR concat(', ', pc.tag, ',') like concat(', %" . $searchItem . "%,')) AND ai.app_id='" . $app_id . "' AND p.status = 1  $finalquery  GROUP BY itemid   $sortorder   LIMIT $offset, $limit";

    $con = $dbCon2->query($cquery);

    $launchdata = $con->fetchAll(PDO::FETCH_ASSOC);
    // $dbCon2 = null;
    if (!empty($launchdata)) {
        foreach ($launchdata as $tempdata) {
            if (@getimagesize($tempdata['imageurl'])) {
                $tempdata_imageurl = $tempdata['imageurl'];
            } else {
                $tempdata_imageurl = $baseUrl . $tempdata['imageurl'];
            }
            $actualprice = $tempdata['actualprice'] ? $tempdata['actualprice'] : 0;
            $specialprice = $tempdata['special_price'] ? $tempdata['special_price'] : 0;
            $discountprice = $tempdata['discount'] ? $tempdata['discount'] . '%' : 0;
            $pquery = "SELECT * FROM oc_product_specs WHERE product_id='" . $tempdata['itemid'] . "'";
            $propdata = $dbCon2->query($pquery);

            if (!empty($propdata)) {
                $arraycomp_elements['is_specification'] = 1;
            } else {
                $arraycomp_elements['is_specification'] = 0;
            }

            $arraycomp_elements["image"] = array("media_type" => 'image', "image_url" => $tempdata_imageurl, "height" => '24', "width" => '24');
            $arraycomp_elements["middle_icon"] = array("text" => "e604", "font_size" => '20', "font_color" => '#7c7c7c', "font_typeface" => 'icomoon_merge.ttf', "action_type_id" => '4', "action_data" => '234', "app_type" => $type_app, "action_message" => 'wqerty');
            $arraycomp_elements["actualprice"] = $actualprice;
            $arraycomp_elements["addedToWishlist"] = "0";
            $arraycomp_elements["discount"] = $discountprice;
            // $arraycomp_elements["imageurl"] = ;
            $arraycomp_elements["itemheading"] = $tempdata['itemheading'];
            $arraycomp_elements["itemid"] = $tempdata['itemid'];
            $arraycomp_elements["price"] = $specialprice;
            $arraycomp_elements["rating"] = $tempdata['rating'];



            $componentResultSet[] = array("comp_id" => $tempdata['itemid'], "app_type" => $app_typenew, "background_color" => '#FFFFFF', "card_elevation" => '2', "card_corner_radius" => '3', "comp_type" => '1001', "linkto_screenid" => '9766', "comp_elements" => $arraycomp_elements);
        }
    }

    /* Filter Query */


    $cqueryproductcomm = "SELECT DISTINCT GROUP_CONCAT(p.product_id) as commasepid FROM oc_product p LEFT JOIN oc_product_description pc ON p.product_id=pc.product_id LEFT JOIN oc_product_to_category opc ON p.product_id=opc.product_id LEFT JOIN oc_product_special ops ON p.product_id=ops.product_id LEFT JOIN oc_app_id ai ON ai.product_id=p.product_id LEFT JOIN oc_product_specs oprospec ON oprospec.product_id=p.product_id  LEFT JOIN product_view_count pvc ON pvc.product_id=p.product_id  LEFT OUTER JOIN review_rating rr ON rr.product_id=p.product_id  WHERE (pc.name LIKE '%" . $searchItem . "%' OR pc.description LIKE '%" . $searchItem . "%' OR concat(', ', pc.tag, ',') like concat(', %" . $searchItem . "%,')) AND ai.app_id='" . $app_id . "' AND p.status = 1";

    $conproductcomm = $dbCon2->query($cqueryproductcomm);

    $launchdataproductcomm = $conproductcomm->fetch(PDO::FETCH_ASSOC);

    $commasepid = $launchdataproductcomm['commasepid'];
    if ($commasepid != '') {
        $cqueryfilter = "Select  SUM(IF(FIND_IN_SET('S',size) > 0, 1, 0)) AS S,SUM(IF(FIND_IN_SET('M',size) > 0, 1, 0)) AS M,SUM(IF(FIND_IN_SET('L',size) > 0, 1, 0)) AS L,SUM(IF(FIND_IN_SET('XS',size) > 0, 1, 0)) AS XS,SUM(IF(FIND_IN_SET('XXS',size) > 0, 1, 0)) AS XXS,SUM(IF(FIND_IN_SET('XL',size) > 0, 1, 0)) AS XL,SUM(IF(FIND_IN_SET('XXL',size) > 0, 1, 0)) AS XXL,SUM(IF(FIND_IN_SET('XXXL',size) > 0, 1, 0)) AS XXXL from oc_product_specs where product_id IN ($commasepid)";
        $confilter = $dbCon2->query($cqueryfilter);
        $launchdatafilter = $confilter->fetchAll(PDO::FETCH_ASSOC);
    }
    
    if (!empty($launchdatafilter)) {
        foreach ($launchdatafilter as $tempdata) {

            $S = $tempdata['S'] ? $tempdata['S'] : 0;
            $M = $tempdata['M'] ? $tempdata['M'] : 0;
            $L = $tempdata['L'] ? $tempdata['L'] : 0;
            $XS = $tempdata['XS'] ? $tempdata['XS'] : 0;
            $XXS = $tempdata['XXS'] ? $tempdata['XXS'] : 0;
            $XL = $tempdata['XL'] ? $tempdata['XL'] : 0;
            $XXL = $tempdata['XXL'] ? $tempdata['XXL'] : 0;
            $XXXL = $tempdata['XXXL'] ? $tempdata['XXXL'] : 0;


            if ($S != 0 || $M != 0 || $L != 0 || $XS != 0 || $XXS != 0 || $XL != 0 || $XXL != 0 || $XXXL != 0) {
                $componentResultSet2[] = array("comp_id" => '1', "component_type" => '1004', "search_filters" => "Yes", "filter_name" => 'size', "elements_data" => array(array("name" => "S", "count" => $S), array("name" => "M", "count" => $M), array("name" => "L", "count" => $L), array("name" => "XS", "count" => $XS), array("name" => "XXS", "count" => $XXS), array("name" => "XL", "count" => $XL), array("name" => "XXL", "count" => $XXL), array("name" => "XXXL", "count" => $XXXL)));
            }
        }
    }

    if ($commasepid != '') {
        $cquerybrand = "Select brand as brand,count(brand) as count from oc_product_specs  WHERE product_id IN($commasepid) AND brand!='' group by brand";
        $conbrand = $dbCon2->query($cquerybrand);

        $launchdatabrand = $conbrand->fetchAll(PDO::FETCH_ASSOC);
    }
    // $dbCon2 = null;
    $test = array();

    $componentResultSet3 = [];
    if (!empty($launchdatabrand)) {
        foreach ($launchdatabrand as $tempdata) {
            $arraycomp_elements3['name'] = $tempdata['brand'];
            $arraycomp_elements3['count'] = $tempdata['count'];
            $test[] = $arraycomp_elements3;
        }
        $componentResultSet3[] = array("comp_id" => '2', "component_type" => '1004', "search_filters" => "Yes", "filter_name" => "brand", "elements_data" => $test);
    }



    if (count($componentResultSet2) > 0 && count($componentResultSet3) > 0) {
        $finalarray = array_merge($componentResultSet2, $componentResultSet3);
    } else if (count($componentResultSet2) > 0 && count($componentResultSet3) == 0) {
        $finalarray = $componentResultSet2;
    } else if (count($componentResultSet2) == 0 && count($componentResultSet3) > 0) {
        $finalarray = $componentResultSet3;
    } else {
        //$finalarray=[];
        //  $finalarray = array(array("result" => 'success', "msg" => '0'));
        $finalarray = (object) [];
        //print_r($finalarray);
    }

    return array($componentResultSet, $finalarray);
}

function searchData() {
    if (isset($_POST['primary_app_id']) && isset($_POST['primary_app_type']) && isset($_POST['authToken']) && isset($_POST['device_id']) && isset($_POST['searchItem'])) {
        $offsetprimary = 0;
        $offsetsecondary = 0;
        $offset=0;
        $limit = 10;
        $rtcount = 0;
        $ctcount = 0;
        $primaryapp_id = $_POST['primary_app_id'];
        $primaryapp_type = $_POST['primary_app_type'];
        $secondaryapp_type = '';
        $secondaryapp_id = '';
        $authToken = $_POST['authToken'];
        $device_id = $_POST['device_id']; //device_id  
        $searchItem = $_POST['searchItem'];
        $megreapp = 0;


        if (isset($_POST['offset']) && $_POST['offset'] != '') {
            $offset = $_POST['offset'];
        }
    } else {
        $responce = array();
        $response = array("result" => 'error', "msg" => 'parameter missing or wrong parameter');
        $Basearray = array("response" => $response);
        $basejson = json_encode($Basearray);
        echo $basejson;
        die;
    }

    $dbCon = content_db();
    $secarray = array();
    //  try {
    $authResult = authCheck($authToken, $device_id);

    if ($authResult == 0 || $device_id == '') {
        $response = array("result" => 'error', "msg" => 'Authentication Failed');
        $Basearray = array("response" => $response);
        $basejson = json_encode($Basearray);
        echo $basejson;
    } else {
        $filterarr = [];
        $additionalarr = [];
        if (isset($_POST['secondary_app_id']) && $_POST['secondary_app_id'] != '') {
            $megreapp = 1;
            $limit = 5;
            if (isset($_POST['rtcount']) && $_POST['rtcount'] != '') {
                $rtcount = $_POST['rtcount'];
            }
            if (isset($_POST['ctcount']) && $_POST['ctcount'] != '') {
                $ctcount = $_POST['ctcount'];
            }
            if ($primaryapp_type == "1") {
                $offsetprimary = $ctcount;
                $offsetsecondary = $rtcount;
            } else {
                $offsetprimary = $rtcount;
                $offsetsecondary = $ctcount;
            }
        } else {
            $offsetprimary = $offset;
            $offsetsecondary = $offset;
            $megreapp = 0;
            $limit = 10;
        }

        ////Primary Content App Code Start////
        if ($primaryapp_type == "1" && isset($_POST['primary_app_id'])) {
            $priarray = contentapp_search($primaryapp_id, $searchItem, $offsetprimary, $limit);
            //  $filterarr=[];
        }
        ////Primary Content App Code End////
        ////Primary Retails App Code Start////
        else if ($primaryapp_type == "2" || $primaryapp_type == "3") {

            $sortorder = " ";
            $orderby = " ";
            if (isset($_POST['orderby']) && $_POST['orderby'] != '') {
                $orderby = " order by " . $_POST['orderby'];
            }

            if ((isset($_POST['sortorder']) && $_POST['sortorder'] != '')) {
                $sortorder = " " . $_POST['sortorder'] . " ";
            }

            $priarrayval = retailapp_search($primaryapp_id, $searchItem, $offsetprimary, $sortorder, $orderby, $limit);
            $priarray = $priarrayval['0'];
            $filterarr = $priarrayval['1'];
        }
        ////Primary Retails App Code End////
        else {
            $basejson = $this->real_json_encode('', 'error', 'Invalid App Type', 405);
            echo $basejson;
        }
        //Secondary AppCode Start
        if (isset($_POST['secondary_app_id']) && isset($_POST['secondary_app_type']) && $_POST['secondary_app_id'] != '') {

            $secondaryapp_id = $_POST['secondary_app_id'];
            $secondaryapp_type = $_POST['secondary_app_type'];


            if ($secondaryapp_type == "1") {
                //            echo "SECOND APP";
                $secarray = contentapp_search($secondaryapp_id, $searchItem, $offsetsecondary, $limit);
                //
            }
            ////Primary Content App Code End////
            ////Primary Retails App Code Start////
            else if ($secondaryapp_type == "2" || $secondaryapp_type == "3") {
                $sortorder = " ";
                $orderby = " ";
                if (isset($_POST['orderby']) && $_POST['orderby'] != '') {
                    $orderby = " order by " . $_POST['orderby'];
                }

                if ((isset($_POST['sortorder']) && $_POST['sortorder'] != '')) {
                    $sortorder = " " . $_POST['sortorder'] . " ";
                }
                //   echo "SECOND APP";
                $secarrayval = retailapp_search($secondaryapp_id, $searchItem, $offsetsecondary, $sortorder, $orderby, $limit);
                $secarray = $secarrayval['0'];
                $filterarr = $secarrayval['1'];
            }
        }

        $mathspri = count($priarray);
        $mathssec = count($secarray);
        if ($mathspri < 5 || $mathssec < 5) {
            
        } else {
            $rtcount = $rtcount + 5;
            $ctcount = $ctcount + 5;
        }
        if ($megreapp == 1) {

            if ($mathspri < 5 && $primaryapp_type == "1") {
                $newlimit = 5 - $mathspri;
                $newoffset = $offsetsecondary + 5;
                $rtcount = $newoffset + $newlimit;
                $ctcount = $ctcount + $mathspri;


                $additionalarr = retailapp_search($secondaryapp_id, $searchItem, $newoffset, $sortorder, $orderby, $newlimit);
                $priarray = array_merge($priarray, $additionalarr['0']);
            }
            if ($mathssec < 5 && $secondaryapp_type == "1") {
                $newlimit = 5 - $mathssec;
                $newoffset = $offsetprimary + 5;
                $rtcount = $newoffset + $newlimit;
                $ctcount = $ctcount + $mathssec;

                $additionalarr = retailapp_search($primaryapp_id, $searchItem, $newoffset, $sortorder, $orderby, $newlimit);
                $secarray = array_merge($secarray, $additionalarr['0']);
            }

            if ($mathspri < 5 && $primaryapp_type != "1") {
                $newlimit = 5 - $mathspri;
                $newoffset = $offsetsecondary + 5;
                $ctcount = $ctcount + $newlimit;

                $ctcount = $newoffset + $newlimit;
                $rtcount = $rtcount + $mathspri;

                $additionalarr = contentapp_search($secondaryapp_id, $searchItem, $newoffset, $newlimit);
                $priarray = array_merge($priarray, $additionalarr);
            }
            if ($mathssec < 5 && $secondaryapp_type != "1") {

                $newlimit = 5 - $mathssec;
                $newoffset = $offsetprimary + 5;
                $ctcount = $newoffset + $newlimit;
                $rtcount = $rtcount + $mathssec;

                $additionalarr = contentapp_search($primaryapp_id, $searchItem, $newoffset, $newlimit);

                $secarray = array_merge($secarray, $additionalarr);
            }
        }
        if (count($priarray) > 0 && count($secarray) > 0) {
            $finalarray = array_merge($secarray, $priarray);
        } else if (count($priarray) > 0 && count($secarray) == 0) {
            $finalarray = $priarray;
        } else if (count($priarray) == 0 && count($secarray) > 0) {
            $finalarray = $secarray;
        } else {
            // $finalarray = array("result" => 'success', "msg" => '0');
            $finalarray = [];
        }

        if ($megreapp == 1) {
            if ((count($finalarray) == 10)) {
                $pageStatus = 'cof';
            } else {
                $pageStatus = 'eof';
            }
        } else {
            if (($mathspri == 10)) {
                $pageStatus = 'cof';
            } else {
                $pageStatus = 'eof';
            }
        }

        if (array_key_exists('0', $filterarr)) {
            
        } else {
            $filterarr = [];
        }
        $appQueryData = "select *  from app_data where app_id=:appid";
        $app_screenData = $dbCon->prepare($appQueryData);
        $app_screenData->bindParam(':appid', $primaryapp_id, PDO::PARAM_STR);
        $app_screenData->execute();
        $result_screenData = $app_screenData->fetch(PDO::FETCH_OBJ);
        if ($result_screenData != '') {
            $background_color = $result_screenData->background_color;
            $background_image = $result_screenData->background_image;
            if ($background_color == NULL) {
                $background_color = '';
            }
            if ($background_image == NULL) {
                $background_image = '';
            }
        } else {
            $background_color = 0;
            $background_image = 0;
        }

        echo preg_replace('/("\w+"):(\d+)/', '\\1:"\\2"', preg_replace('/\: *([0-9]+\.:?[0-9e+\-]*)/', ':"\\1"', json_encode((
                                array(
                                    "response" => array(
                                        "result" => "success",
                                        "msg" => ""
                                    ),
                                    "pagination" => $pageStatus,
                                    "rtcount" => $rtcount,
                                    "ctcount" => $ctcount,
                                    "screen_properties" => array(
                                        "background_color" => $background_color,
                                        "background_image_url" => $background_image,
                                        "popup_flag" => "0",
                                        "title" => "Searched Product",
                                    ),
                                    "filterItems" => $filterarr,
                                    "search_data" => $finalarray
                                )
                                ), JSON_NUMERIC_CHECK)));
    }
}

// POST route
$app->post(
        '/post', function () {
    echo 'This is a POST route';
}
);

// PUT route
$app->put(
        '/put', function () {
    echo 'This is a PUT route';
}
);

// PATCH route
$app->patch('/patch', function () {
    echo 'This is a PATCH route';
});

// DELETE route
$app->delete(
        '/delete', function () {
    echo 'This is a DELETE route';
}
);

/**
 * Step 4: Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the HTTP client.
 */
$app->run();
