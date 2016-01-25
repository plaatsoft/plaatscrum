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
** ---------------------------------------------------------------- 
** DEFINES
** ----------------------------------------------------------------
*/

define("ROLE_GUEST", 1);
define("ROLE_SCRUM_MASTER", 2);
define("ROLE_PRODUCT_OWNER", 3);
define("ROLE_TEAM_MEMBER", 4);

define("ROLE_ADMINISTRATOR", 5);
define("ROLE_USER", 6);

define("PRIO_MINOR", 1);
define("PRIO_MAJOR", 2);
define("PRIO_CRITICAL", 3);

define("TYPE_STORY", 1);
define("TYPE_BUG", 2);
define("TYPE_TASK", 3);
define("TYPE_EPIC", 4);

define('STATUS_ALL', 0);
define('STATUS_NEW', 0);
define('STATUS_NONE', 0);
define('STATUS_TODO', 1);
define('STATUS_DOING', 2);
define('STATUS_DONE', 3);
define('STATUS_SKIPPED', 4);
define('STATUS_ONHOLD', 5);
define('STATUS_REVIEW', 6);

define('LANGUAGE_ENGLISH',0);
define('LANGUAGE_DUTCH',1);

define("MENU_LOGIN", 100);
define("MENU_HOME", 101);
define("MENU_BACKLOG", 102);
define("MENU_BOARD", 103);
define("MENU_CHART", 104);
define("MENU_CALENDER", 105);
define("MENU_SETTINGS", 106);
define("MENU_HELP", 107);
define("MENU_LOGOUT", 100);

define("PAGE_LOGIN", 200);
define("PAGE_REGISTER", 201);
define("PAGE_RECOVER", 202);
define("PAGE_HOME", 203);
define("PAGE_BACKLOG_FORM", 204);
define("PAGE_STORY", 205);
define("PAGE_TASKBOARD", 206);
define("PAGE_STATUSBOARD", 207);
define('PAGE_RESOURCEBOARD', 208);
define('PAGE_COST', 209);
define("PAGE_GENERAL", 210);
define("PAGE_USERLIST", 211);
define("PAGE_USER", 212);
define("PAGE_PROJECTLIST_FORM", 213);
define("PAGE_PROJECT_FORM", 214);
define("PAGE_INSTRUCTIONS", 215);
define("PAGE_RELEASE_NOTES", 216);
define("PAGE_CREDITS", 217);
define("PAGE_DONATE", 218);
define("PAGE_ABOUT", 219);
define("PAGE_SPRINTLIST_FORM", 220);
define('PAGE_SPRINT_FORM', 221);
define('PAGE_BURNDOWN_CHART', 222);
define('PAGE_STATUS_CHART', 223);
define("PAGE_CALENDER", 224);
define("PAGE_RELEASELIST_FORM", 225);
define('PAGE_RELEASE_FORM', 226);
define('PAGE_PROJECT_USER_ASSIGN', 227);
define('PAGE_VELOCITY_CHART', 228);
define('PAGE_BACKLOG_EXPORT', 229);
define('PAGE_BACKLOG_IMPORT', 230);

define("EVENT_LOGIN", 301);
define("EVENT_REGISTER", 302);
define("EVENT_RECOVER", 303);
define("EVENT_LOGOUT", 304);
define("EVENT_FILTER", 305);
define("EVENT_USER_SAVE", 306);
define("EVENT_USER_DELETE", 307);
define("EVENT_USER_ASSIGN", 308);
define("EVENT_USER_DROP", 309);
define("EVENT_USER_CANCEL", 310);
define("EVENT_USER_HACK", 311);
define("EVENT_PROJECT_DELETE", 312);
define("EVENT_PROJECT_SAVE", 313);
define("EVENT_PROJECT_SELECT", 314);
define("EVENT_RELEASE_ADD", 315);
define("EVENT_RELEASE_SAVE", 316);
define("EVENT_RELEASE_DELETE", 317);
define("EVENT_SPRINT_ADD", 318);
define("EVENT_SPRINT_SAVE", 319);
define("EVENT_SPRINT_DELETE", 320);
define("EVENT_STORY_SAVE", 321);
define("EVENT_STORY_DELETE", 322);
define("EVENT_STORY_ASSIGN", 323);
define("EVENT_STORY_DROP", 324);
define("EVENT_STORY_NEW", 325);
define("EVENT_STORY_LOAD", 326);
define("EVENT_STORY_CANCEL", 327);
define("EVENT_IMPORT", 328);
define("EVENT_EXPORT", 329);
define("EVENT_SEARCH", 330);
define("EVENT_SETTING_SAVE", 331);
define("EVENT_EMAIL_CONFIRM", 332);

/*
** ---------------------------------------------------------------- 
** TRANSLATE
** ---------------------------------------------------------------- 
*/

/**
 * Translate text label (multi language support)
 */
function t() {

	global $lang;
	
   $numArgs = func_num_args();

   $temp = $lang[func_get_arg(0)];

   $pos = 0;
   $i = 1;

   while (($pos = strpos($temp, "%s", $pos)) !== false) {
      if ($i >= $numArgs) {
         throw new InvalidArgumentException("Not enough arguments passed.");
		}

      $temp = substr($temp, 0, $pos) . func_get_arg($i) . substr($temp, $pos + 2);
      $pos += strlen(func_get_arg($i));
      $i++;
   }      
	
	$temp = mb_convert_encoding($temp, "UTF-8", "HTML-ENTITIES" ); 
   return $temp; 
}

/*
** ---------------------------------------------------------------- 
** TRACING
** ----------------------------------------------------------------
*/

function udate($format, $utimestamp = null) {
	if (is_null($utimestamp)) {
		$utimestamp = microtime(true);
	}

	$timestamp = floor($utimestamp);
	$milliseconds = round(($utimestamp - $timestamp) * 1000000);

	return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
}

function plaatscrum_user_log($player, $text) {
	
	$message  = udate('d-m-Y H:i:s:u');		
	$message .= ' [';
	$message .= number_format2($player->money).'|';
	$message .= number_format2($player->bank1).'|';
	$message .= number_format2($player->bank2).'|';
	$message .= number_format2($player->bank3);	
	$message .= '] ';
	$message .= $text;
	$message .= "\r\n";
	
	$myFile = 'user/'.$player->pid.'.log';
	$fp = fopen($myFile, 'a');	
	fwrite($fp, $message);
	fclose($fp);		
}

function plaatscrum_write_file($type, $text) {

	/* input */
	global $player;
	global $other;
	
	$message = udate('d-m-Y H:i:s:u').' ['.$_SERVER["REMOTE_ADDR"];
	
	if (isset($player)) {
		$message .= '|'.$player->pid;
	}
	
	if (isset($other)) {
		$message .= '|'.$other->pid;
	}
	
	$message .= '] '.$type.' '.$text."\r\n";
	$message = str_replace('<br/>', " ", $message); 
	
	$myFile = 'log/scrumboard-'.date('Ymd').'.log';
	$fp = fopen($myFile, 'a');	
	fwrite($fp, $message);
	fclose($fp);		
}

function plaatscrum_info($text) {

	plaatscrum_write_file('INFO', $text);
}

function plaatscrum_error($text) {
	
	plaatscrum_write_file('ERROR', $text);	
}

function plaatscrum_debug($text) {
	
	if (DEBUG == 1 ) {
		
		plaatscrum_write_file('DEBUG', $text); 
	}
}

/*
** ---------------------------------------------------------------- 
** LINKS
** ----------------------------------------------------------------
*/

function plaatscrum_button($parameters, $label, $id="") {

	$link  = '<button name="token" value="'.plaatscrum_token($parameters).'" class="button" ';
	if (strlen($id)!=0) {
		$link .= ' id="'.strtolower($id).'"';
	}
	$link .= '>'.$label.'</button>';	

	return $link;
}
		
/**
 * Create hidden link 
 */
function plaatscrum_link($parameters, $label, $id="") {
   		
	$link  = '<a href="javascript:link(\''.plaatscrum_token($parameters).'\');" class="link" ';			
	if (strlen($id)!=0) {
		$link .= ' id="'.strtolower($id).'"';
	}
	$link .= '>'.$label.'</a>';	

	return $link;
}

/**
 * Create hidden link 
 */
function plaatscrum_link_hidden($parameters, $label, $id="") {
   		
	$link  = '<a href="javascript:link(\''.plaatscrum_token($parameters).'\');" class="hide_link" ';		
	if (strlen($id)!=0) {
		$link .= ' id="'.strtolower($id).'"';
	}
	$link .= '>'.$label.'</a>';
	
	return $link;
}

/**
 * Create hidden link with popup
 */ 
function plaatscrum_link_confirm($parameters, $label, $question="") {
   			
	global $link_counter;	
	
	$link_counter++;
	
	$link  = '<a href="javascript:show_confirm(\''.$question.'\',\''.plaatscrum_token($parameters).'\');" class="link" ';
	$link .= 'id="link-'.$link_counter.'">'.$label.'</a>';	
		
	return $link;
}

/** 
 * Zip and uuencode token.
 */
function plaatscrum_token($token) {
   
	/* Encode token  */
	$token = base64_encode(gzdeflate($token));
	
	return $token;
}

function plaatscrum_post($label, $default) {
	
	$value = $default;
	
	if (isset($_POST[$label])) {
		$value = $_POST[$label];
		$value = stripslashes($value);
		$value = htmlspecialchars($value);
	}
	
	return $value;
}

function plaatscrum_get($label, $default) {
	
	$value = $default;
	
	if (isset($_GET[$label])) {
		$value = $_GET[$label];
		$value = stripslashes($value);
		$value = htmlspecialchars($value);
	}
	
	return $value;
}


function plaatscrum_multi_post($label, $default) {
	
	$value = $default;
	
	if (isset($_POST[$label])) {
	
		$value = "";
	
		for($i=0; $i<sizeof($_POST[$label]); $i++) {
		
			if (strlen($value)>0) {
				$value .= ",";
			}
			$value .= $_POST[$label][$i];
		}
	}
	
	return $value;
}

function plaatscrum_link_store($mid, $sid) {

	/* input */
	global $user;
	
	if (($user->menu_id!=$mid) || ($user->page_id!=$sid)) {
	
		$user->menu_id=$mid;
		$user->page_id=$sid;
	
		plaatscrum_db_user_update($user);
		
	}
}

/*
** ---------------------
** UI
** ---------------------
*/

function plaatscrum_ui_input($name, $size, $maxlength, $value, $readonly=false) {
	
	$page  = '<input ';
	$page .= 'type="text" ';
	$page .= 'id="'.$name.'" ';
	$page .= 'name="'.$name.'" ';
	$page .= 'value="'.$value.'" ';
	$page .= 'size='.$size.' ';
	$page .= 'maxlength='.$maxlength.' ';
	
	if ($readonly==true) {
		$page .= 'disabled="true" ';
	}
	
	$page .= '/>';

	return $page;
}

function plaatscrum_ui_input_hidden($name, $value) {
	
	$page  = '<input ';
	$page .= 'type="hidden" ';
	$page .= 'id="'.$name.'" ';
	$page .= 'name="'.$name.'" ';
	$page .= 'value="'.$value.'" ';
		
	$page .= '/>';

	return $page;
}

function plaatscrum_ui_datepicker($name, $size, $maxlength, $value, $readonly=false) {

	$page  = '<script language="JavaScript" type="text/javascript">';
	$page .= '$(function() {';
	$page .= '	$( "#'.$name.'" ).datepicker({ dateFormat: "dd-mm-yy", showWeek: true, firstDay: 1});';
	$page .= '});';
	$page .= '</script>';
		
	$page .= '<input ';
	$page .= 'type="text" ';
	$page .= 'id="'.$name.'" ';
	$page .= 'name="'.$name.'" ';
	$page .= 'value="'.$value.'" ';
	$page .= 'size="'.$size.'" ';
	$page .= 'maxlength="'.$maxlength.'" ';
	
	if ($readonly==true) {
		$page .= 'disabled="true" ';
	}
	
	$page .= '/>';
	
	return $page;	
}

function plaatscrum_ui_textarea($name, $rows, $cols, $value, $readonly) {
	
	$page ='<textarea name="'.$name.'" rows="'.$rows.'" cols="'.$cols.'" ';
	if ($readonly) {
		$page .= 'disabled="true" ';
	}
	$page .= '>'; 
	
	$page.= $value;		
	$page.='</textarea>';
	  
   return $page;
}

function plaatscrum_ui_project_user($tag, $id, $readonly=false, $empty=false) {

	/* input */
	global $user;
	
	$query  = 'select a.user_id, a.name from tuser a left join member b on b.user_id=a.user_id ';
	$query .= 'left join project_user c on a.user_id=c.user_id where b.deleted=0 and ';
	$query .= 'c.project_id='.$user->project_id.' order by a.name ';
	$result = plaatscrum_db_query($query);
			
	$page ='<select id="'.$tag.'" name="'.$tag.'" ';

	if ($readonly) {
		$page .= 'disabled="true" ';
	}
	$page .= '>'; 
	
	if ($empty) {
		$page.='<option value="0"></option>';
	}
		
	while ($data=plaatscrum_db_fetch_object($result)) {	
	
		$page.='<option value="'.$data->user_id.'"';
		
		if ($id == $data->user_id) {
			$page .= ' selected="selected"';
		}
		$page .= '>'.$data->name.'</option>';
	}
		
	$page .= '</select>';
	
	if ($readonly) {	
		$page .= '<input type="hidden" name="'.$tag.'" value="'.$id.'" />';
	}
		  
   return $page;
}

function plaatscrum_ui_all_user($tag, $id, $readonly=false, $empty=false) {

	$query  = 'select a.user_id, a.name from tuser a left join member b on b.user_id=a.user_id ';
	$query .= 'where b.deleted=0 order by a.name';
	$result = plaatscrum_db_query($query);
	
	$page ='<select id="'.$tag.'" name="'.$tag.'" ';

	if ($readonly) {
		$page .= 'disabled="true" ';
	}
	$page .= '>'; 
	
	if ($empty) {
		$page.='<option value="0"></option>';
	}

	while ($data=plaatscrum_db_fetch_object($result)) {	
	
		$page.='<option value="'.$data->user_id.'"';
		
		if ($id == $data->user_id) {
			$page .= ' selected="selected"';
		}
		$page .= '>'.$data->name.'</option>';
	}
		
	$page .= '</select>';
	
   return $page;
}

function plaatscrum_ui_project($tag, $project_id, $readonly=false) {

	/* input */
	global $mid;
	global $sid;
	global $sort;
	global $user;

	if ($user->role_id==ROLE_ADMINISTRATOR) {
	
		$query  = 'select a.project_id, a.name, a.public from project a  ';	
		$query .= 'where a.deleted=0 ';
	
	} else {
	
		$query  = 'select a.project_id, a.name, a.public from project a  ';	
		$query .= 'left join project_user b on b.project_id=a.project_id ';
		$query .= 'where a.deleted=0 ';
		$query .= 'and (b.user_id='.$user->user_id.' or a.public=1)';
				
	}	
	$query .= 'order by a.name';
	
	$result = plaatscrum_db_query($query);
	
	$page ='<select id="'.$tag.'" name="'.$tag.'" ';

	if ($readonly) {
		$page .= 'disabled="true" ';
	} else {	
		$page .= 'onchange="javascript:link(\''.plaatscrum_token('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_FILTER.'&sort='.$sort).'\');" ';
	}
	$page .= '>';
								
	while ($data=plaatscrum_db_fetch_object($result)) {	
		$page.='<option value="'.$data->project_id.'"';
		
		if ($project_id == $data->project_id) {
			$page .= ' selected="selected"';
		}
		$page .= '>'.$data->name.'</option>';
	}
		
	$page.='</select>';
		
   return $page;
}

function plaatscrum_ui_release($tag, $release_id, $readonly=false, $empty=false) {

	/* input */
	global $user;
	global $access;

	$query  = 'select release_id, name from released where deleted=0 ';
	$query .= 'and project_id='.$user->project_id.' order by release_id';
	
	$result = plaatscrum_db_query($query);
	
	$page ='<select id="'.$tag.'" name="'.$tag.'" ';

	if ($readonly) {
		$page .= 'disabled="true" ';
	}
		
	$page .= '>'; 
			
	if ($empty) {
		$page .='<option value="0"> </option>';
	}
	
	while ($data=plaatscrum_db_fetch_object($result)) {	
		$page.='<option value="'.$data->release_id.'"';
		
		if ($release_id == $data->release_id) {
			$page .= ' selected="selected"';
		}
		$page .= '>'.$data->name.'</option>';
	}
		
	$page.='</select>';
		
   return $page;
}

function plaatscrum_ui_sprint($tag, $id, $readonly=false, $empty=false, $locked=false) {
	
	/* input */
	global $user;
	global $mid;
	global $sid;
	global $sort;
	
	$query  = 'select sprint_id, number from sprint ';
	$query .= 'where project_id='.$user->project_id.' and deleted=0 ';
	
	if ($locked) {
		$query .= 'and locked=0 ';
	}
	
	$query .= 'order by number';
	
	$result = plaatscrum_db_query($query);
	
	$page ='<select id="'.$tag.'" name="'.$tag.'" ';

	if ($readonly) {
		$page .= 'disabled="true" ';
	}
	
	if ($empty) {	
		$page .= 'onchange="javascript:link(\''.plaatscrum_token('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_FILTER.'&sort='.$sort).'\');" ';
	}
	
	$page .= '>'; 
	
	if ($empty) {
		$page .='<option value="0"> </option>';
	}
	
	while ($data=plaatscrum_db_fetch_object($result)) {	
	
		$page.='<option value="'.$data->sprint_id.'"';
	
		if ($id == $data->sprint_id) {
			$page .= ' selected="selected"';
		}
		$page .= '>';
		if ($data->number==0) {
			$page .= '0';
		} else {
			$page .= $data->number;
		}
		$page .= '</option>';
	}
			
	$page.='</select>';
	
   return $page;
}

function plaatscrum_ui_multi_day($tag, $id, $readonly=false, $empty=false) {
	
	/* input */
	global $mid;
	global $sid;
	global $sort;
			
	$page = '<select id="'.$tag.'" name="'.$tag.'[]" size="7" multiple="multiple" ';
	
	if ($readonly) {
		$page .= 'disabled="true" ';
	} 
	
	$page .= '>'; 
	
	for ($day=0; $day<7; $day++) {
	
		$page.='<option value="'.$day.'"';
		
		if (is_numeric(strpos($id, (string) $day))) {
			$page .= ' selected="selected"';
		}
		
		$page .= '>'.t('DAY_'.$day).'</option>';
	}
		
	$page .='</select>';
		
   return $page;
}

function plaatscrum_ui_multi_status($tag, $id, $readonly=false, $empty=false) {
	
	/* input */
	global $mid;
	global $sid;
	global $sort;
	
	$values = array(STATUS_TODO, STATUS_DOING, STATUS_REVIEW, STATUS_DONE, STATUS_SKIPPED, STATUS_ONHOLD);	

	$page  = '<script type="text/javascript">';
	$page .= '$(document).ready( function() { ';
	$page .= '$("#'.$tag.'").multiSelect({ ';
	$page .= ' selectAll: false,';
	$page .= ' noneSelected: \'0 '.t('GENERAL_SELECTED').'\', ';
	$page .= ' oneOrMoreSelected: \'% '.t('GENERAL_SELECTED').'\' ';
	$page .= '});';
	$page .= '});';
	$page .= '</script>';
	
	$page .= '<select id="'.$tag.'" name="'.$tag.'[]" size="6" multiple="multiple" ';
	
	if ($readonly) {
		$page .= 'disabled="true" ';
	} 
	
	$page .= '>'; 
	
	foreach ($values as $value) {
	
		$page.='<option value="'.$value.'"';
		
		if (is_numeric(strpos($id, (string) $value))) {
			$page .= ' selected="selected"';
		}
		$page .= '>'.t('STATUS_'.$value).'</option>';
	}
		
	$page .='</select>';
		
   return $page;
}

function plaatscrum_ui_status($tag, $id, $readonly=false, $empty=false) {
	
	if ($empty) {
	
		$values = array(0, STATUS_TODO, STATUS_DOING, STATUS_REVIEW, STATUS_DONE, STATUS_SKIPPED, STATUS_ONHOLD);
			
	} else {
		
		$values = array(STATUS_TODO, STATUS_DOING, STATUS_REVIEW, STATUS_DONE, STATUS_SKIPPED, STATUS_ONHOLD);	
	}

	$page ='<select id="'.$tag.'" name="'.$tag.'" ';
	
	if ($readonly) {
		$page .= 'disabled="true" ';
	}
	$page .= '>'; 
	
	foreach ($values as $value) {
	
		$page.='<option value="'.$value.'"';
		
		if ($id == $value) {
			$page .= ' selected="selected"';
		}
		$page .= '>'.t('STATUS_'.$value).'</option>';
	}
		
	$page.='</select>';
		
   return $page;
}


function plaatscrum_ui_language($tag, $id, $readonly=false) {
			
	$values = array(LANGUAGE_ENGLISH, LANGUAGE_DUTCH);	

	$page ='<select id="'.$tag.'" name="'.$tag.'" ';
	
	if ($readonly) {
		$page .= 'disabled="true" ';
	}
	$page .= '>'; 
	
	foreach ($values as $value) {
	
		$page.='<option value="'.$value.'"';
		
		if ($id == $value) {
			$page .= ' selected="selected"';
		}
		$page .= '>'.t('LANGUAGE_'.$value).'</option>';
	}
		
	$page.='</select>';
		
   return $page;
}


function plaatscrum_ui_prio($tag, $id, $readonly=false, $empty=false) {
	
	if ($empty) {
	
		$values = array(0, PRIO_MINOR, PRIO_MAJOR, PRIO_CRITICAL);
		
	} else {
	
		$values = array(PRIO_MINOR, PRIO_MAJOR, PRIO_CRITICAL);
	
	}
	
	$page ='<select id="'.$tag.'" name="'.$tag.'" ';
	
	if ($readonly) {
		$page .= 'disabled="true" ';
	}
	$page .= '>'; 
	
	foreach ($values as $value) {
	
		$page.='<option value="'.$value.'"';
		
		if ($id == $value) {
			$page .= ' selected="selected"';
		}
		$page .= '>'.t('PRIO_'.$value).'</option>';
	}
		
	$page.='</select>';
		
   return $page;
}

function plaatscrum_ui_multi_type($tag, $id, $readonly=false, $empty=false) {
				
	$values = array(TYPE_STORY, TYPE_BUG, TYPE_TASK, TYPE_EPIC);

	$page  = '<script type="text/javascript">';
	$page .= '$(document).ready( function() { ';
	$page .= '$("#'.$tag.'").multiSelect({ ';
	$page .= ' selectAll: false,';
	$page .= ' noneSelected: \'0 '.t('GENERAL_SELECTED').'\', ';
	$page .= ' oneOrMoreSelected: \'% '.t('GENERAL_SELECTED').'\' ';
	$page .= '});';
	$page .= '});';
	$page .= '</script>';
	
	$page .= '<select id="'.$tag.'" name="'.$tag.'[]" size="4" multiple="multiple" ';
	
	if ($readonly) {
		$page .= 'disabled="true" ';
	} 
	$page .= '>'; 
	
	foreach ($values as $value) {
	
		$page.='<option value="'.$value.'"';
		
		if (is_numeric(strpos($id, (string) $value))) {
			$page .= ' selected="selected"';
		}
		$page .= '>'.t('TYPE_'.$value).'&nbsp;&nbsp;</option>';
	}
		
	$page.='</select>';
			
   return $page;
}

function plaatscrum_ui_type($tag, $id, $readonly=false, $empty=false) {
	
	if ($empty) {
	
		$values = array(0, TYPE_STORY, TYPE_BUG, TYPE_TASK, TYPE_EPIC);
		
	} else {
	
		$values = array(TYPE_STORY, TYPE_BUG, TYPE_TASK, TYPE_EPIC);
		
	}
	
	$page ='<select id="'.$tag.'" name="'.$tag.'" ';
	
	if ($readonly) {
		$page .= 'disabled="true" ';
	}
	$page .= '>'; 
	
	foreach ($values as $value) {
	
		$page.='<option value="'.$value.'"';
		
		if ($id == $value) {
			$page .= ' selected="selected"';
		}
		$page .= '>'.t('TYPE_'.$value).'</option>';
	}
		
	$page.='</select>';
		
   return $page;
}

function plaatscrum_ui_role($tag, $id, $readonly) {
	
	/* input */
	global $user;
	
	$values = array(ROLE_GUEST, ROLE_SCRUM_MASTER, ROLE_PRODUCT_OWNER, ROLE_TEAM_MEMBER);
	
	$page ='<select id="'.$tag.'" name="'.$tag.'" ';
	if ($readonly) {
		$page .= 'disabled="true" ';
	}
	$page .= '>'; 
	
	foreach ($values as $value) {
	
		$page.='<option value="'.$value.'"';
		
		if ($id == $value) {
			$page .= ' selected="selected"';
		}
		$page .= '>'.t('ROLE_'.$value).'</option>';
	}
		
	$page.='</select>';
	
    return $page;
}

function plaatscrum_ui_member_role($tag, $id, $readonly) {
	
	/* input */
	global $user;
	
	$values = array(ROLE_USER, ROLE_ADMINISTRATOR);
	
	$page ='<select id="'.$tag.'" name="'.$tag.'" ';
	if ($readonly) {
		$page .= 'disabled="true" ';
	}
	$page .= '>'; 
	
	foreach ($values as $value) {
	
		$page.='<option value="'.$value.'"';
		
		if ($id == $value) {
			$page .= ' selected="selected"';
		}
		$page .= '>'.t('ROLE_'.$value).'</option>';
	}
		
	$page.='</select>';
	  
   return $page;
}

function plaatscrum_ui_checkbox($name, $value, $readonly) {

	$tmp = '<input type="checkbox" name="'.$name.'" id="'.$name.'" value="1" ';
	
	if ($value==1) {
		$tmp .= ' checked="checked"';
	} 
	
	if ($readonly==1) {
		$tmp .= ' disabled="true"';
	} 
	
	$tmp .= '/>';
	
	return $tmp;	
}

function plaatscrum_ui_radiobox($name, $value, $readonly) {

	$tmp = '<input type="radio" name="'.$name.'" value="1" ';
	
	if ($value==1) {
		$tmp .= ' checked="checked"';
	} 
	
	if ($readonly==1) {
		$tmp .= ' disabled="true"';
	} 
	
	$tmp .= '/>';
	
	return $tmp;	
}
	
function plaatscrum_ui_box($title, $message) {

	/* output */
	global $page;
	
	$page .= '<div id="box">';
	
	if ($title=="info") {

		$page .= '<b>'.t('GENERAL_INFO').'</b>: ';
		
	} else if ($title=="warning") {

		$page .= '<span class="warning"><b>'.t('GENERAL_WARNING').'</b></span>: ';
	
	} else if ($title=="error") {
 
		$page .= '<b>'.t('GENERAL_ERROR').'</b>: ';
				
	} else { 
	
		$page .= '<b>'.$title.'</b> ';
	} 
	
	$page .= $message;

	$page .= '</div>';		
}
	
function plaatscrum_ui_image($filename, $options="") {

	/*  input */
	global $config;
	
	$image = '<img '.$options.' src="'.$config["content_url"].'images/'.$filename.'" />';
	return $image;
}

function plaatscrum_ui_header( $title = "") {
   
	/* input */
	global $mid;
   global $sid;
	global $config;
	global $player;
	global $session;
	
	$page  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	$page .= '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="EN" lang="EN" dir="ltr">';
	$page .= '<head profile="http://gmpg.org/xfn/11">';

	$page .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
 
 	if ($mid==MENU_LOGIN) {
		
		$page .= '<meta name="keywords" content="plaatscrum,plaatsoft,scrum,taskboard,burndown,chart,velocity,calender,php,mysql" />';
		$page .= '<link rel="canonical" href="http://scrum.plaatsoft.nl" />';
		
		$page .= '<meta name="application-name" content="PlaatScrum" />';
		$page .= '<meta name="description" content="PlaatScrum is a scrum development tool" />';
		$page .= '<meta name="application-url" content="http://scrum.plaatsoft.nl" />';		
	}
	
	$page .= '<link href="'.$config["content_url"].'images/favicon.ico" rel="shortcut icon" type="image/x-icon" />'; 
	$page .= '<link href="'.$config["content_url"].'css/general.css" rel="stylesheet" type="text/css" />';
	$page .= '<link href="'.$config["content_url"].'css/jquery.css" rel="stylesheet" type="text/css" />';
						
	/* Add JavaScripts */
	$page .= '<script language="JavaScript" src="'.$config["content_url"].'js/link.js" type="text/javascript"></script>';
	$page .= '<script language="JavaScript" src="'.$config["content_url"].'js/jquery.js" type="text/javascript"></script>';
	$page .= '<script language="JavaScript" src="'.$config["content_url"].'js/jquery-ui.js" type="text/javascript"></script>';	
	$page .= '<script language="JavaScript" src="'.$config["content_url"].'js/jquery-multi.js" type="text/javascript"></script>';
	
	/* Add HTML Title */
	if ($title=="") {
		$page .= '<title>'.$config["applName"].'</title>';
	} else {
		$page .= '<title>'.$config["applName"].' - '.strtolower($title).'</title>';
	}
	$page .= "</head>";

	$page .= '<body id="top">';
	
	$page .= '<form id="scrumboard" ';
	if ($sid==PAGE_BACKLOG_IMPORT) {
		$page .= 'enctype="multipart/form-data" ';
	}
	$page .= 'method="POST">';
	
	/* Store session information for next request */	
	$page .= '<input type="hidden" name="session" value="'.$session.'" />';
	
	return $page;
}

function plaatscrum_ui_banner($menu) {
	
	/* input */
	global $mid;
	global $sid;
	
	global $user;
	global $config;
	global $access;
	
	$page = '<div class="wrapper">';
	
	$page .= '<div id="header">';
   
	$page .= '<div class="fl_left">';
	
   $page .= '<h1>';
	if ($mid==MENU_LOGIN) { 
		$page .= plaatscrum_link('mid='.MENU_LOGIN.'&sid='.PAGE_LOGIN, $config["applName"]);
	} else {	
		$page .= plaatscrum_link('mid='.MENU_HOME.'&sid='.PAGE_HOME, $config["applName"]);
	}
	$page .= '</h1>';
	
   $page .= '<p>';
	if (isset($user->user_id)) {
		
		$page .= $user->name.' ';
	
		$page .= ' [';
		$page .= t('ROLE_'.$access->role_id);
					
		if ($user->role_id==ROLE_ADMINISTRATOR) {
			$page .= '+';
		}
		$page .= ']';
		
	} else {
		$page .= $config['applVersion'];
	}
	$page .= '</p>';
  
   $page .= '</div>';
	
	if ($mid!=MENU_LOGIN) {
		$page .= '<div id="search">';		
		$page .= '<fieldset>';
		$page .= '<legend>Site Search</legend>';
		$page .= '<input type="text" name="search" id="search" value="'.t('HELP').'" onfocus="this.value=(this.value==\''.t('HELP').'\')? \'\' : this.value ;" />'; 	
		$page .= plaatscrum_button('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_SEARCH, "go", "go");		
		$page .= '</fieldset>';		
		$page .= '</div>';
	}
			
	$page .= '<br/>';
	$page .= '<br/>';
	$page .= '<br/>';
	
	$page .= '<div class="fl_right">';
	$page .= $menu;
	$page .= '</div>';		
	
   $page .= '<br class="clear" />';
   $page .= '</div>';

	$page .= '<div id="topbar">';
   $page .= '<div class="fl_left">';
		
	$page .= '</div>';
	$page .= '</div>';
	
	return $page;
}

function plaatscrum_ui_footer($renderTime, $queryCount) {

	global $config;
	global $player;
	global $mid;
			
	$page = '<br class="clear" />';
				
	$page .= '<div id="copyright">';
	
	$page .= '<p class="fl_left">';
	$page .= t('COPYRIGHT');
	$page .= '</p>';
	
	$page .= '<p class="fl_right">';
	$page .= 'Render time '.$renderTime.'ms - '.$queryCount.' Queries - '.memory_format(memory_get_peak_usage(true)).'';
	$page .= '</p>';
	$page .= '</div>';
	
	$page .= '<br class="clear" />';
	
	$page .= '</div>';
	
	$page .= '<br/>';
	$page .= '<br/>';
			
	$page .= '</form>';
	$page .= "</body>";
	$page .= "</html>";
	
	return $page;
}

/*
** ---------------------
** CONVERTS
** ---------------------
*/

function convert_date_mysql($date) {
	$part = preg_split('/-/', $date);
	return $part[2].'-'.$part[1].'-'.$part[0];
}

function convert_date_php($date) {
	return date("d-m-Y", strtotime($date));
}

function convert_datetime_php($date) {
	return date("d-m-Y H:i:s", strtotime($date));
}

function convert_number($value) {
   
	return number_format($value,0,",",".");
}

/*
** ---------------------
** FORMATTERS
** ---------------------
*/

function memory_format($size) {
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

/*
** ---------------------
** VALIDATION
** ---------------------
*/

/**
 * Function valid email address
 * @return true or false
 */
function validate_email($address) {

   return !preg_match("/[A-Za-z0-9_-]+([\.]{1}[A-Za-z0-9_-]+)*@[A-Za-z0-9-]+([\.]{1}[A-Za-z0-9-]+)+/",$address);
}

/*
** ---------------------------------------------------------------- 
** THE END
** ----------------------------------------------------------------
*/

?>