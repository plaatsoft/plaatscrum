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

$lang = array();

$time_start = microtime(true);

include "config.php";
include "database.php";
include "general.php";
include "menu.php";
include "english.php";
include "filter.php";
include "cron.php";

/*
** ---------------------------------------------------------------- 
** Global variables
** ---------------------------------------------------------------- 
*/

plaatscrum_debug('-----------------');

$page = "";
$title = "";
$user = "";
$access = "";

$mid = 0;
$sid = 0;
$eid = 0;
$uid = 0;
$pid = 0;
$id = 0;

/* 
** ---------------------------------------------------------------- 
** POST parameters
** ----------------------------------------------------------------
*/	

$session = plaatscrum_post("session", "");
$search = plaatscrum_post("search", "");
$token = plaatscrum_post("token", "");
$action = plaatscrum_get("action", "");

if (strlen($token)>0) {
	
	/* Decode token */
	$token = gzinflate(base64_decode($token));	
	$tokens = @preg_split("/&/", $token);
	
	if (DEBUG == 1) {
			echo '================<br>';
	}
	
	foreach ($tokens as $item) {
		$items = preg_split ("/=/", $item);				
		${$items[0]} = $items[1];	
		
		if (DEBUG == 1) {
			echo $items[0].'='.$items[1].'<br>';
		}
	}
	if (DEBUG == 1) {
			echo '================<br>';
	}
}

/*
** ---------------------------------------------------------------- 
** Database
** ---------------------------------------------------------------- 
*/

/* connect to database */
if (@plaatscrum_db_connect($config["dbhost"], $config["dbuser"], $config["dbpass"], $config["dbname"]) == false) {

	echo plaatscrum_ui_header();	
	echo plaatscrum_ui_banner("");

	$page  = '<h1>'.t('GENERAL_WARNING').'</h1>';
	$page .= '<br/>';
   $page .= t('DATABASE_CONNECTION_FAILED');
	$page .= '<br/>';
	
	echo '<div id="container">'.$page.'</div>';
	
	$time_end = microtime(true);
	$time = $time_end - $time_start;
	
	echo plaatscrum_ui_footer($time, 0 );

	exit;
}

/* create / patch database if needed */
plaatscrum_db_check_version();

/* Set default timezone */
date_default_timezone_set ( plaatscrum_db_config_get("timezone" ) );

/*
** ---------------------------------------------------------------- 
** Login check
** ---------------------------------------------------------------- 
*/

$user_id = plaatscrum_db_session_valid($session);

if ( $user_id == 0 ) {

	/* Redirect to login page */
	$mid = MENU_LOGIN;
				
} else {

	$user = plaatscrum_db_user($user_id);
	$data = plaatscrum_db_project_user($user->project_id, $user_id);
	if (isset($data->role_id)) {
		$access = plaatscrum_db_role($data->role_id);
	} else {
		$access = plaatscrum_db_role(ROLE_GUEST);
	}
	
	if ($user->language==1) {
		include "nederlands.php";
	}
}

/*
** ---------------------------------------------------------------- 
** State Machine
** ----------------------------------------------------------------
*/

/* Validated email address */
$tmp = preg_split('/-/', $action);

switch ($tmp[0]) {

	case EVENT_EMAIL_CONFIRM:

			$data = plaatscrum_db_user($tmp[1]);
		
			if (isset($data->valid) && ($data->valid==0) && (md5($data->email)==$tmp[2])) {
			
				$data->valid=1;
				plaatscrum_db_user_update($data);
				plaatscrum_ui_box('info', t('USER_EMAIL_VALID1'));
			}
			break;
}
				
/* Global Event Handler */
switch ($eid) {

	case EVENT_SEARCH: 			
			if ((strlen($search)>0) && ($search!=t('HELP')) && (isset($user->project_id))) {
			
				if (strstr($search,"#")) { 
					$search = str_replace('#', '', $search); 
					$id = plaatscrum_db_story_number_check($search, $user->project_id);
					if ($id>0) {
						$mid = MENU_BACKLOG;	
						$eid = EVENT_STORY_LOAD;
						$sid = PAGE_STORY;
					}
					
				} else {
						
					/* Clear sprint filter */
					$user->sprint_id = 0;
					plaatscrum_db_user_update($user);
	
					$mid = MENU_BACKLOG;
					$sid = PAGE_BACKLOG_FORM;
				}
			}
			break;
}

/* Global Page Handler */
switch ($sid) {

	// ---------------------------------------- //

	case PAGE_LOGIN: 	
	case PAGE_REGISTER: 	
	case PAGE_RECOVER: 	
				include "login.php";
				include "home.php";
				plaatscrum_login();
				break;
				
	// ---------------------------------------- //

	case PAGE_HOME:
				include "home.php";				
				plaatscrum_home();
				break;
				
	// ---------------------------------------- //

	case PAGE_BACKLOG_FORM:
				include "backlog.php";
				plaatscrum_backlog();
				break;

	case PAGE_BACKLOG_EXPORT:
				include "export.php";
				plaatscrum_export();
				break;
			
	case PAGE_BACKLOG_IMPORT:
				include "import.php";
				plaatscrum_import();
				break;
				
	case PAGE_STORY:
				include "story.php";
				plaatscrum_story();
				break;
				
	// ---------------------------------------- //
	
	case PAGE_TASKBOARD:
				include "task_board.php";
				plaatscrum_taskboard();
				break;
				
	case PAGE_STATUSBOARD:
				include "story_board.php";
				plaatscrum_storyboard();
				break;
				
	case PAGE_RESOURCEBOARD:
				include "resource_board.php";
				plaatscrum_resourceboard();
				break;
								
	case PAGE_COST:
				include "cost_board.php";
				plaatscrum_costboard();
				break;
				
	// ---------------------------------------- //
				
	case PAGE_BURNDOWN_CHART:
				include "PHPGraphLib.php";
				include "burndown_chart.php";
				plaatscrum_burndown_chart();
				break;
			
	case PAGE_STATUS_CHART:
				include "PHPGraphLib.php";
				include "status_chart.php";
				plaatscrum_status_chart();
				break;
				
	case PAGE_VELOCITY_CHART:
				include "PHPGraphLib.php";
				include "velocity_chart.php";
				plaatscrum_velocity_chart();
				break;
				
	case PAGE_CALENDER_CHART:
				include "calender_chart.php";
				plaatscrum_calender();
				break;
				
	// ---------------------------------------- //
	
	case PAGE_GENERAL:
	case PAGE_USERLIST:
	case PAGE_PROJECTLIST_FORM:
				include "settings.php";				
				include "user.php";
				include "project.php";
				include "release.php";	
				include "sprint.php";				
				plaatscrum_settings();
				break;
				
	// ---------------------------------------- //
	
	case PAGE_INSTRUCTIONS:
				include "help.php";
				plaatscrum_help();
				break;
				
	case PAGE_CREDITS:
				include "credits.php";
				plaatscrum_credits();
				break;
				
	case PAGE_DONATE:
				include "donate.php";
				plaatscrum_donate();
				break;
				
	case PAGE_ABOUT:
				include "about.php";
				plaatscrum_about();
				break;

	case PAGE_RELEASE_NOTES:
				include "release_notes.php";
				plaatscrum_release_notes();
				break;

	// ---------------------------------------- //
}

/* update member statistics */
if (isset($user->user_id)) {

	/* member_id = user_id */
	$member = plaatscrum_db_member($user->user_id);
	
	$member->requests++;
	$member->last_activity = date("Y-m-d H:i:s", time());
	
	plaatscrum_db_member_update($member);
}
		
/*
** ---------------------------------------------------------------- 
** Process cron jobs
** ----------------------------------------------------------------
*/

plaatscrum_cron();

/*
** ---------------------------------------------------------------- 
** Create html response
** ----------------------------------------------------------------
*/

if ($eid!=EVENT_EXPORT) {

	echo plaatscrum_ui_header($title);
	
	echo plaatscrum_ui_banner(plaatscrum_menu());
	
	echo '<div id="container">'.$page.'</div>';

	$time_end = microtime(true);
	$time = $time_end - $time_start;

	$time = round($time*1000);

	echo plaatscrum_ui_footer($time, plaatscrum_db_count() );
	
	plaatscrum_debug('Page render time = '.$time.' ms.');
	plaatscrum_debug('Amount of queries = '.plaatscrum_db_count());
}

plaatscrum_db_close();

/*
** ---------------------------------------------------------------- 
** THE END
** ----------------------------------------------------------------
*/

?>
