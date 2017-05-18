<?php
session_start();
require_once ('../../config/db.php');
$connection = new Db();
$mysqli = $connection->dbconnection();

if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
//Request identified as ajax request

    if (@isset($_SERVER['HTTP_REFERER'])) {
//HTTP_REFERER verification
        if ($_POST['token'] == $_SESSION['token']) {
//            echo 'success';
            $data = $_POST['data'];
//           print_r($data);
            $token = bin2hex(openssl_random_pseudo_bytes(16));
            $platform = $data['platform'];
            $appName = $data['appName'];
            $themeid = $_POST['themeid'];
            $catid = $_POST['catid'];
            $confirmVal = $_POST['confirm'];
            $hasid = $_POST['hasid'];

            $is_ajax = $_POST['is_ajax'];
            $username = $_SESSION['username'];
            $custid = $_SESSION['custid'];

            if (trim($custid) != '') {
                $sql = "select id from author where custid='$custid'";
                $stmt = $mysqli->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $userId = $result['id'];
               
                if ($hasid != 0) {
				  $_SESSION['appid'] = $hasid;
                    $checkname = "select * from app_data where summary='$appName' and author_id='$userId' and id!='$hasid' and deleted !=1";
                } else {
                    $checkname = "select * from app_data where summary='$appName' and author_id='$userId' and deleted !=1";
                }
                $stmt_name = $mysqli->prepare($checkname);
                $stmt_name->execute();
                $result_name = $stmt_name->fetchAll(PDO::FETCH_ASSOC);
                $checkresult = count($result_name);
                if (($checkresult > 0) && ($is_ajax == 1)) {
                    echo '1';
                } else {
                    if ($hasid != 0) {
                        $checkforid = "select * from app_data where id='$hasid' and author_id='$userId' and deleted !=1";
                        $idExist = $mysqli->prepare($checkforid);
                        $idExist->execute();
                        $totalid = $idExist->fetchAll(PDO::FETCH_ASSOC);
                        $check_id = count($totalid);
                        if ($check_id > 0) {
                           /* updated by Nitin on 1/9/15 to update app name */
						   $sql = "UPDATE app_data SET summary = :summary, 
								platform = :platform, 
								updated =  NOW()
								WHERE id = :appID and deleted!=1";
                            $stmt = $mysqli->prepare($sql);
                            $stmt->bindParam(':summary', $appName, PDO::PARAM_STR);
                            $stmt->bindParam(':platform', $platform, PDO::PARAM_STR);
                            $stmt->bindParam(':appID', $hasid, PDO::PARAM_INT);
                            $stmt->execute();
                            echo $hasid;
                            $createNew = 0;
                        } else {
                            $createNew = 1;
                        }
                    } else {
                        $createNew = 1;
                    }
                    if ($createNew == 1) {
                        $screenid = 1;
						$expiry = date('Y-m-d 00:00:00', strtotime('+30 days', time()));
                        $sql = "INSERT INTO app_data(app_id,plan_expiry_date,summary,author_id,launch_screen_id,splash_screen_id,category,platform,theme,created) VALUES (:app_id,:expiry,:summary,:author_id,:launch_screen_id,:splash_screen_id,:category,:platform,:theme, NOW())";
                        $stmt = $mysqli->prepare($sql);
                        $stmt->bindParam(':app_id', $token, PDO::PARAM_STR);
                        $stmt->bindParam(':expiry', $expiry, PDO::PARAM_STR);
                        $stmt->bindParam(':summary', $appName, PDO::PARAM_STR);
                        $stmt->bindParam(':author_id', $userId, PDO::PARAM_STR);
                        $stmt->bindParam(':launch_screen_id', $screenid, PDO::PARAM_STR);
                        $stmt->bindParam(':splash_screen_id', $screenid, PDO::PARAM_STR);
                        $stmt->bindParam(':category', $catid, PDO::PARAM_STR);
                        $stmt->bindParam(':platform', $platform, PDO::PARAM_STR);
                        $stmt->bindParam(':theme', $themeid, PDO::PARAM_STR);
                        $stmt->execute();
                        if(isset($_SESSION['appid'])){
                            unset($_SESSION['appid']);
                            
                        }                        
                        $appid = $mysqli->lastInsertId();
		
						
						$username = $_SESSION['username'];
						
						$sql122 ="select * FROM author WHERE id='".$userId."'";
						$stmt122 = $mysqli->prepare($sql122);
						$stmt122->execute();
						$results122 = $stmt122->fetch(PDO::FETCH_ASSOC);
						
						$user = $results122;
						
						if($user['email_address'] != '')
						{
							$sql1221 ="select at.name FROM app_data a LEFT JOIN app_type at ON at.id=a.id WHERE a.id='".$appid."' and a.deleted !=1";
							$stmt1221 = $mysqli->prepare($sql1221);
							$stmt1221->execute();
							$results1221 = $stmt1221->fetch(PDO::FETCH_ASSOC);
							
							$app_name          = $appName;
							
							$currentdate       = date('d/m/Y');
							$thirtydays        = date('d/m/Y', strtotime('+30 days', time()));
							$app_start_to_date = $currentdate.' to '.$thirtydays;
							
							$pdflink           = $basicUrl;
							if(!empty($results1221))
							{
								if($results1221['name'] == 'Content Publishing')
								{
									$pdflink = 'edm/Ready-Checklist-Content-Publishing.pdf';
								}
								elseif($results1221['name'] == 'Retail Commerce' || $results1221['name'] == 'Retail Catalogue')
								{
									$pdflink = 'edm/Ready-Checklist-Retail.pdf';
								}
								else
								{
									$pdflink = 'edm/Ready-Checklist-Content-Publishing.pdf';
								}
							}
							
							$csubject          = 'Congratulations!! Your 30-day Instappy trial period starts now!';
							$basicUrl          = $connection->siteurl();
							$chtmlcontent      = file_get_contents('../../edm/first_step_registration.php');
							$clastname         = $user['last_name'] != '' ? ' ' . $user['last_name'] : $user['last_name'];
							$cname             = ucwords($user['first_name'] . $clastname);
							$chtmlcontent      = str_replace('{customer_name}', ucwords($cname), $chtmlcontent);
							$chtmlcontent      = str_replace('{base_url}', $basicUrl, $chtmlcontent);
							$chtmlcontent      = str_replace('{app_name}', $app_name, $chtmlcontent);
							$chtmlcontent      = str_replace('{app_start_to_date}', $app_start_to_date, $chtmlcontent);
							$chtmlcontent      = str_replace('{pdflink}', $pdflink, $chtmlcontent);
							//$chtmlcontent      = str_replace('{verify_link}', $basicUrl . 'signup-varification.php?verification=' . $uid, $chtmlcontent);
							//$chtmlcontent      = str_replace('{verify_link}', $basicUrl, $chtmlcontent);

							$cto = $user['email_address'];
							$cformemail = 'noreply@instappy.com';
							$key = 'f894535ddf80bb745fc15e47e42a595e';
							//$url          = 'https://api.falconide.com/falconapi/web.send.rest?api_key='.$key.'&subject='.rawurlencode($csubject).'&fromname='.rawurlencode($csubject).'&from='.$cformemail.'&content='.rawurlencode($chtmlcontent).'&recipients='.$cto;
							//$customerhead = file_get_contents($url);

							$curl = curl_init();
							curl_setopt_array($curl, array(
								CURLOPT_RETURNTRANSFER => 1,
								CURLOPT_URL => 'https://api.falconide.com/falconapi/web.send.rest',
								CURLOPT_POST => 1,
								CURLOPT_POSTFIELDS => array(
									'api_key' => $key,
									'subject' => $csubject,
									'fromname' => 'Instappy',
									'from' => $cformemail,
									'content' => $chtmlcontent,
									'recipients' => $cto
								)
							));
							$customerhead = curl_exec($curl);

							curl_close($curl);
						}
                        $_SESSION['appid'] = $appid;
                        echo $appid;
                    }
                }
            } else {
                echo "User not login";
            }
        } else {
            echo "Request is not completed";
        }
    } else {
        echo "Request is not completed";
    }
}
?>