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

$project_name = plaatscrum_post("project_name", "");
$project_public = plaatscrum_post("project_public", 0);
$project_days = plaatscrum_multi_post("project_days", "");
$project_history = plaatscrum_post("project_history", 0);

$user_id  = plaatscrum_post("user_id", 0);
$user_role  = plaatscrum_post("user_role", ROLE_USER);
$user_bcr  = plaatscrum_post("user_bcr", 0);

/*
** ------------------
** ACTIONS
** ------------------
*/

function plaatscrum_project_user_assign_do() {

	/* input */
	global $user;

	global $user_id;
	global $user_role;
	global $user_bcr;
	
	/* output */
	global $sid;
	global $id;
	
	$data = plaatscrum_db_project_user($user->project_id, $user_id);
	
	if (isset($data->user_id)) {
		
		$data->role_id=$user_role;
		$data->bcr=$user_bcr;
		
		plaatscrum_db_project_user_update($data);
	
	} else {
	
		plaatscrum_db_project_user_insert($user->project_id, $user_id, $user_role, $user_bcr);
	}
	
	plaatscrum_ui_box('info', t('PROJECT_USER_ASSIGN'));
	plaatscrum_info($user->name.' ['.$user->user_id.'] user ['.$user_id.'] assigned to project ['.$user->project_id.']');
		
	$sid = PAGE_PROJECT_FORM;
	$id = $user->project_id;
}


function plaatscrum_project_user_drop_do() {

	/* input */
	global $user_id;
	global $user;

	/* output */
	global $sid;
	global $id;
	
	$data = plaatscrum_db_project_user($user->project_id, $user_id);
	
	if (isset($data->user_id)) {
			
		plaatscrum_db_project_user_delete($user->project_id, $user_id);
		
		plaatscrum_ui_box('info', t('PROJECT_USER_DROP'));
		plaatscrum_info($user->name.' ['.$user->user_id.'] user ['.$user_id.'] dropped to project ['.$user->project_id.']');
	}
	
	$sid = PAGE_PROJECT_FORM;
	$id = $user->project_id;
}

function plaatscrum_project_save_do() {
	
	/* input */
	global $mid;
	global $sid;
	global $id;
	global $user;
	
	global $project_name;
	global $project_public;
	global $project_days;
	global $project_history;
	
	$project = plaatscrum_db_project($id);
	
	if (strlen($project_name)==0) {
	
		$message = t('PROJECT_NAME_EMPTY');
		plaatscrum_ui_box('warning', $message);
			
	} else if (($id==0) && (plaatscrum_db_project_unique($project_name)>0)) {
	
		$message = t('PROJECT_NAME_ALREADY_USED');
		plaatscrum_ui_box('warning', $message);
	
	} else if (($id>0) && ($project_name!=$project->name) && (plaatscrum_db_project_unique($project_name)>0)) {
	
		$message = t('PROJECT_NAME_ALREADY_USED');
		plaatscrum_ui_box('warning', $message);
		
	} else {
	
		if ($id>0) {
			$data = plaatscrum_db_project($id);
			$data->name = $project_name;
			$data->public = $project_public;
			$data->days = $project_days;
			$data->history = $project_history;
			plaatscrum_db_project_update($data);
		
		} else  {
		
			/* Create project and return id */
			$id = plaatscrum_db_project_insert($project_name, $project_public, $project_days, $project_history);
			$user->role_id= ROLE_SCRUM_MASTER;
		        plaatscrum_db_project_user_insert($id, $user->user_id, $user->role_id, 0);
		}
		
		plaatscrum_ui_box('info', t('PROJECT_SAVED'));
		plaatscrum_info($user->name.' ['.$user->user_id.'] project ['.$id.'] saved!');	
	} 
}

function plaatscrum_project_delete_do() {
	
	/* input */
	global $mid;
	global $sid;
	global $id;
				
	$data = plaatscrum_db_project($id);
	
	if (isset($data->project_id)) {

		plaatscrum_db_project_delete($id);

		plaatscrum_ui_box('info', t('PROJECT_DELETED'));
		plaatscrum_info($user->name.' ['.$user->user_id.'] project ['.$id.'] deleted');
	} 
}

/*
** ------------------
** UI
** ------------------
*/

function plaatscrum_project_user_assign() {

	/* input */
	global $mid;
	global $sid;
	global $page;

	global $user;
	global $access;
	
	global $id;
	global $user_role;
	global $user_bcr;
	
	$readonly = true;
	if ($access->project_edit) {
		$readonly = false;
	}
	
	$data = plaatscrum_db_project_user($user->project_id, $id);
	if (isset($data->user_id)) {
		$user_role = $data->role_id;
		$user_bcr = $data->bcr;
	}
	
	$page .= '<div id="detail">';
	
	$page .= '<h1>';
	$page .= t('PROJECT_USER_TITLE');
	$page .= '</h1>';
	
	$page .= '<p>';
	$page	.= '<label>'.t('GENERAL_USER').': *</label>';
	$page .= plaatscrum_ui_all_user('user_id', $id, $readonly);		
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_ROLE').': *</label>';
	$page .= plaatscrum_ui_role("user_role", $user_role, $readonly);
	$page .= '</p>';
	
	if ($access->role_id==ROLE_SCRUM_MASTER) {
		$page .= '<p>';
		$page .= '<label>'.t('GENERAL_BCR').':</label>';
		$page .= plaatscrum_ui_input("user_bcr", 5, 10, $user_bcr, $readonly);
		$page .= '</p>';
	}
	
	$page .= '<p>';
	if (!$readonly || ($user->role_id==ROLE_ADMINISTRATOR)) {
		$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_USER_ASSIGN, t('LINK_SAVE'));
		$page .= ' ';
	}
	
	if (($id>0) && ($user->user_id!=$id) && (!$readonly || ($user->role_id==ROLE_ADMINISTRATOR))) {
		$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_USER_DROP, t('LINK_DELETE'));
		$page .= ' ';
	}
		
	$page .= plaatscrum_link('mid='.$mid.'&sid='.PAGE_PROJECT_FORM.'&id='.$user->project_id, t('LINK_CANCEL'));
	
	$page .= '</div>';
}

function plaatscrum_project_userlist_form() {

	/* input */
	global $mid;
	global $sid;
	global $user;
	global $access;

	/* output */
	global $page;			
	
	$page .= '<fieldset>';
	$page .= '<legend>'.t('USERS_TITLE').'</legend>';	
	
	$page .= '<p>';
	$page .= t('PROJECT_USER_TEXT');
	$page .= '</p>';
		
	$query  = 'select a.user_id, a.bcr, b.name, a.role_id from project_user a ';
	$query .= 'left join tuser b on a.user_id=b.user_id where a.project_id='.$user->project_id.' ';
	$query .= 'order by b.name';
		
	$result = plaatscrum_db_query($query);

	$page .= '<table>';
		
	$page .= '<thead>';
	$page .= '<tr>';
		
	$page .= '<th>';
	$page	.= t('GENERAL_NAME');
	$page .= '</th>';
					
	$page .= '<th>';
	$page	.= t('GENERAL_ROLE');
	$page .= '</th>';

	if ($access->role_id==ROLE_SCRUM_MASTER) {

		$page .= '<th>';
		$page	.= t('GENERAL_BCR');
		$page .= '</th>';
	}
		
	$page .= '<th>';
	$page	.= t('GENERAL_ACTION');
	$page .= '</th>';
	
	$page .= '</tr>';
	$page .= '</thead>';
		
	$page .= '<tbody>';
		
	$count=0;
	while ($data=plaatscrum_db_fetch_object($result)) {				
				$page .= '<tr ';
		if ((++$count % 2 ) == 1 ) {
			$page .= 'class="light" ';
		} else {
			$page .= 'class="dark" ';
		} 
		$page .='>';
				
		$page .= '<td>';
		$page	.= $data->name;
		$page .= '</td>';
			
		$page .= '<td>';
		$page	.= t('ROLE_'.$data->role_id);
		$page .= '</td>';
		
		if ($access->role_id==ROLE_SCRUM_MASTER) {
		
			$page .= '<td>';
			$page	.= $data->bcr;
			$page .= '</td>';
		}
		
		$page .= '<td>';	
		$page .= plaatscrum_link('mid='.$mid.'&sid='.PAGE_PROJECT_USER_ASSIGN.'&id='.$data->user_id, t('LINK_VIEW'));
		$page .= '</td>';
		
		$page .= '</tr>';	
	}
	$page .= '</tbody>';
	$page .= '</table>';
	
	$page .= '<p>';	
	if (($access->project_edit) || ($user->role_id==ROLE_ADMINISTRATOR)) {
		$page .= plaatscrum_link('mid='.$mid.'&sid='.PAGE_PROJECT_USER_ASSIGN.'&id=0', t('LINK_ADD'));
	}	
	$page .= '</p>';
	
	$page .= '</fieldset>';
}


function plaatscrum_project_form() {

	/* input */
	global $mid;
	global $sid;
	global $id;
	global $user;
	global $access;
	
	global $project_name;
	global $project_public;
	global $project_history;
	global $project_days;
	
	/* output */
	global $page;
	global $title;
		
	$title = t('PROJECT_TITLE');
		
	$page .= '<h1>';
	$page .= $title;
	$page .= '</h1>';
	
	$page .= '<div id="detail">';
	
	/* Store selected project as default */
	$user->project_id=$id;
	plaatscrum_db_user_update($user);
	
	$readonly = true;
	if ($user->role_id==ROLE_ADMINISTRATOR) {
		$readonly = false;
	}
	
	if ((strlen($project_name)==0) && ($id!=0)) {
	
		$data = plaatscrum_db_project($id);
		
		$project_name = $data->name;
		$project_public = $data->public;
		$project_history = $data->history;
		$project_days = $data->days;
	}
	
	$page .= '<fieldset>' ;
	$page .= '<legend>'.t('PROJECT_GENERAL').'</legend>';
		
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_NAME').':</label>';
	$page .= plaatscrum_ui_input("project_name", 30, 50, $project_name, $readonly);
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_PUBLIC').':</label>';
	$page .= plaatscrum_ui_checkbox("project_public", $project_public, $readonly);
	$page .= '<span id="tip">'.t('PROJECT_PUBLIC_NOTE').'</span>';
	$page .= '</p>';
		
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_HISTORY').':</label>';
	$page .= plaatscrum_ui_checkbox("project_history", $project_history, $readonly);
	$page .= '<span id="tip">'.t('PROJECT_HISTORY_NOTE').'</span>';
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_DAYS').':</label>';
	$page .= plaatscrum_ui_multi_day("project_days", $project_days, $readonly);
	$page .= '</p>';
	
	$page .= '</fieldset>' ;
	
	if ($id>0) {
		plaatscrum_releaselist_form($readonly);
	
		plaatscrum_sprintlist_form($readonly);
	
		plaatscrum_project_userlist_form($readonly);
	}

	$page .= '<p>';
	
	if ($user->role_id==ROLE_ADMINISTRATOR) {
		$page .= plaatscrum_link('mid='.$mid.'&sid='.PAGE_PROJECT_FORM.'&id='.$id.'&eid='.EVENT_PROJECT_SAVE, t('LINK_SAVE'));
		$page .= ' ';
	}
	
	if (($user->role_id==ROLE_ADMINISTRATOR) && ($id!=0)) {
		$page .= plaatscrum_link_confirm('mid='.$mid.'&sid='.PAGE_PROJECTLIST_FORM.'&id='.$id.'&eid='.EVENT_PROJECT_DELETE, t('LINK_DELETE'), t('USER_DELETE_CONFIRM'));
		$page .= ' ';
	}	
	
	$page .= plaatscrum_link('mid='.$mid.'&sid='.PAGE_PROJECTLIST_FORM, t('LINK_CANCEL'));
	
	$page .= '</p>';
	
	$page .= '</div>';
}

function plaatscrum_projectlist_form() {

	/* input */
	global $mid;
	global $sid;
	global $user;
	global $access;
	global $sort;

	/* output */
	global $page;

	$readonly = true;
	if ($access->project_edit) {
		$readonly = false;
	} 
	
	global $title;

	$title = t('PROJECTS_TITLE');
			
	$page .= '<h1>';
	$page .= $title;
	$page .= '</h1>';
	
	$page .= '<p>';
	$page .= t('PROJECT_TEXT');
	$page .= '</p>';
	
	if ($user->role_id==ROLE_ADMINISTRATOR) {
	
		$query  = 'select a.project_id, a.name, a.public, a.history from project a  ';	
		$query .= 'where a.deleted=0 ';
	
	} else {
	
		$query  = 'select a.project_id, a.name, a.public, a.history from project a  ';	
		$query .= 'left join project_user b on b.project_id=a.project_id ';
		$query .= 'where a.deleted=0 ';
		$query .= 'and (b.user_id='.$user->user_id.' or a.public=1)';
	}
	
	switch ($sort) {
		   
		 case 1: $query .= 'order by a.project_id';
				   break;

		 default:
	    case 2: $query .= 'order by a.name';
				   break;	

		 case 3: $query .= 'order by a.public';
				   break;	
		
		 case 4: $query .= 'order by a.history';
				   break;	
						
	}
	
	$result = plaatscrum_db_query($query);

	$page .= '<table>';
		
	$page .= '<thead>';
	$page .= '<tr>';
		
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&sort=2', t('GENERAL_NAME'));
	$page .= '</th>';
					
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&sort=3', t('GENERAL_PUBLIC'));
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&sort=4', t('GENERAL_HISTORY'));
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_OVERVIEW');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_ACTION');
	$page .= '</th>';
	
	$page .= '</tr>';
	$page .= '</thead>';
		
	$page .= '<tbody>';
		
	$count=0;
	while ($data=plaatscrum_db_fetch_object($result)) {				
				$page .= '<tr ';
		if ((++$count % 2 ) == 1 ) {
			$page .= 'class="light" ';
		} else {
			$page .= 'class="dark" ';
		} 
		$page .='>';
				
		$page .= '<td>';
		$page	.= $data->name;
		$page .= '</td>';
	
		$page .= '<td>';
		$page	.= t('GENERAL_'.$data->public);
		$page .= '</td>';
		
		$page .= '<td>';
		$page	.= t('GENERAL_'.$data->history);
		$page .= '</td>';
		
		$page .= '<td>';
		$page	.= plaatscrum_db_story_count($data->project_id, TYPE_STORY).' '.t('GENERAL_STORIES').'<br/>';
		$page	.= plaatscrum_db_story_count($data->project_id, TYPE_TASK).' '.t('GENERAL_TASKS').'<br/>';
		$page	.= plaatscrum_db_story_count($data->project_id, TYPE_BUG).' '.t('GENERAL_BUGS').'<br/>';
		$page	.= plaatscrum_db_story_count($data->project_id, TYPE_EPIC).' '.t('GENERAL_EPICS').'<br/>';
		$page .= '</td>';
				
		$page .= '<td>';
		$page .= plaatscrum_link('mid='.$mid.'&sid='.PAGE_PROJECT_FORM.'&id='.$data->project_id, t('LINK_VIEW'));
		$page .= '</td>';
		
		$page .= '</tr>';	
	}
	$page .= '</tbody>';
	$page .= '</table>';
	
	$page .= '<p>';
	if ($user->role_id==ROLE_ADMINISTRATOR) {
		$page .= plaatscrum_link('mid='.$mid.'&sid='.PAGE_PROJECT_FORM.'&id=0', t('LINK_ADD'));
	}
	$page .= '</p>';
}

/*
** ------------------
** HANDLERS
** ------------------
*/

function plaatscrum_project_event_handler() {

	/* input */
	global $eid;
	
	/* Event handler */
	switch ($eid) {
		
		case EVENT_PROJECT_SAVE: 
					plaatscrum_project_save_do();
					break;
				  
		case EVENT_PROJECT_DELETE: 
					plaatscrum_project_delete_do();
					break;		

		case EVENT_USER_ASSIGN: 
					plaatscrum_project_user_assign_do();
					break;		
					
		case EVENT_USER_DROP: 
					plaatscrum_project_user_drop_do();
					break;		  	  		  
	}
}

function plaatscrum_project_page_handler() {

	/* input */
	global $sid;
	
	/* Page handler */
	switch ($sid) {
	
 	   case PAGE_PROJECTLIST_FORM: 
					plaatscrum_projectlist_form();	
					break;		
				  
		case PAGE_PROJECT_FORM: 
					plaatscrum_project_form(false);
					break;
					
		case PAGE_PROJECT_USER_ASSIGN:
					plaatscrum_project_user_assign();
					break;
	}
}

/*
** ------------------
** THE END
** ------------------
*/

?>
