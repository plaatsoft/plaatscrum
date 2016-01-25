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

include "config.inc";
include "database.inc";
include "general.inc";
include "menu.inc";
include "english.inc";
include "filter.inc";
include "cron.inc";

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
	
	foreach ($tokens as $item) {
		$items = preg_split ("/=/", $item);				
		$$items[0] = $items[1];	
	}
}

/*
** ---------------------------------------------------------------- 
** Database
** ---------------------------------------------------------------- 
*/

/* connect to database */
plaatscrum_db_connect($config["dbhost"], $config["dbuser"], $config["dbpass"], $config["dbname"]);

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
		include "nederlands.inc";
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
switch ($mid) {
	
	case MENU_LOGIN: 	
				include "login.inc";
				include "story.inc";
				include "home.inc";
				plaatscrum_login();
				break;
	
	case MENU_HOME:
				include "story.inc";
				include "home.inc";				
				plaatscrum_home();
				break;
	
	case MENU_BACKLOG:
				include "story.inc";				
				include "export.inc";
				include "import.inc";
				include "backlog.inc";
				plaatscrum_backlog();
				break;
				
	case MENU_BOARD:
				include "story.inc";	
				include "board.inc";
				plaatscrum_board();
				break;
			  
   case MENU_CHART:
				include "story.inc";	
				include "graph.php";
				include "calender.inc";
				include "chart.inc";
				plaatscrum_chart();
				break;
				
	case MENU_SETTINGS:
				include "settings.inc";				
				include "user.inc";
				include "project.inc";
				include "release.inc";	
				include "sprint.inc";				
				plaatscrum_settings();
				break;
				
	case MENU_HELP:
				include "releasenotes.inc";
				include "help.inc";
				plaatscrum_help();
				break;
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