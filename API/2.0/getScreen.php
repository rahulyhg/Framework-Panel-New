<?php

/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */
require 'Slim/Slim.php';
require 'includes/db.php';
\Slim\Slim::registerAutoloader();
/**
 * Step 2: Instantiate a Slim application
 *
 * This example instantiates a Slim application using
 * its default settings. However, you will usually configure
 * your Slim application now by passing an associative array
 * of setting names and values into the application constructor.
 */
$app = new \Slim\Slim();
$app->post('/screen', 'screen');

/**
 * Step 3: Define the Slim application routes.
 * Here we define several Slim application routes that respond.
 * to appropriate HTTP request methods. In this example, the second.
 * argument for `Slim::get`, `Slim::post`, `Slim::put`, `Slim::patch`, and `Slim::delete`.
 * is an anonymous function.
 */
function authCheck($authToken, $device_id) {
    $dbCon = content_db();
    //  $auth_query = "select id from user_appdata where user_name=$device_id and auth_token='" . $authToken . "'";
    $auth_query = "select u.id from users u left join user_appdata uad on u.id=uad.user_id where uad.auth_token=:auth and u.device_id=:deviceid";
    $auth_queryExecution = $dbCon->prepare($auth_query);
    $auth_queryExecution->bindParam(':auth', $authToken, PDO::PARAM_STR);
    $auth_queryExecution->bindParam(':deviceid', $device_id, PDO::PARAM_STR);
    $auth_queryExecution->execute();
    $result_auth = $auth_queryExecution->rowCount(PDO::FETCH_NUM);
    return $result_auth;
}

function screen() {
    if (isset($_POST['app_id']) && isset($_POST['authToken']) && isset($_POST['device_id']) && isset($_POST['screen_id'])) {
        $offset = 0;
        $pagelimit = 15;
        $Apiversion = 1;
        $app_idString = $_POST['app_id'];
        $authToken = $_POST['authToken'];
        $device_id = $_POST['device_id']; //uuid  
        $screenId = $_POST['screen_id'];
        $linkTocatalogue='0';
        $catalougeType='1';
        if (isset($_POST['offset']) && $_POST['offset'] != '') {
            $offset = $_POST['offset'];
        }

        if (isset($_POST['api_version']) && $_POST['api_version'] == '1.9') {
            $pagelimit = 30;
        }
        if (isset($_POST['api_version']) && $_POST['api_version'] != '') {
            $Apiversion = $_POST['api_version'];
        }
        $restrictComponent = 0;

        $restrictComponent4 = '1.5';
        $restrictComponent22 = '1.5';
        $restrictComponent59 = '1.9';
        $restrictComponent58 = '1.8';
        $restrictComponent52 = '1.9';
        $restrictComponent36 = '1.9';
        $restrictComponent40 = '1.9';
        $restrictComponent47 = '1.9';
        $restrictComponent49 = '1.9';
        $restrictComponent50 = '1.9';
        $restrictComponent60 = '1.9';
        $restrictComponent61 = '1.9';
        $restrictComponent62 = '1.9';
        $restrictComponent63 = '1.9';
        $restrictComponent40 = '1.9';
        $restrictComponent47 = '1.9';
        $restrictComponent49 = '1.9';
        $restrictComponent50 = '1.9';
    } else {
        $responce = array();
        $response = array("result" => 'error', "msg" => 'parameter missing or wrong parameter');
        $Basearray = array("response" => $response);
        $basejson = json_encode($Basearray);
        echo $basejson;
        die;
    }
    $dbCon = content_db();
    $result_properties = '';
    $data = '';
    $screen = '';
    $screenNavigation = '';
    $screenCompNavigation = '';
    $componentResultSet = '';
    $isDrawerEnable = 0;
    $countofComponemt = 0;
    $componentResult = '';
    $x = 0;
    $bgColor = '';
    $phoneNumber = '';
    $email = '';
    $itemId = '';

    $authResult = authCheck($authToken, $device_id);
    if ($authResult == 0 || $device_id == '') {
        $response = array("result" => 'error', "msg" => '0');
        $Basearray = array("response" => $response);
        $basejson = json_encode($Basearray);
        echo $basejson;
    } else {
        $appQueryData = "select *  from app_data where app_id=:appid";
        $app_screenData = $dbCon->prepare($appQueryData);
        $app_screenData->bindParam(':appid', $app_idString, PDO::PARAM_STR);
        $app_screenData->execute();
        $result_screenData = $app_screenData->fetch(PDO::FETCH_OBJ);
        $jump_toApp = 0;
        $jumpAppType = '2';
        if ($result_screenData != '') {
            $app_id = $result_screenData->id;
            $email = $result_screenData->contactus_email;
            $phoneNumber = $result_screenData->contactus_phone;
            $jump_to = $result_screenData->jump_to;
            if ($jump_to == 1) {
                $jump_toApp2 = $result_screenData->jump_to_app_id;

                $appQueryData1 = "select *  from app_data where id=:appid";
                $app_screenData2 = $dbCon->prepare($appQueryData1);
                $app_screenData2->bindParam(':appid', $jump_toApp2, PDO::PARAM_STR);
                $app_screenData2->execute();
                $result_screenData2 = $app_screenData2->fetch(PDO::FETCH_OBJ);
                if ($result_screenData2 != '') {
                    $jump_toApp = $result_screenData2->app_id;
                    $jumpAppType = $result_screenData2->type_app;
                }
            }
        } else {
            $app_id = 0;
        }

        $appQueryNavigation = "select *  from screen_title_id where app_id=:appid and id=:screenid";
        $app_screenNavigationquery = $dbCon->prepare($appQueryNavigation);
        $app_screenNavigationquery->bindParam(':appid', $app_id, PDO::PARAM_INT);
        $app_screenNavigationquery->bindParam(':screenid', $screenId, PDO::PARAM_INT);
        $app_screenNavigationquery->execute();
        $result_screenNavigation = $app_screenNavigationquery->fetch(PDO::FETCH_OBJ);
        $bgColor = $result_screenNavigation->background_color;
        $response = array("result" => 'success', "msg" => '');
        if ($result_screenNavigation->event_type == 1) {
            $appEventQuery = "select ea.id,ea.component_type_id,ea.componentfieldoption_id,
dt.customname as component_name,dt.field_attributes,dt.sequence,dt.field_type,
ea.font_size,ea.font_color,ea.font_typeface
 from app_event_attribute ea
left join customfielddata dt on (ea.component_type_id=dt.component_type_id and ea.componentfieldoption_id=dt.componentfieldoption_id)
where ea.screen_id=:screenid";
            $app_eventNavigationquery = $dbCon->prepare($appEventQuery);
            $app_eventNavigationquery->bindParam(':screenid', $screenId, PDO::PARAM_INT);
            $app_eventNavigationquery->execute();
            $result_event = $app_eventNavigationquery->fetchAll(PDO::FETCH_OBJ);
            $arraycomp_elements = array();
            $ys = count($result_event);
            foreach ($result_event as $resultEventSet) {
                if ($resultEventSet->component_name == 'title') {
                    $titlefont_color = $resultEventSet->font_color;
                    $titlefont_size = $resultEventSet->font_size;
                    $titlefont_typeface = $resultEventSet->font_typeface;
                } else {
                    $datefont_color = $resultEventSet->font_color;
                    $datefont_size = $resultEventSet->font_size;
                    $datefont_typeface = $resultEventSet->font_typeface;
                }
                $compType = $resultEventSet->component_type_id;
                $linkTo = '';
            }
            if ($titlefont_size == '0' || $titlefont_size == '') {
                $titlefont_size = '14';
            }
            $appEventAllQuery = "SELECT ad.id,ad.app_id,ad.screen_id,ar.title,ar.heading,ar.description,ad.linkto_screenid,ar.start_datetime,ar.end_datetime,
ar.image1,ar.image2,ar.allday,ar.event_type_id,et.NAME AS event_type_name,ar.updatable
 FROM app_event_rel ad LEFT JOIN event_data ar ON ad.event_data_id=ar.id LEFT JOIN event_type et ON ar.event_type_id=et.id
WHERE ad.screen_id=:screenid AND ad.app_id=:appid AND DATE(start_datetime)>=CURDATE() order by ar.start_datetime ASC limit $offset,15";

            $app_AlleventNavigationquery = $dbCon->prepare($appEventAllQuery);
            $app_AlleventNavigationquery->bindParam(':appid', $app_id, PDO::PARAM_INT);
            $app_AlleventNavigationquery->bindParam(':screenid', $screenId, PDO::PARAM_INT);
            $app_AlleventNavigationquery->execute();
            $result_eventAll = $app_AlleventNavigationquery->fetchAll(PDO::FETCH_OBJ);

            $posNumber = 1;
            foreach ($result_eventAll as $resultAllEventSet) {


                $posNumber++;
                $pos = $posNumber + $offset;
                $date = strtotime($resultAllEventSet->start_datetime);
                $date1 = strtotime($resultAllEventSet->end_datetime);
                if ($resultAllEventSet->event_type_id != 0) {
                    $eventhours = date('H', $date);
                    if ($eventhours == 00) {
                        $myTime = date('H:i', $date);
                        //  $myTime = date_format($date, 'H:i');
                    } else {
                        $myTime = date('H:i', $date);
                        //  $myTime = date_format($date, 'm-d H:i');
                    }
                    //$mydate= date('m-d-Y', $date);
                    $mydate = date('Y-m-d', $date);
                    $eventhours1 = date('H', $date1);
                    if ($eventhours1 == 00) {
                        $myTime1 = date('H:i', $date1);
                        //  $myTime1 = date_format($date1, 'H:i');
                    } else {
                        $myTime1 = date('H:i', $date1);
                        // $myTime1 = date_format($date1, 'H:i');
                    }
                    $myTimeFinal = $mydate . ' ' . $myTime . '-' . $myTime1;


                    $dt = new DateTime($resultAllEventSet->start_datetime);
                    $date = $dt->format('d');
                    $differTime = date("d") - $date;
                    //  if ($differTime == 0) {
                    $arraycomp_elements['card_elevation'] = '2';
                    $arraycomp_elements['card_corner_radius'] = '3';
                    $arraycomp_elements['background_color'] = '#FFFFFF';
                    ;
                    $arraycomp_elements["image"] = array("media_type" => 'image', "image_url" => $resultAllEventSet->image2, "height" => '60', "width" => '60');
                    $arraycomp_elements["left_label_up"] = array("text" => $resultAllEventSet->heading, "font_size" => $titlefont_size, "font_color" => $titlefont_color, "font_typeface" => $titlefont_typeface);
                    $arraycomp_elements["left_label_down"] = array("text" => $myTimeFinal, "font_size" => $datefont_size, "font_color" => $datefont_color, "font_typeface" => $datefont_typeface);
                    $arraycomp_elements["right_label_up"] = array("text" => '', "font_size" => $titlefont_size, "font_color" => $titlefont_color, "font_typeface" => $titlefont_typeface);
                    $arraycomp_elements["right_label_down"] = array("text" => '', "font_size" => $datefont_size, "font_color" => $datefont_color, "font_typeface" => $datefont_typeface);
                    $compId = $resultAllEventSet->id;
                    $headingEvent = array();
                    if ($compType == '41') {
                        $compType = '9';
                    }
                    $componentResultSet[] = array("comp_id" => $compId, "comp_type" => $compType, "tag" => '', "linkto_screenid" => $resultAllEventSet->linkto_screenid, "pos" => "$pos", "is_link_to_catalogue" => "$linkTocatalogue", "catalogue_component" => array("app_type" => "$jumpAppType", "screen_type" => "$catalougeType", "category_id" => "$itemId", "catalogue_app_id" => "$jump_toApp"), "comp_elements" => $arraycomp_elements);
                    //  }
                } else {
                    $eventhours = date('H', $date);
                    if ($eventhours == 00) {
                        $myTime = date('M j, Y', $date);
                        //  $myTime = date_format($date, 'M j, Y');
                    } else {
                        $myTime = date('M j, Y', $date);

                        // $myTime = date_format($date, 'M j, Y');
                    }
                    $myTime1 = '';
                    $myTimeFinal = $myTime;
                    $arraycomp_elements['card_elevation'] = '2';
                    $arraycomp_elements['card_corner_radius'] = '3';
                    $arraycomp_elements['background_color'] = '#FFFFFF';
                    $arraycomp_elements["image"] = array("media_type" => 'image', "image_url" => $resultAllEventSet->image2, "height" => $titlefont_size, "width" => $titlefont_size);
                    $arraycomp_elements["left_label_up"] = array("text" => $resultAllEventSet->heading, "font_size" => $titlefont_size, "font_color" => $titlefont_color, "font_typeface" => $titlefont_typeface);
                    $arraycomp_elements["left_label_down"] = array("text" => $myTimeFinal, "font_size" => $datefont_size, "font_color" => $datefont_color, "font_typeface" => $datefont_typeface);
                    $arraycomp_elements["right_label_up"] = array("text" => '', "font_size" => $titlefont_size, "font_color" => $titlefont_color, "font_typeface" => $titlefont_typeface);
                    $arraycomp_elements["right_label_down"] = array("text" => '', "font_size" => $datefont_size, "font_color" => $datefont_color, "font_typeface" => $datefont_typeface);
                    $compId = $resultAllEventSet->id;
                    $headingEvent = array();
                    if ($compType == '41') {
                        $compType = '9';
                    }
                    $componentResultSet[] = array("comp_id" => $compId, "comp_type" => $compType, "tag" => '', "linkto_screenid" => $resultAllEventSet->linkto_screenid, "pos" => "$pos", "is_link_to_catalogue" => "$linkTocatalogue", "catalogue_component" => array("app_type" => "$jumpAppType", "screen_type" => "$catalougeType", "category_id" => "$itemId", "catalogue_app_id" => "$jump_toApp"), "comp_elements" => $arraycomp_elements);
                }

//                    $headingEvent[''] = array("heading" => array("text" => $resultAllEventSet->heading, "font_size" => $titlefont_size, "font_color" => $titlefont_color, "font_typeface" =>$titlefont_typeface), "subheading" => array("text" => $resultAllEventSet->heading, "font_size" => $datefont_size, "font_color" => $datefont_color, "font_typeface" => $datefont_typeface));
            }
            $countofComponemt = count($componentResultSet);
        } else {
            if ($result_screenNavigation->screen_type != 21) {
                $componentQuery = "SELECT 
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
	cv.display,
    cv.defaultselect,
    cv.component_position,
    cv.description,
    cv.datevalue,
    cv.image_url,
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
    app_id = :appid AND screen_id = :screenid
        AND component_no IN (select * from (select distinct(component_no) from componentfieldvalue where app_id='" . $app_id . "' and screen_id='" . $screenId . "' order by component_no limit  $offset,15 ) as compcount)
ORDER BY component_no , list_no , componentarraylink_id";
            } else {
                $componentQuery = "SELECT 
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
	cv.display,
    cv.defaultselect,
    cv.component_position,
    cv.description,
    cv.datevalue,
    cv.image_url,
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
    app_id = :appid AND screen_id = :screenid
        AND component_no IN (select * from (select distinct(component_no) from componentfieldvalue where app_id='" . $app_id . "' and screen_id='" . $screenId . "' order by component_no ) as compcount)
ORDER BY component_no , list_no , componentarraylink_id";
            }

            $componentQueryExecution = $dbCon->prepare($componentQuery);
            $componentQueryExecution->bindParam(':appid', $app_id, PDO::PARAM_INT);
            $componentQueryExecution->bindParam(':screenid', $screenId, PDO::PARAM_INT);
            $componentQueryExecution->execute();
            $componentResult = $componentQueryExecution->fetchAll(PDO::FETCH_OBJ);



            $lastIdofCompType = 0;
            $x = 0;
            $y = 0;
            $mm = 0;
            $samecompid = 0;
            $arraycomp_elements = array();
            $countOfComponentAll = count($componentResult);
            $myCount = 0;
            $listTupple = 0;
            $listDAtaArray3 = array();
            $j = 0;
            $listDAtaArray2 = array();
            $headingEvent = array();

            function clean($string) {
                // Replaces all spaces with hyphens.

                return preg_replace('/[^A-Za-z0-9<>,!`*()<>;&^#%\':._\/\-]/', ' ', $string); // Removes special chars.
            }

            $adid = rand(1, 2);
            $AdData = "select * from self_advert where id='" . $adid . "'";
            $appQueryAdData = $dbCon->query($AdData);
            $rowappQueryAdData = $appQueryAdData->fetch(PDO::FETCH_ASSOC);

            $resultAttributes['tab_link'] = $rowappQueryAdData['linkto_url'];
            $resultAttributes['tab_heading'] = $rowappQueryAdData['image_url'];
            $resultAttributes['tab_headingData'] = $rowappQueryAdData['heading'];
            $resultAttributes['tab_text'] = $rowappQueryAdData['subheading'];
            $clr = '#000000';
            $typeface = 'Arial';
            $evname = 'No Event';
            foreach ($componentResult as $resultSet) {
                $y = $resultSet->component_no;
                $resrictVersion = 0;
                $currentDate=date('Y-m-d h:m:s');
                $restrictCompName = 'restrictComponent' . $resultSet->component_type_id;
                // $compareRestrict=$$restrictCompName;

                if (isset($$restrictCompName)) {
                    $resrictVersion = $$restrictCompName;
                }
                if ($resultSet->component_attr_name == NULL )
                {
                   $expiry= $resultSet->datevalue;
                 if($expiry == '0000-00-00 00:00:00' || $expiry == null || $expiry =='')
                 {
                    $expiry1=$currentDate;
                 }
                 else
                 {
                  $expiry1=$resultSet->datevalue;  
                 }
                 $expiry =  strtotime($expiry1);
                 
                    $today1=$currentDate;
                 $today =  strtotime($today1);
                   
                }
               
                
                if ($resrictVersion <= $Apiversion && $expiry >= $today) {

                    if ($x == $y || $x == 0) {
                        $x = $resultSet->component_no;
                        if ($resultSet->component_attr_name == NULL) {
                            $arraycomp_elements['background_color'] = $resultSet->backgroundcolor;
                            $arraycomp_elements['card_elevation'] = $resultSet->card_elevation;
                            $arraycomp_elements['span'] = $resultSet->display;
                            $arraycomp_elements['card_corner_radius'] = $resultSet->card_corner_radius;
                            $catalougeArray = json_decode($resultSet->description);

                            $product = '';
                            $subcategoryVal = '';
                            $subcategory = '';
                            $catalougeType = 2;
                            $linkTocatalogue = 0;


                            if (isset($catalougeArray->main_cat)) {

                                $maincategory = $catalougeArray->main_cat;
                                if ($maincategory != '') {
                                         $itemId = $catalougeArray->main_cat;
                                        if ($itemId == 0) {
                                            $catalougeType = 1;
                                        } else {
                                            $catalougeType = 2;
                                        }
                                    $linkTocatalogue = 1;
                                }
                                if (isset($catalougeArray->is_child)) {
                                    $isChild = $catalougeArray->is_child;

                                    if ($isChild == 0) {
                                        $catalougeType = 3;
                                    }
                                }
                                $subcategory = count($catalougeArray->subcategory);
                                if (isset($catalougeArray->subcategory)) {
                                    for ($subcat = 0; $subcat < $subcategory; $subcat++) {
                                        $itemId = $catalougeArray->subcategory[$subcat];
                                    }
                                }

                                if (isset($catalougeArray->product)) {
                                    $product = $catalougeArray->product;
                                    if ($product != '') {
                                        $itemId = $catalougeArray->product;
                                        $catalougeType = 4;
                                    }
                                }
                            }
                        }

                        if ($resultSet->item_orientation != NULL) {
                            $arraycomp_elements['item_orientation'] = $resultSet->item_orientation;
                        }
                        $compId = $resultSet->component_no;
                        $compType = $resultSet->component_type_id;
                        $linkTo = $resultSet->linkto_screenid;
                        $pos = $resultSet->component_position;
                        $myArray2 = array();
                        $listDatavalue = array();

                        if ($resultSet->is_except == 1 && $resultSet->description != NULL && $resultSet->description != '') {
                            $evname = $resultSet->description;

                            $clr = $resultSet->font_color;

                            $typeface = $resultSet->font_typeface;
                        }
                        if ($resultSet->is_except == 1 && $resultSet->component_attr_name != NULL && $resultSet->description == NULL) {
                            $app_event_query = "SELECT ad.app_id,ad.screen_id,ar.title,ar.heading,ar.description,ar.start_datetime,ar.end_datetime,
ar.image1,ar.image2,ar.allday,ar.event_type_id,et.NAME AS event_type_name,ar.updatable
 FROM app_event_rel ad LEFT JOIN event_data ar ON ad.event_data_id=ar.id LEFT JOIN event_type et ON ar.event_type_id=et.id
WHERE ad.screen_id=:screenid AND ad.app_id=:appid AND DATE(start_datetime)>=CURDATE() order by ar.start_datetime ASC
LIMIT 3
";
                            $app_eventExecution = $dbCon->prepare($app_event_query);
                            $app_eventExecution->bindParam(':appid', $app_id, PDO::PARAM_INT);
                            $app_eventExecution->bindParam(':screenid', $resultSet->linkto_screenid, PDO::PARAM_INT);
                            $app_eventExecution->execute();
                            $result_event = $app_eventExecution->fetchAll(PDO::FETCH_OBJ);
                            $eventsize = $resultSet->font_size;
                            if ($eventsize == '0') {
                                $eventsize = '14';
                            }
                            $eventclr = $resultSet->font_color;
                            if ($eventclr == '') {
                                $eventclr = $clr;
                            }


                            foreach ($result_event as $eventSet) {
                                $date = strtotime($eventSet->start_datetime);
                                $date1 = strtotime($eventSet->end_datetime);
                                if ($eventSet->event_type_id != 0) {
                                    $eventhours = date('H', $date);
                                    if ($eventhours == 00) {
                                        $myTime = date('H:i', $date);
                                        // $myTime = date_format($date, 'H:i');
                                    } else {
                                        $myTime = date('H:i', $date);
                                        //  $myTime = date_format($date, 'm-d H:i');
                                    }
                                    $eventhours1 = date('H', $date1);
                                    if ($eventhours1 == 00) {
                                        $myTime1 = date('H:i', $date1);
                                        //   $myTime1 = date_format($date1, 'H:i');
                                    } else {
                                        $myTime1 = date('H:i', $date1);
                                        //$myTime1 = date_format($date1, 'm-d H:i');
                                    }
                                    $mydate = date('m-d-Y', $date);
                                    $myTimeFinal = $mydate . ' ' . $myTime . '-' . $myTime1;
                                } else {
                                    $myTime = date('M j, Y', $date);
                                    //   $myTime = date_format($date, 'M j, Y');

                                    $myTime1 = '';
                                    $myTimeFinal = $myTime;
                                }

                                // $date = date_create($eventSet->start_datetime);
                                $headingEvent[] = array("heading" => array("text" => $eventSet->title, "font_size" => "$eventsize", "font_color" => "$eventclr", "font_typeface" => "$typeface"), "subheading" => array("text" => $myTimeFinal, "font_size" => "$eventsize", "font_color" => "$eventclr", "font_typeface" => "$typeface"));
//                               $headingEvent[]= array("subheading"=>array("text" => $eventSet->start_datetime, "font_size" => $resultSet->font_size, "font_color" => $resultSet->font_color, "font_typeface" => $resultSet->font_typeface));                               
                            }

                            $arraycomp_elements['list_array'] = $headingEvent;
                            $arraycomp_elements['heading'] = array("text" => "$evname", "font_size" => "20", "font_color" => "$clr", "font_typeface" => "$typeface");

                            $headingEvent = array();
                        } elseif ($resultSet->component_attr_name != NULL && $resultSet->list_type != 1) {

                            //  $arraycomp_elements[$resultSet->component_attr_name] = array("text" => $resultSet->description, "font_size" => $resultSet->font_size, "font_color" => $resultSet->font_color, "font_typeface" => $resultSet->font_typeface);
                            if ($resultSet->component_type_name == 'Calendar View Card ') {
                                $curDate = date("d");
                                if ($resultSet->component_attr_name == 'date') {
                                    $myArray = explode(',', $resultSet->field_attributes);
                                    $myArrayCount = count($myArray);
                                    for ($i = 0; $i < $myArrayCount; $i++) {
                                        if ($myArray[$i] == 'text') {

                                            $myArray2[$myArray[$i]] = $curDate;
                                        } elseif ($myArray[$i] == 'media_type') {
                                            $valueText = 'description';
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        } elseif ($myArray[$i] == 'icon') {
                                            $valueText = 'description';
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        } elseif ($myArray[$i] == 'background_color') {
                                            $valueText3 = 'backgroundcolor';
                                        } else {
                                            $valueText = $myArray[$i];
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        }
                                    }
                                } elseif ($resultSet->component_attr_name == 'subheading') {
                                    $myArray = explode(',', $resultSet->field_attributes);
                                    $myArrayCount = count($myArray);
                                    for ($i = 0; $i < $myArrayCount; $i++) {
                                        if ($myArray[$i] == 'text') {
                                            $myArray2[$myArray[$i]] = date('F');
                                        } elseif ($myArray[$i] == 'media_type') {
                                            $valueText = 'description';
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        } elseif ($myArray[$i] == 'icon') {
                                            $valueText = 'description';
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        } elseif ($myArray[$i] == 'background_color') {
                                            $valueText3 = 'backgroundcolor';
                                        } else {
                                            $valueText = $myArray[$i];
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        }
                                    }
                                } elseif ($resultSet->component_attr_name == 'description') {
                                    $myArray = explode(',', $resultSet->field_attributes);
                                    $myArrayCount = count($myArray);
                                    for ($i = 0; $i < $myArrayCount; $i++) {
                                        if ($myArray[$i] == 'text') {
                                            $appdescription = "SELECT title FROM event_data ar LEFT JOIN event_type et ON ar.event_type_id=et.id WHERE ar.event_type_id=5 AND DATE(start_datetime)=CURDATE()LIMIT 1";
                                            $app_desExecution = $dbCon->query($appdescription);
                                            $resudes = $app_desExecution->fetchAll(PDO::FETCH_OBJ);
                                            $countDesNav = count($resudes);
                                            if ($countDesNav != 0) {
                                                $TitleDescription = $resudes[0]->title;
                                                $myArray2[$myArray[$i]] = $TitleDescription;
                                            } else {
                                                $myArray2[$myArray[$i]] = '';
                                            }
                                        } elseif ($myArray[$i] == 'media_type') {
                                            $valueText = 'description';
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        } elseif ($myArray[$i] == 'icon') {
                                            $valueText = 'description';
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        } elseif ($myArray[$i] == 'background_color') {
                                            $valueText3 = 'backgroundcolor';
                                        } else {
                                            $valueText = $myArray[$i];
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        }
                                    }
                                } elseif ($resultSet->component_attr_name == 'bottom_text_up') {
                                    $myArray = explode(',', $resultSet->field_attributes);
                                    $myArrayCount = count($myArray);
                                    for ($i = 0; $i < $myArrayCount; $i++) {
                                        if ($myArray[$i] == 'text') {
                                            $appdescription = "SELECT title FROM event_data ar LEFT JOIN app_event_rel et ON ar.id=et.event_data_id WHERE et.app_id=:appid AND DATE(start_datetime)=CURDATE() LIMIT 1";
                                            $app_desExecution = $dbCon->prepare($appdescription);
                                            $app_desExecution->bindParam(':appid', $app_id, PDO::PARAM_STR);
                                            $app_desExecution->execute();
                                            $resudes = $app_desExecution->fetchAll(PDO::FETCH_OBJ);
                                            $countDesNav = count($resudes);
                                            if ($countDesNav != 0) {
                                                if (isset($resudes[0]->start_datetime)) {
                                                    $date = date_create($resudes[0]->start_datetime);
                                                    $TitleDescription = $resudes[0]->title . " " . date_format($date, 'h:i');
                                                } else {
                                                    $date = '';
                                                    $TitleDescription = '';
                                                }
                                                $myArray2[$myArray[$i]] = $TitleDescription;
                                            } else {
                                                $myArray2[$myArray[$i]] = 'No Event For Today';
                                            }
                                        } elseif ($myArray[$i] == 'media_type') {
                                            $valueText = 'description';
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        } elseif ($myArray[$i] == 'icon') {
                                            $valueText = 'description';
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        } elseif ($myArray[$i] == 'background_color') {
                                            $valueText3 = 'backgroundcolor';
                                        } else {
                                            $valueText = $myArray[$i];
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        }
                                    }
                                } elseif ($resultSet->component_attr_name == 'bottom_text_down') {
                                    $myArray = explode(',', $resultSet->field_attributes);
                                    $myArrayCount = count($myArray);
                                    for ($i = 0; $i < $myArrayCount; $i++) {
                                        if ($myArray[$i] == 'text') {
                                            $appdescription = "SELECT start_datetime,title FROM event_data ar LEFT JOIN app_event_rel et ON ar.id=et.event_data_id WHERE et.app_id=:appid AND DATE(start_datetime)=CURDATE() LIMIT 1";
                                            $app_desExecution = $dbCon->prepare($appdescription);
                                            $app_desExecution->bindParam(':appid', $app_id, PDO::PARAM_STR);
                                            $app_desExecution->execute();
                                            $resudes = $app_desExecution->fetchAll(PDO::FETCH_OBJ);
                                            $countDesNav = count($resudes);
                                            if ($countDesNav != 0) {
                                                if (isset($resudes[0]->start_datetime) && isset($resudes[0]->title)) {
                                                    $date = date_create($resudes[0]->start_datetime);
                                                    $TitleDescription = $resudes[0]->title . " " . date_format($date, 'h:i');
                                                } else {
                                                    $date = '';
                                                    $TitleDescription = '';
                                                }
                                                $myArray2[$myArray[$i]] = $TitleDescription;
                                            } else {
                                                $myArray2[$myArray[$i]] = '';
                                            }
                                        } elseif ($myArray[$i] == 'media_type') {
                                            $valueText = 'description';
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        } elseif ($myArray[$i] == 'icon') {
                                            $valueText = 'description';
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        } else {
                                            $valueText = $myArray[$i];
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        }
                                    }
                                } elseif ($resultSet->component_attr_name == 'text_icon_bottom_left') {
                                    $myArray = explode(',', $resultSet->field_attributes);
                                    $myArrayCount = count($myArray);
                                    for ($i = 0; $i < $myArrayCount; $i++) {
                                        if ($myArray[$i] == 'text') {
                                            $TitleDescription = 'e348';
//                                        $appdescription = "select  image1 from event_data ed   left join     event_type et    on    ed.event_type_id=et.id  where event_type_id=6 and start_datetime BETWEEN'" . date("Y-m-d") . "' AND '" . date("Y-m-d") . "' limit 1; ";
//
//                                        $app_desExecution = $dbCon->query($appdescription);
//                                        $resudes = $app_desExecution->fetchAll(PDO::FETCH_OBJ);
                                            //    $countDesNav = count($resudes);
                                            //  if ($countDesNav != 0) {
                                            // $TitleDescription = $resudes[0]->image1;
                                            $TitleDescription = 'e348';
                                            $myArray2[$myArray[$i]] = $TitleDescription;
                                            // } else {
                                            //   $myArray2[$myArray[$i]] = '';
                                            // }
                                        } elseif ($myArray[$i] == 'media_type') {
                                            $valueText = 'description';
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        } elseif ($myArray[$i] == 'icon') {
                                            $valueText = 'description';
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        } else {
                                            $valueText = $myArray[$i];
                                            $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                        }
                                    }
                                } else {
                                    $myArray = explode(',', $resultSet->field_attributes);
                                    $myArrayCount = count($myArray);
                                    for ($i = 0; $i < $myArrayCount; $i++) {
                                        if ($myArray[$i] == 'text') {
                                            $valueText = 'description';
                                        } elseif ($myArray[$i] == 'media_type') {
                                            $valueText = 'description';
                                        } elseif ($myArray[$i] == 'icon') {
                                            $valueText = 'description';
                                        } else if ($myArray[$i] == 'background_color') {
                                            $valueText3 = 'backgroundcolor';
                                        } else {
                                            $valueText = $myArray[$i];
                                        }
                                        $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                    }
                                }
                                $arraycomp_elements[$resultSet->component_attr_name] = $myArray2;
                            } else {
                                if ($resultSet->top_level == 0) {
                                    if ($resultSet->component_type_id == '100') {
                                        $valueText = '';
                                        $myArray = explode(',', $resultSet->field_attributes);
                                        $myArrayCount = count($myArray);
                                        for ($i = 0; $i < $myArrayCount; $i++) {

                                            $valueText = $myArray[$i];
                                            if ($myArray[$i] == 'media_type') {
                                                $valueText = 'description';
                                            } elseif ($myArray[$i] == 'icon') {
                                                $valueText3 = 'description';
                                            } elseif ($myArray[$i] == 'background_color') {
                                                $valueText3 = 'backgroundcolor';
                                            } elseif ($valueText == 'image_url') {
                                                $myArray2[$myArray[$i]] = $resultAttributes['tab_heading'];
                                            } elseif ($valueText == 'action_type_id') {
                                                $myArray2[$myArray[$i]] = '10';
                                            } elseif ($valueText == 'action_data') {
                                                $myArray2[$myArray[$i]] = $resultAttributes['tab_link'];
                                            } elseif ($resultSet->component_attr_name == 'heading' && $valueText == 'text') {
                                                $myArray2[$myArray[$i]] = $resultAttributes['tab_headingData'];
                                            } elseif ($resultSet->component_attr_name == 'subheading' && $valueText == 'text') {
                                                $myArray2[$myArray[$i]] = $resultAttributes['tab_text'];
                                            } elseif ($myArray[$i] == 'text') {
                                                $myArray2[$myArray[$i]] = '';
                                            } elseif ($myArray[$i] == 'video_name') {
                                                $myArray2[$myArray[$i]] = 'action_data';
                                            } else {
                                                $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                            }
                                        }
                                        $arraycomp_elements[$resultSet->component_attr_name] = $myArray2;
                                        /* 8888888888888888888888888888888888888888888888888
                                          888888888888888888888888888888888888888888888888888
                                          ////////////////////////////////////////////////////////////////////////////////////////////////
                                          /////////////////////////////////////////////
                                          8888888888888888888888888888888888888888888888888
                                          8888888888888888888888888888888888888888888888888 */
                                    } else {

                                        $valueText = '';
                                        $myArray = explode(',', $resultSet->field_attributes);
                                        $myArrayCount = count($myArray);
                                        for ($i = 0; $i < $myArrayCount; $i++) {

                                            if ($myArray[$i] == 'text') {
                                                $valueText = 'description';
                                            } elseif ($myArray[$i] == 'media_type' ) {
                                                $valueText = 'description';
                                            } 
                                            elseif ($myArray[$i] == 'video_name') {
                                                $valueText = 'action_data';
                                            } 
                                            elseif ($myArray[$i] == 'icon') {
                                                $valueText = 'description';
                                            } elseif ($myArray[$i] == 'audio_url') {
                                                $valueText = 'video_url';
                                            } elseif ($myArray[$i] == 'audio_duration') {
                                                $valueText = 'description';
                                            } elseif ($myArray[$i] == 'background_color') {
                                                $valueText3 = 'backgroundcolor';
                                            } else {
                                                $valueText = $myArray[$i];
                                            }

                                            if ($valueText != '') {
                                                if ($Apiversion < '1.4' && $myArray[$i] == 'action_data' && $resultSet->action_type_id == '2') {
                                                    $myArray2[$myArray[$i]] = '';
                                                } else {
                                                    $myArray2[$myArray[$i]] = $resultSet->$valueText;
                                                }
                                            }
                                        }




                                        $arraycomp_elements[$resultSet->component_attr_name] = $myArray2;
                                    }
                                } else {

                                    $arraycomp_elements[$resultSet->component_attr_name] = $resultSet->description;
                                }
                            }
                        } elseif ($resultSet->list_type == 1) {
                            if ($resultSet->top_level == 0) {
                                $myArray3 = explode(',', $resultSet->field_attributes);
                                $myArrayCount3 = count($myArray3);
                                for ($ii = 0; $ii < $myArrayCount3; $ii++) {
                                    if ($myArray3[$ii] == 'text') {
                                        $valueText3 = 'description';
                                    } elseif ($myArray3[$ii] == 'media_type') {
                                        $valueText3 = 'description';
                                    } 
                                    elseif ($myArray3[$ii] == 'video_name') {
                                        $valueText3 = 'action_data';
                                    } 
                                    elseif ($myArray3[$ii] == 'icon') {
                                        $valueText3 = 'description';
                                    } elseif ($myArray3[$ii] == 'audio_url') {
                                        $valueText3 = 'video_url';
                                    } elseif ($myArray3[$ii] == 'audio_duration') {
                                        $valueText3 = 'description';
                                    } elseif ($myArray3[$ii] == 'background_color') {
                                        $valueText3 = 'backgroundcolor';
                                    } else {
                                        $valueText3 = $myArray3[$ii];
                                    }

                                    if ($myArray3[$ii] == 'action_data' && $resultSet->action_type_id == '6') {
                                        $listDatavalue['action_data'] = $resultSet->linkto_screenid;
                                    } else {

                                        $listDatavalue[$myArray3[$ii]] = $resultSet->$valueText3;
                                    }
                                }

                                if ($resultSet->list_no == $listTupple || $listTupple == 0) {
                                    $listDAtaArray[$resultSet->component_attr_name] = $listDatavalue;
                                    $listTupple = $resultSet->list_no;
                                } else {
                                    $listDAtaArray2[] = $listDAtaArray;
                                    $listTupple = $resultSet->list_no;
                                    $listDAtaArray = array();
                                    $listDAtaArray[$resultSet->component_attr_name] = $listDatavalue;
                                }
                            } else {

                                $arraycomp_elements[$resultSet->component_attr_name] = $resultSet->description;
                            }
                        }
                    } else {
                        if (count($listDAtaArray2) != 0) {
                            $listDAtaArray2[] = $listDAtaArray;
                            $myarray = $listDAtaArray2;
                            $listDAtaArray2 = array();
                            $arraycomp_elements['list_array'] = $myarray;
                            $myarray = '';
                        }

                        if ($mm == 1) {
                            $componentResultSet[] = array("comp_id" => $compId, "comp_type" => $compType, "tag" => '', "linkto_screenid" => $linkTo, "pos" => $pos, "is_link_to_catalogue" => "$linkTocatalogue", "catalogue_component" => array("app_type" => "$jumpAppType", "screen_type" => "$catalougeType", "category_id" => "$itemId", "catalogue_app_id" => "$jump_toApp"), "comp_elements" => $arraycomp_elements);
                            $mm++;
                        } else {
                            $componentResultSet[] = array("comp_id" => $compId, "comp_type" => $compType, "tag" => '', "linkto_screenid" => $linkTo, "pos" => $pos, "is_link_to_catalogue" => "$linkTocatalogue", "catalogue_component" => array("app_type" => "$jumpAppType", "screen_type" => "$catalougeType", "category_id" => "$itemId", "catalogue_app_id" => "$jump_toApp"), "comp_elements" => $arraycomp_elements);

                            $mm++;
                        }
                        $arraycomp_elements = array();

                        $x = 0;
                        // when loop ends for first componemt type this section will take background color of second component
                        if ($resultSet->component_attr_name == NULL) {
                            $arraycomp_elements['background_color'] = $resultSet->backgroundcolor;
                            $arraycomp_elements['card_elevation'] = $resultSet->card_elevation;
                            $arraycomp_elements['span'] = $resultSet->display;
                            $arraycomp_elements['card_corner_radius'] = $resultSet->card_corner_radius;
                            $catalougeArray = json_decode($resultSet->description);

                            $product = '';
                            $subcategoryVal = '';
                            $subcategory = '';
                            $catalougeType = 2;
                            $linkTocatalogue = 0;
                            if (isset($catalougeArray->main_cat)) {
                                $maincategory = $catalougeArray->main_cat;
                                if ($maincategory != '') {
                                    $itemId = $catalougeArray->main_cat;
                                    if ($maincategory != '') {
                                        $itemId = $catalougeArray->main_cat;
                                        if ($itemId == 0) {
                                            $catalougeType = 1;
                                        } else {
                                            $catalougeType = 2;
                                        }
                                        $linkTocatalogue = 1;
                                    }
                                }

                                $subcategory = count($catalougeArray->subcategory);
                                if (isset($catalougeArray->subcategory)) {
                                    for ($subcat = 0; $subcat < $subcategory; $subcat++) {
                                        $itemId = $catalougeArray->subcategory[$subcat];
                                    }
                                }
                                if (isset($catalougeArray->is_child)) {

                                    $isChild = $catalougeArray->is_child;

                                    if ($isChild == 0) {
                                        $catalougeType = 3;
                                    }
                                }
                                if (isset($catalougeArray->product)) {
                                    $product = $catalougeArray->product;
                                    if ($product != '') {
                                        $itemId = $catalougeArray->product;
                                        $catalougeType = 4;
                                    }
                                }
                            }
                        }
                    }
                    $myCount++;
                    if ($myCount == $countOfComponentAll) {
                        if (count($listDAtaArray2) != 0) {
                            $listDAtaArray2[] = $listDAtaArray;
                            $myarray = $listDAtaArray2;
                            $listDAtaArray2 = array();
                            $arraycomp_elements['list_array'] = $myarray;
                            $myarray = '';
                        }

                        $componentResultSet[] = array("comp_id" => $compId, "comp_type" => $compType, "tag" => '', "linkto_screenid" => $linkTo, "pos" => $pos, "is_link_to_catalogue" => "$linkTocatalogue", "catalogue_component" => array("app_type" => "$jumpAppType", "screen_type" => "$catalougeType", "category_id" => "$itemId", "catalogue_app_id" => "$jump_toApp"), "comp_elements" => $arraycomp_elements);
                    }
                }
               else
               {
                   $myCount++;
               }
            }
            $countofComponemt = count($componentResultSet);
//            $compList = array("card_layout" => array("display" => '', 'corner' => ''), "background" => array("type" => '', "properties" => array("color" => '')), "heading" => array("text" => '', "font_size" => '', "font_color" => '', "font_typeface" => ''), "date" => array("text" => '', "font_size" => '', "font_color" => '', "font_typeface" => ''), "subheading" => array("text" => '', "font_size" => '', "font_color" => '', "font_typeface" => ''), "description" => array("text" => '', "font_size" => '', "font_color" => '', "font_typeface" => ''), "image_bottom_left_url" => array("text" => '', "font_size" => '', "font_color" => '', "font_typeface" => ''), "image_bottom_center_url" => array("text" => '', "font_size" => '', "font_color" => '', "font_typeface" => ''), "bottom_text_up" => array("text" => '', "font_size" => '', "font_color" => '', "font_typeface" => ''), "bottom_text_down" => array("text" => '', "font_size" => '', "font_color" => '', "font_typeface" => ''));
        }
        $screenDataCount = count($result_screenNavigation);
        $maths = $countofComponemt % 15;
        if ($maths == 0) {
            $pageStatus = 'cof';
        } else {
            $pageStatus = 'eof';
        }
        if ($result_screenNavigation->screen_type == 21)
        {
           $pageStatus='eof'; 
        }
        
        if ($result_screenNavigation->screen_type == 15) {
            $backgroundImage = $result_screenNavigation->background_image;
        } else {
            $backgroundImage = '';
        }
        // $keywords  = array($result_screenNavigation->keywords );
        if ($screenDataCount == 0) {
            $Basearray = array("response" => $response, "screen_data" => array("screen_id" => $result_screenNavigation->id, "parent_id" => $result_screenNavigation->parent_id, "screen_type" => $result_screenNavigation->screen_type, "tag" => '1', "dirtyflag" => '0', "bypass" => $result_screenNavigation->is_bypass, "server_time" => $result_screenNavigation->server_time, "pagination" => $pageStatus, "screen_properties" => array("title" => $result_screenNavigation->title, "popup_flag" => $result_screenNavigation->popup_flag, "background_color" => "$result_screenNavigation->background_color", "background_image" => "$backgroundImage", "phone" => "$phoneNumber"), "comp_count" => "$countofComponemt", "comp_array" => $componentResultSet));
            $basejson = json_encode($Basearray);
            echo $basejson;
        } else {
            $Basearray = array("response" => $response, "screen_data" => array("screen_id" => $result_screenNavigation->id, "parent_id" => $result_screenNavigation->parent_id, "screen_type" => $result_screenNavigation->screen_type, "tag" => '1', "dirtyflag" => '0', "bypass" => $result_screenNavigation->is_bypass, "server_time" => $result_screenNavigation->server_time, "pagination" => $pageStatus, "screen_properties" => array("title" => $result_screenNavigation->title, "popup_flag" => $result_screenNavigation->popup_flag, "background_color" => "$result_screenNavigation->background_color", "background_image" => "$backgroundImage", "phone" => "$phoneNumber"), "comp_count" => "$countofComponemt", "comp_array" => $componentResultSet));
            $basejson = json_encode($Basearray);
            echo $basejson;
        }
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
