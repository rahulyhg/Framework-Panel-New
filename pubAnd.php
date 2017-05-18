<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/custom-style.css" rel="stylesheet" type="text/css" />
<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="css/colpick.css">
<link rel="stylesheet" href="css/jquery.mCustomScrollbar.css">
<link rel="stylesheet" href="css/style_old.css">
<link rel="stylesheet" href="css/intlTelInput.css">
<link rel="stylesheet" type="text/css" href="css/ImageSelect.css">
<link rel="stylesheet" type="text/css" href="css/chosen.css">
<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
<script src="js/jquery.min.js"></script>

<title>Untitled Document</title>
</head>
<body>
<?php
require_once('includes/header.php');
require_once('includes/leftbar.php');
?>
<section class="clear framework">

  <section class="main">
    <section class="right_main">
    	<div class="right_inner">
            <div class="how_publish">
            	<div class="how_publish_left">
                    <h1>Publish:</h1>
                    <h2>Please fill in important details.</h2>
                </div>
                <div class="clear"></div>
                <div class="how_publish_body">
                	<div class="how_publish_body_left">
                    	<div class="publish_content">
                            <h2>Content</h2>
                            <div class="publish_content_form">
                                <div class="publish_content_label">
                                    <label>App Namre :</label>
                                </div>
                                <div class="publish_content_textbox">
                                    <input type="text" placeholder="Dummy Content">
                                </div>
                                <div class="publish_content_label">
                                    <label>Title :</label>
                                </div>
                                <div class="publish_content_textbox">
                                    <input type="text" placeholder="Name of app appear in app store">
                                </div>
                                <div class="publish_content_label">
                                    <label>short Description :</label>
                                </div>
                                <div class="publish_content_textbox">
                                    <textarea placeholder="Limit 0-80 characters"></textarea>
                                </div>
                                <div class="publish_content_label">
                                    <label>Description :</label>
                                </div>
                                <div class="publish_content_textbox">
                                    <textarea class="disc" placeholder="Limit 10-4000 characters"></textarea>
                                </div>
                                <div class="publish_content_label">
                                    <label>Category :</label>
                                </div>
                                <div class="publish_content_textbox">
                                    <select>
                                    	<option>Dummy Content</option>
                                    	<option>Dummy Content</option>
                                    	<option>Dummy Content</option>
                                    </select>
                                </div>
                                <div class="publish_content_label">
                                    <label>Rating :</label>
                                </div>
                                <div class="publish_content_textbox">
                                    <select>
                                    	<option>Dummy Content</option>
                                    	<option>Dummy Content</option>
                                    	<option>Dummy Content</option>
                                    </select>
                                </div>
                                <div class="publish_content_label">
                                    <label>App Price :</label>
                                </div>
                                <div class="publish_content_textbox">
                                    <select>
                                    	<option>Dummy Content</option>
                                    	<option>Dummy Content</option>
                                    	<option>Dummy Content</option>
                                    </select>
                                </div>
                            </div>
                            <div class="developer_account">
	                            <h3>Google Play Android Developer Account</h3>
                                <div class="developer_account_info">
                                	<p>You need to invite shoutem@shoutem.com to use your android dev account as an administrator.</p>
                                    <ul>
                                    	<li>Log in to your android developer account</li>
                                        <li>Click 'Manage user accounts...' link</li>
                                        <li>Click 'Invite a new user button'</li>
                                        <li>Enter 'shoutem@shoutem.com' and send invitation</li>
                                    </ul>
                                </div>
                                <div class="developer_account_details">
                                	<div class="developer_details_label">
                                        <label>Android dev account owner email:*</label>
                                    </div>
                                    <div class="developer_details_textbox">
                                        <input type="text">
                                        <a href="#">?</a>
                                    </div>
                                	<div class="developer_details_label">
                                        <label>Android developer console account name:*</label>
                                    </div>
                                    <div class="developer_details_textbox">
                                        <input type="text">
                                        <a href="#">?</a>
                                    </div>
                                </div>
                            </div>
                        <a href="#" class="make_app_next">Save &amp; Continue</a>
                        <div class="clear"></div>
                        </div>
                    </div>
                	<div class="how_publish_body_right">
                    	<div class="common_publish_right_box">
                        	<h2>Let us help you !</h2>
                            <p>Need any help at any point, Let us guide you till the end.</p>
                            <a href="#">Give a Call</a>
                            <div class="clear"></div>
                        </div>
                    	<div class="common_publish_right_box">
                        	<h2>Need More Help ?</h2>
                            <p>For better view on the topic visit Devloper Console.</p>
                            <a href="#">Go to Developer Console</a>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </section>
</section>
<script>window.jQuery || document.write('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"><\/script>')</script> 
<script src="js/jquery.mCustomScrollbar.concat.min.js"></script> 
<script>
	(function($){
		$(window).load(function(){
			$("#content-1").mCustomScrollbar();
			$("#content-2").mCustomScrollbar();
			
			
		});
	})(jQuery);
</script>
	<script src="js/chosen.jquery.js"></script>
    <script src="js/ImageSelect.jquery.js"></script>
    <script>
    $(".my-select").chosen();
    </script>
</body>
</html>
