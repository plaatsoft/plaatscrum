<?php

/* 
**  ==========
**  PlaatScrum
**  ==========
**
**  Created by wplaat
**
**  For more information visit the following website.
**  Website : www.plaatsoft.nl 
**
**  Or send an email to the following address.
**  Email   : info@plaatsoft.nl
**
**  All copyrights reserved (c) 2008-2016 PlaatSoft
*/

/*
** ------------------
** POST PARAMETERS
** ------------------
*/

$name = plaatscrum_post("name", "");
$email = plaatscrum_post("email", "");
$username = plaatscrum_post("username", "");
$password  = plaatscrum_post("password", "");

/*
** ------------------
** ACTIONS
** ------------------
*/

/**
 * Create random password
 */
function plaatscrum_randomPassword($length) {
	$possible = '0123456789' .
					'abcdefghjiklmnopqrstuvwxyz'.
					'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$str = "";
	while (strlen($str) < $length) {
			$str .= substr($possible, (rand() % strlen($possible)), 1);
	}	

	return($str);
}

/**
 * Send recover email
 */
function plaatscrum_recover_mail($to, $username, $password) {

	/* input */
	global $config;
	
	$subject = 'Password reset of PlaatScrum';
	
	$body  = 'Your password is reset!'."\r\n\r\n";
	$body .= 'Your username = '.$username."\r\n";
	$body .= 'Your new password = '.$password."\r\n\r\n";
	$body .= 'Visit '.$config["base_url"].' and login to continue working with PlaatScrum'."\r\n";
		
	$header = 'From: PlaatScrum <'.$config['from_email'].">\r\n";

	@mail($to, $subject, $body, $header);
	plaatscrum_info("Send email [".$to."] Password reset");
}
		

function plaatscrum_recover_do() {

	/* input */
	global $email;

	global $mid;
	global $sid;	
	
	if (validate_email($email)) {
		
		plaatscrum_ui_box('warning', t('LOGIN_EMAIL_INVALID'));
	
	} else {
	
		$user_id = plaatscrum_db_user_email($email);
	
		if ($user_id==0) {
		
			plaatscrum_ui_box('warning', t('LOGIN_EMAIL_NOT_FOUND'));
					
		} else {
		
			/* Create random password */
			$password = plaatscrum_randomPassword(10);
			
			$member = plaatscrum_db_member($user_id);
			plaatscrum_db_member_update2($member->username, $password, $user_id);	
						
			plaatscrum_recover_mail($email, $member->username, $password);
						
			plaatscrum_ui_box('info', t('LOGIN_RECOVER_OK'));
		}
	}
}

/**
 * Send registration welcome email
 */
function plaatscrum_register_mail($to, $username, $id) {

	/* input */
	global $config;
	
	$subject = 'Welcome to PlaatScrum';
	
	$body  = 'Welcome to PlaatScrum'."\r\n\r\n";
	$body .= 'Thanks you for registrating!'."\r\n\r\n";
	$body .= 'Your username = '.$username."\r\n\r\n";

	$body .= 'Please confirm your email address by clicking the following link'."\r\n";
	
	$body .= $config["base_url"].'?action='.EVENT_EMAIL_CONFIRM.'&key='.$id.'-'.md5($to);
	
	$header = 'From: PlaatScrum <'.$config['from_email'].">\r\n";

	@mail($to, $subject, $body, $header);
	plaatscrum_info("Send email [".$to."] Welcome message");
}

function plaatscrum_register_do() {

	/* input */
   global $username;
	global $password;
	global $name;
	global $email;
		
	if (strlen($username)<5) {
		
		plaatscrum_ui_box('warning', t('LOGIN_USERNAME_TO_SHORT'));
		
	} else if (plaatscrum_db_member_username($username)>0)  {
	
		plaatscrum_ui_box('warning', t('LOGIN_USERNAME_EXIST'));
		
	} else if (strlen($password)<5) {

		plaatscrum_ui_box('warning', t('LOGIN_PASSWORD_TO_SHORT'));
	
	} else if (strlen($name)<3) {

		plaatscrum_ui_box('warning', t('LOGIN_NAME_TO_SHORT'));
	
	} else if (validate_email($email)) {
		
		plaatscrum_ui_box('warning', t('LOGIN_EMAIL_INVALID'));
	
	} else {
	
		plaatscrum_ui_box('info', t('LOGIN_REGISTER_SUCCESFULL'));
	
		$member_id = plaatscrum_db_member_insert($username, $password);
		plaatscrum_db_user_insert($member_id, $name, $email, ROLE_GUEST);	
		
		plaatscrum_register_mail($email, $name, $member_id);
		
		plaatscrum_login_do();
	}
}

function plaatscrum_login_do() {

	/* input */
   global $username;
	global $password;
	
	/* output */
	global $mid;
	global $sid;	
	global $user;
	global $access;
	global $access;
	global $session;
	global $page;
					
	$user_id = plaatscrum_db_member_id($username, $password);	
	
	if ($user_id == 0) {
	
		plaatscrum_ui_box('warning', t('LOGIN_FAILED'));
		plaatscrum_info("Login [".$username."] failed!");
	
	} else { 
		
		$session = plaatscrum_db_session_add($user_id);
		
		/* user_id = member_id */
		$member = plaatscrum_db_member($user_id);
		$member->last_login = date("Y-m-d H:i:s", time());
		$member->last_activity = date("Y-m-d H:i:s", time());
		plaatscrum_db_member_update($member);
		
		$user = plaatscrum_db_user($user_id);
		$data = plaatscrum_db_project_user($user->project_id, $user_id);
		if (isset($data->role_id)) {
			$access = plaatscrum_db_role($data->role_id);
		} else {
			$access = plaatscrum_db_role(ROLE_GUEST);
		}
		
		/* Redirect to home page. */
		$mid = MENU_HOME;			
		$sid = PAGE_HOME;	
		$page = "";
		
		plaatscrum_info('Login '.$user->name.' ['.$user->user_id.']');
	} 
}

function plaatscrum_logout_do() {

	/* output */
	global $session;
	global $user;
	global $access;
	
	plaatscrum_info('Logout '.$user->name.' ['.$user->user_id.']');
	
	plaatscrum_ui_box('info', t('LOGIN_LOGOUT'));
	
	plaatscrum_db_session_delete($session);
	
	/* Destroy user and access information */
	$user="";
	$access="";
}


/*
** ------------------
** UI
** ------------------
*/

function plaatscrum_login_footer() {

	$page  = '<br class="clear" />';
		
	$page .= '<div id="footer">';
   
	$page .= '<h1>'.t('LOGIN_SCREENSHOTS').'</h1>';
	
	$page .= '<div class="footbox">';
	$page .= plaatscrum_ui_image("plaatscrum-home.png");
	$page .= '<p>';
	$page .= '<b>'.t('LOGIN_HOME_TEXT1').'</b>';
	$page .= '</p>';
   $page .= '</div>';
	 
	$page .= '<div class="footbox">';
	$page .= plaatscrum_ui_image("plaatscrum-story.png");
	$page .= '<p>';
	$page .= '<b>'.t('LOGIN_HOME_TEXT2').'</b>';
	$page .= '</p>';
   $page .= '</div>';
	 
	$page .= '<div class="footbox">';
	$page .= plaatscrum_ui_image("plaatscrum-status.png");
	$page .= '<p>';
	$page .= '<b>'.t('LOGIN_HOME_TEXT3').'</b>';
	$page .= '</p>';
   $page .= '</div>';
	 
	$page .= '<div class="footbox">';
	$page .= plaatscrum_ui_image("plaatscrum-burndown.png");
	$page .= '<p>';
	$page .= '<b>'.t('LOGIN_HOME_TEXT4').'</b>';
	$page .= '</p>';
   $page .= '</div>';
	 
   $page .= '<div class="footbox last">';
	$page .= plaatscrum_ui_image("plaatscrum-calendar.png");
	$page .= '<p>';
	$page .= '<b>'.t('LOGIN_HOME_TEXT5').'</b>';
	$page .= '</p>';
   $page .= '</div>';
	 
   $page .= '<br class="clear"/>';
	$page .= '</div>';
	
	return $page;
}

function plaatscrum_recover_form() {

	/* input */
	global $mid;
	global $sid;
	
	global $name;
	global $username;
	global $email;
	
	/* output */
	global $page;
	
   $page .= '<div id="content">';
	
	$page .= '<h1>'.t('LOGIN_WELCOME_TITLE').'</h1>';
	$page .= '<img class="imgl" src="images/plaatscrum-taskboard.png" alt="" />';
	$page .= t('LOGIN_WELCOME');
	$page .= '</div>'; 
		
	$page .= '<div id="column">';
   $page .= '<div class="subnav">';
	$page .= '<h2>'.t('LOGIN_RECOVER').'</h2>';
		
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_EMAIL').':</label>';
	$page .= plaatscrum_ui_input("email", 50, 100, $email);
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= plaatscrum_button('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_RECOVER, t('LINK_RECOVER'));
	$page .= '</p>';
			
	$page .= '</div>';
	$page .= '</div>';
	
	$page .= plaatscrum_login_footer();
		  		
	return $page;	
}

function plaatscrum_register_form() {

	/* input */
	global $mid;
	global $sid;
	
	global $name;
	global $username;
	global $password;
	global $email;
	
	/* output */
	global $page;
	
   $page .= '<div id="content">';
	
	$page .= '<h1>'.t('LOGIN_WELCOME_TITLE').'</h1>';
	$page .= '<img class="imgl" src="images/plaatscrum-taskboard.png" alt="" />';
	$page .= t('LOGIN_WELCOME');
	
	$page .= '</div>'; 
		
	$page .= '<div id="column">';
   $page .= '<div class="subnav">';
	$page .= '<h2>'.t('LOGIN_REGISTER').'</h2>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_NAME').':</label>';
	$page .= plaatscrum_ui_input("name", 50, 50, $name);
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_USERNAME').':</label>';
	$page .= plaatscrum_ui_input("username", 20, 15, $username);
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_PASSWORD').':</label>';
	$page .= '<input type="password" name="password" id="password" size="20" maxlength="15" value="'.$password.'"/>';
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_EMAIL').':</label>';
	$page .= plaatscrum_ui_input("email", 50, 100, $email);
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= plaatscrum_button('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_REGISTER, t('LINK_REGISTER'));
	$page .= '</p>';
			
	$page .= '</div>';
	$page .= '</div>';
	
	$page .= plaatscrum_login_footer();
		  		
	return $page;	
}

function plaatscrum_login_form() {

	/* input */
	global $mid;
	
	/* output */
	global $page;
		
   $page .= '<div id="content">';
	
	$page .= '<h1>'.t('LOGIN_WELCOME_TITLE').'</h1>';
	$page .= '<img class="imgl" src="images/plaatscrum-taskboard.png" alt="" />';
	$page .= t('LOGIN_WELCOME');
	
	$page .= '</div>'; 
		
	$page .= '<div id="column">';
   $page .= '<div class="subnav">';
   $page .= '<h2>'.t('LOGIN_TITLE').'</h2>';
		  	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_USERNAME').':</label>';
	$page .= '<input type="text" name="username" id="username" value=""/>';
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_PASSWORD').':</label>';
	$page .= '<input type="password" name="password" id="password" value=""/>';
	$page .= '</p>';
	
	$page .= plaatscrum_button('mid='.$mid.'&eid='.EVENT_LOGIN, t('LINK_LOGIN'));
			
	$page .= '</div>';
						
	$page .= '</div>';	
	
	$page .= plaatscrum_login_footer();
		
	return $page;	
}
    
/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_login() {

	/* input */
	global $eid;
	global $sid;
		
	/* Event handler */
	switch ($eid) {
	
		case EVENT_LOGIN: 	
					plaatscrum_login_do();	
					break;
				  
		case EVENT_REGISTER: 	
					plaatscrum_register_do();	
					break;
					
		case EVENT_RECOVER: 	
					plaatscrum_recover_do();	
					break;
					
		case EVENT_LOGOUT: 	
					plaatscrum_logout_do();
					break;
	}
		
	/* Page handler */
	switch ($sid) {
			
		default:
		case PAGE_LOGIN: 
					plaatscrum_login_form();	
				   break;
			
		case PAGE_REGISTER:
					plaatscrum_register_form();	
				   break;
					
		case PAGE_RECOVER:
					plaatscrum_recover_form();	
				   break;
					
		case PAGE_HOME:
					plaatscrum_home();
					break;
	}
}

/*
** ------------------
** The End
** ------------------
*/
