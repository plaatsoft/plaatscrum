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

$data = new StdClass();
$data->points= plaatscrum_post("points", 0);
$data->number = plaatscrum_post("number", "");
$data->summary = plaatscrum_post("summary", "");
$data->description = plaatscrum_post("description", "");
$data->reference = plaatscrum_post("reference", "");

if (isset($user->project_id)) {
	$data->project_id = plaatscrum_post("project_id", $user->project_id);
}

if (isset($user->sprint_id)) {
	$data->sprint_id = plaatscrum_post("sprint_id", $user->sprint_id);
}

$data->status = plaatscrum_post("status", STATUS_TODO);
$data->user_id = plaatscrum_post("user_id", 0);
$data->date = plaatscrum_post("date", "");
$data->prio = plaatscrum_post("prio", PRIO_MINOR);

/* New, type is given */
if (isset($type)) {
	$data->type = $type;
} else {
	$data->type = plaatscrum_post("type", TYPE_STORY);
}

/* New, story_story_id is given */
if (isset($story_id_ref)) {
	$data->story_id_ref = $story_id_ref;
} else {
	$data->story_id_ref = plaatscrum_post("story_id_ref", "");
}

/*
** ------------------
** ACTIONS
** ------------------
*/

/**
 * Update parent story if relate child is adapted 
 */ 
function plaatscrum_story_parent_update($story) {

	/* input */
	global $id;
	
	$total  = plaatscrum_db_story_ref_count($story->story_story_id, STATUS_NONE);
	$total2 = plaatscrum_db_story_ref_count($story->story_story_id, STATUS_DONE);
	$total3 = plaatscrum_db_story_ref_count($story->story_story_id, STATUS_SKIPPED);
	$total4 = plaatscrum_db_story_ref_count($story->story_story_id, STATUS_ONHOLD);
	$total5 = plaatscrum_db_story_ref_count($story->story_story_id, STATUS_REVIEW);
	$total6 = plaatscrum_db_story_ref_count($story->story_story_id, STATUS_TODO);

	$status = STATUS_DOING;
	
	if ($total==$total2) {
		
		$status = STATUS_DONE;
		
	} else if ($total==$total3) { 
		
		$status = STATUS_SKIPPED;
		
	} else if ($total==$total4) {
	
		$status = STATUS_ONHOLD;
		
	} else if ($total==$total5) {
		
		$status = STATUS_REVIEW;
	
	} else if ($total==$total6) {
		
		$status = STATUS_TODO;
	}
	
	$points = plaatscrum_db_story_points_count($story->story_story_id);
	
	/* Load parent, date values and save changes to database */
	$data = plaatscrum_db_story($story->story_story_id);	
	$data->points = $points;
	
	if ($data->status!=$status) {
		$data->status = $status;		
		$data->date = $story->date;
	}
	plaatscrum_db_story_update($data);
}

/** 
 * Create new story action
 */
function plaatscrum_story_new_do() {

	/* input */
	global $user;
	global $sid;

	/* output */
	global $data;
	
	$story_id = plaatscrum_db_story_check($data->story_id_ref, $user->project_id);
	$story = plaatscrum_db_story($story_id); 
	$sprint = plaatscrum_db_sprint($user->sprint_id); 

	if (isset($story->date)) {
		$data->date = $story->date;
	} else if (isset($sprint->start_date)) {
		$data->date = $sprint->start_date;
	} else {
		$data->date = date("Y-m-d", time());
	}
	
	if (isset($story->sprint_id)) {
		$data->sprint_id = $story->sprint_id;
	} else {
		$data->sprint_id = $user->sprint_id;
	}
	
	$data->story_id = 0;
	$data->number = plaatscrum_db_story_unique($user->project_id);
	$data->summary = "";
	$data->description = "";
	$data->status = STATUS_TODO;
	$data->points = 0;
	$data->reference = "";
	$data->project_id = $user->project_id;
	$data->prio = PRIO_MAJOR;
	$data->story_story_id = 0;
	
	if ($data->type == TYPE_STORY) {
	
		/* For story type, assign direct story to creator */
		$data->user_id = $user->user_id;
		
	} else {
	
		/* For all others types, user must manually assign owner */
		$data->user_id = 0;
	}	
}

/** 
 * Load existing story action
 */
function plaatscrum_story_load_do() {
	
	/* input */
	global $id;

	/* output */
	global $data;
	
	$query  = 'select a.story_id, a.number, a.summary, a.description, a.sprint_id, a.project_id, a.points, ';
	$query .= 'a.status, a.reference, a.date, a.prio, a.type, a.user_id, c.name, a.story_story_id, ';
	$query .= '(select b.number from story b where b.story_id=a.story_story_id) as story_id_ref ';
	$query .= 'from story a left join tuser c on a.user_id=c.user_id where a.story_id='.$id;		
	
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
} 
	
/**
 * Assign story to owner action
 */
function plaatscrum_story_assign_do() {

	/* input */
	global $id;	
	global $sid;	
	global $mid;	
	global $user;
	
	/* output */
	global $data;
	
	if ($id>0) {
				
		$story = plaatscrum_db_story($id);
		
		/* Update database */
		if ($story->status==STATUS_TODO) {
			
			plaatscrum_db_history_insert($story->story_id, $user->user_id, $story->status, STATUS_DOING);
		
			$story->status=STATUS_DOING;			
		}
				
		$story->user_id=$user->user_id;
		plaatscrum_db_story_update($story);
		
		if (($story->type!=TYPE_STORY) && ($story->story_story_id>0)) {
			
			plaatscrum_story_parent_update($story);
		}
		
		/* Update screen */
		$data->user_id = $user->user_id;
		$data->status = $story->status;
		
		$link = plaatscrum_link('mid='.$mid.'&sid='.PAGE_STORY.'&eid='.EVENT_STORY_LOAD.'&id='.$story->story_id, $story->number);
		
		plaatscrum_ui_box("info", t('STORY_ASSIGN', t('TYPE_'.$story->type), $link));
		plaatscrum_info($user->name.' ['.$user->user_id.'] assign story '.$id);		
	}
}

/** 
 * Drop assigment of story action
 */
function plaatscrum_story_drop_do() {

	/* input */
	global $id;	
	global $sid;	
	global $mid;	
	global $user;

	/* output */
	global $data;
	
	if ($id>0) {
	
		$story = plaatscrum_db_story($id);
		
		/* Update database */		
		if ($story->status==STATUS_DOING) {
			plaatscrum_db_history_insert($story->story_id, $user->user_id, $story->status, STATUS_TODO);
			
			$story->status=STATUS_TODO;
		}
		
		$story->user_id = 0;
		plaatscrum_db_story_update($story);
		
		if (($story->type!=TYPE_STORY) && ($story->story_story_id>0)) {
			
			plaatscrum_story_parent_update($story);
		}
		
		/* Update screen */		
		$data->user_id = 0;
		$data->status = $story->status;
		
		$link = plaatscrum_link('mid='.$mid.'&sid='.PAGE_STORY.'&eid='.EVENT_STORY_LOAD.'&id='.$story->story_id, $story->number);
		
		plaatscrum_ui_box("info", t('STORY_DROPPED', t('TYPE_'.$data->type), $link));	
		plaatscrum_info($user->name.' ['.$user->user_id.'] drop story '.$id);
	}
}

/**
 * Validate and save story data action
 */
function plaatscrum_story_save_do() {

	/* input */
	global $user;	
	global $id;	
	global $data;
	
	/* output */
	global $mid;
	global $sid;
	
	$story = plaatscrum_db_story($id);	
	$sprint = plaatscrum_db_sprint($data->sprint_id);
	$project = plaatscrum_db_project($user->project_id);
	
	if (strlen($data->number)==0) {
	
		plaatscrum_ui_box('warning', t('STORY_NUMBER_NEEDED'));
	
	} else if (!is_numeric($data->number)) {
	
		plaatscrum_ui_box('warning', t('STORY_NUMBER_INVALID', $data->number));		
		
	} else if (!is_numeric($data->points)) {
	
		if ($data->type==TYPE_STORY) {
			plaatscrum_ui_box('warning', t('STORY_POINTS_INVALID', $data->points));		
		} else {		
			plaatscrum_ui_box('warning', t('STORY_WORK_INVALID', $data->points));		
		}
	} else if (!isset($story->number) && (plaatscrum_db_story_number_check($data->number, $data->project_id)>0)) {
	
		plaatscrum_ui_box('warning', t('STORY_NUMBER_ALREADY_USED'));
	
	} else if (isset($story->number) && ($data->number!=$story->number) && 
	          (plaatscrum_db_story_number_check($data->number, $data->project_id)>0)) {
	
		plaatscrum_ui_box('warning', t('STORY_NUMBER_ALREADY_USED'));
	
	} else if (strlen($data->summary)==0) {
	
		plaatscrum_ui_box('warning', t('STORY_SUMMARY_NEEDED'));
		
	} else if ($data->sprint_id==0) {
	
		plaatscrum_ui_box('warning', t('STORY_SPRINT_NEEDED'));		
	
	} else if ( (strtotime($data->date) < strtotime($sprint->start_date)) || 
	            (strtotime($data->date) > strtotime($sprint->end_date)) ) {
	
		plaatscrum_ui_box('warning', t('STORY_DATE_INVALID', 
													convert_date_php($sprint->start_date), 
													convert_date_php($sprint->end_date)));		
	} else if (!is_numeric(strpos($project->days, (string) date('w',strtotime($data->date))))) {
	
		plaatscrum_ui_box('warning', t('STORY_NON_WORKING_DAY_SELECTED'));		
	
	} else if (($data->type!=TYPE_STORY) && (plaatscrum_db_story_check($data->story_id_ref, $data->project_id)==0)) {
	
		plaatscrum_ui_box('warning', t('STORY_DOES_NOT_EXIT',$data->story_id_ref));		

	} else if (($data->type==TYPE_STORY) && (strlen($data->story_id_ref)>0) && (plaatscrum_db_story_check($data->story_id_ref, $data->project_id)==0)) {
	
		plaatscrum_ui_box('warning', t('STORY_DOES_NOT_EXIT',$data->story_id_ref));	
	
	} else {
	
		$story_story_id = plaatscrum_db_story_check($data->story_id_ref, $data->project_id);
	
		if ($id>0) {
	
			/* Log status change */
			if ($story->status != $data->status){
				plaatscrum_db_history_insert($id, $user->user_id, $story->status, $data->status);
			}
	 
			$story->number = $data->number;
			$story->summary = $data->summary;
			$story->description = $data->description;
			$story->status = $data->status;
			$story->points = $data->points;			
			$story->reference = $data->reference;
			$story->sprint_id = $data->sprint_id;
			$story->project_id = $data->project_id;
			$story->date = convert_date_mysql($data->date);
			$story->prio = $data->prio;
			$story->type = $data->type;
			$story->user_id = $data->user_id;
			$story->story_story_id = $story_story_id;
		
			plaatscrum_db_story_update($story);
			
		} else {
		
			$id = plaatscrum_db_story_insert($data->number, $data->summary, $data->description, $data->status, $data->points, 
														$data->reference, $data->sprint_id, $data->project_id, $data->user_id, 
														convert_date_mysql($data->date), $data->prio, $data->type, 
														$story_story_id);
																
			/* Log status */
			plaatscrum_db_history_insert($id, $user->user_id, STATUS_NEW, STATUS_TODO);
			
		}

		if (($data->type!=TYPE_STORY) && ($story_story_id>0)) {
			
			$data = plaatscrum_db_story($id);
			plaatscrum_story_parent_update($data);
		}
				
		$link = plaatscrum_link('mid='.$mid.'&sid='.PAGE_STORY.'&eid='.EVENT_STORY_LOAD.'&id='.$id, $data->number);
	
		plaatscrum_ui_box("info", t('STORY_SAVED', t('TYPE_'.$data->type), $link));	
		plaatscrum_info($user->name.' ['.$user->user_id.'] save story '.$id);
		
		$mid = $user->menu_id;		
		$sid = $user->page_id;		
	}
}

/**
 * Delete Story action
 */
function plaatscrum_story_delete_do() {

	/* input */
	global $id;	
	global $user;	
		
	/* output */
	global $mid;
	global $sid;
	
	if ($id>0) {
	
		$data = plaatscrum_db_story($id);
		if (isset($data->summary)) {

			plaatscrum_db_story_delete($id);
				
			if (($data->type!=TYPE_STORY) && ($data->story_story_id>0)) {
			
				plaatscrum_story_parent_update($data);
			}
				
			plaatscrum_ui_box("info", t('STORY_DELETED', t('TYPE_'.$data->type), $data->number));
			plaatscrum_info($user->name.' ['.$user->user_id.'] delete story '.$id);
			
			$mid = $user->menu_id;		
			$sid = $user->page_id;
		}
	}
}

/** 
 * Cancel story action
 */
function plaatscrum_story_cancel_do() {

	/* input */
	global $user;
	
	/* output */
	global $sid;
	global $mid;

	$mid = $user->menu_id;		
	$sid = $user->page_id;
}

/*
** ------------------
** UI
** ------------------
*/

/**
 * Show table list of task/bug/epic related to story 
 */
function plaatscrum_tasklist($story_id, $readonly) {

	/* input */
	global $mid;
	global $sid;

	global $user;
	global $access;

	/* output */
	global $page;
	
	$title = t('GENERAL_TITLE_TASKS');
					
	$page .= '<h1>';	
	$page .= $title;
	$page .= '</h1>';
		
	$query  = 'select a.story_id, a.number, a.summary, a.points, a.status, a.user_id, b.name, b.user_id ';
	$query .= 'from story a left join tuser b on a.user_id=b.user_id ';
	$query .= 'where a.deleted=0 and a.type in ('.TYPE_TASK.','.TYPE_BUG.','.TYPE_EPIC.') and a.story_story_id='.$story_id.' ';
	$query .= 'order by a.number';	
		
	$result = plaatscrum_db_query($query);
				
	$page .= '<table>';
			
	$page .= '<thead>';
	$page .= '<tr>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_US');
	$page .= '</th>';
		
	$page .= '<th>';
	$page	.= t('GENERAL_SUMMARY');	
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_WORK');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_STATUS');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_OWNER');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_ACTION');
	$page .= '</th>';
	
	$page .= '</tr>';
	
	$page .= '</thead>';
	$page .= '<tbody>';
		
	$count =0;
	while ($data=plaatscrum_db_fetch_object($result)) {		
				
		$page .= '<tr ';
		
		if ((++$count % 2 ) == 1 ) {
			$page .= 'class="light" ';
		} else {
			$page .= 'class="dark" ';
		} 
		$page .='>';
		
		$page .= '<td>';	
		$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_STORY_LOAD.'&id='.$data->story_id, $data->number);
		$page .= '</td>';
		
		$page .= '<td>';			
		$page	.= $data->summary;		
		$page .= '</td>';
		
		$page .= '<td>';		
		$page	.= $data->points;
		$page .= '</td>';
		
		$page .= '<td>';
		$page	.= t('STATUS_'.$data->status);
		$page .= '</td>';
				
		$page .= '<td >';
		$page	.= $data->name;
		$page .= '</td>';
		
		$page .= '<td >';
		$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_STORY_LOAD.'&id='.$data->story_id, t('LINK_VIEW'));
		$page .= '</td>';
		
		$page .= '</tr>';
	}
	$page .= '</tbody>';
	$page .= '</table>';	
	
	$page .= '<p>';		
	
	if ($access->story_add && !$readonly) {
		$data = plaatscrum_db_story($story_id);
		$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_STORY_NEW.'&type='.TYPE_TASK.'&story_id_ref='.$data->number, t('LINK_ADD_TASK'));
		$page .= '&nbsp;&nbsp;&nbsp;';
		$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_STORY_NEW.'&type='.TYPE_BUG.'&story_id_ref='.$data->number, t('LINK_ADD_BUG'));
		$page .= '&nbsp;&nbsp;&nbsp;';		
		$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_STORY_NEW.'&type='.TYPE_EPIC.'&story_id_ref='.$data->number, t('LINK_ADD_EPIC'));		
	}
	$page .= '</p>';	
}

/**
 * Show table list of selected task/bug/epic history events 
 */
function plaatscrum_story_history() {

	/* input */
	global $id;
	
	/* output */
	global $page;
						
	$page .= '<h2>';
	$page .= t('HISTORY_TITLE');
	$page .= '</h2>';
			
	$query  = 'select a.date, a.user_id, a.status_old, a.status_new, b.name from history a left join tuser b on a.user_id=b.user_id ';
	$query .= 'where a.story_id='.$id.' order by history_id asc';
			
	$result = plaatscrum_db_query($query);
							
	$page .= '<p>';
	$page .= '</p>';
				
	$page .= '<table>';
			
	$page .= '<thead>';
	$page .= '<tr>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_DATE');
	$page .= '</th>';
		
	$page .= '<th>';
	$page	.= t('GENERAL_STATUS');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_OWNER');
	$page .= '</th>';
	
	$page .= '</tr>';
	
	$page .= '</thead>';
	$page .= '<tbody>';
		
	$count =0;
	while ($data=plaatscrum_db_fetch_object($result)) {		
				
		$page .= '<tr ';
		
		if ((++$count % 2 ) == 1 ) {
			$page .= 'class="light" ';
		} else {
			$page .= 'class="dark" ';
		} 
		$page .='>';
				
		$page .= '<td>';
		$page	.= convert_datetime_php($data->date);
		$page .= '</td>';
				
		$page .= '<td>';
		$page	.= t('STATUS_'.$data->status_old).' -> '.t('STATUS_'.$data->status_new);
		$page .= '</td>';
				
		$page .= '<td >';
		$page	.= $data->name;
		$page .= '</td>';

		$page .= '</td>';
		
		$page .= '</tr>';
	}
	$page .= '</tbody>';
	$page .= '</table>';	
}

/** 
 * Show detail story/task/bug/epic form
  */
function plaatscrum_story_form() {

	/* input */
	global $mid;
	global $sid;
	global $id;
	global $user;
	global $access;
	
	global $data;
	
	/* output */
	global $page;
	
	$sprint = plaatscrum_db_sprint($data->sprint_id);
	
	if (($access->story_edit==0) || (isset($sprint->locked) && ($sprint->locked==1))) {
		$readonly = true;
	} else {
		$readonly = false;
	}
	
	$page .= '<h1>';
	$page .= strtoupper(t('TYPE_'.$data->type));
	$page .= '</h1>';
				
	$page .= '<div id="respond">';
	$page .= '<div id="story">';
	$page .= '<table>';
	
	$page .= '<tr>';
	
	$page .= '<td>';			
	$page .= '<label>'.t('GENERAL_US').': *</label>';	
	$page .= plaatscrum_ui_input("number", 8, 5, $data->number, $readonly);
	$page .= '</td>';

	$page .= '<td>';				
	$page .= '<label>'.t('GENERAL_PROJECT').': *</label>';
	$page .= plaatscrum_ui_project('project_id', $data->project_id, true);
	$page .= '</td>';
	
	$page .= '<td>';				
	$page .= '<label>'.t('GENERAL_SPRINT').': *</label>';
	$page .= plaatscrum_ui_sprint('sprint_id', $data->sprint_id, $readonly, false, true);
	$page .= '</td>';
	
	$page .= '<td>';	
	if ($data->type==TYPE_STORY) {
		$page .= '<label>'.t('GENERAL_POINTS').':</label>';
		$page .= plaatscrum_ui_input("points", 5, 5, $data->points, true);
		$page .= plaatscrum_ui_input_hidden("points", $data->points);
	} else {
		$page .= '<label>'.t('GENERAL_WORK').': *</label>';
		$page .= plaatscrum_ui_input("points", 5, 5, $data->points, $readonly);
	}
	
	$page .= '</td>';
	
	$page .= '<td>';	
	$page .= '<label>'.t('GENERAL_STATUS').': *</label>';
	$page .= plaatscrum_ui_status('status', $data->status, $readonly);
	$page .= '</td>';
	
	$page .= '<td>';	
	$page .= '<label>'.t('GENERAL_DATE').': *</label>';
	$page .= plaatscrum_ui_datepicker("date", 10, 10, convert_date_php($data->date), $readonly);
	$page .= '</td>';

	$page .= '<td>';	
	$page .= '<label>'.t('GENERAL_PRIO').': *</label>';
	$page .= plaatscrum_ui_prio("prio", $data->prio, $readonly);
	$page .= '</td>';
	
	$page .= '<td>';	
	$page .= '<label>'.t('GENERAL_TYPE').': *</label>';
	$page .= plaatscrum_ui_type("type", $data->type, $readonly);
	$page .= '</td>';

	$page .= '<td>';	
	$page .= '<label>'.t('GENERAL_OWNER').':</label>';
	if ($access->role_id==ROLE_SCRUM_MASTER) {
		$page .= plaatscrum_ui_project_user("user_id", $data->user_id, $readonly, true);
	} else {
		$page .= plaatscrum_ui_project_user("user_id", $data->user_id, true);
	}		
	$page .= '</td>';
	
	$page .= '<td>';	
	$data->story_story_id = plaatscrum_db_story_check($data->story_id_ref, $data->project_id);
	
	if ($data->story_story_id!=0) {
		$link = plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_STORY_LOAD.'&id='.$data->story_story_id, t('GENERAL_STORY_STORY_ID'));
	} else {
		$link = t('GENERAL_STORY_STORY_ID');
	}
		
	$page .= '<label>'.$link.':</label>';			
	$page .= plaatscrum_ui_input("story_id_ref", 8, 5, $data->story_id_ref, $readonly);
	$page .= '</td>';
	
	$page .= '</tr>';
	
	$page .= '<tr>';	
	$page .= '<td colspan="10">';
	$page .= '<label>'.t('GENERAL_SUMMARY').': *</label>';
	$page .= plaatscrum_ui_textarea("summary", 1, 40, $data->summary, $readonly);
	$page .= '</td>';	
	$page .= '</tr>';
	
	$page .= '<tr>';	
	$page .= '<td colspan="10">';
	$page .= '<label>'.t('GENERAL_DESCRIPTION').':</label>';
	$page .= plaatscrum_ui_textarea("description", 5, 40, $data->description, $readonly);
	$page .= '</td>';	
	$page .= '</tr>';	
						
	$page .= '<tr>';	
	$page .= '<td colspan="10">';
	$page .= '<label>'.t('GENERAL_REFERENCE').':</label>';
	$page .= plaatscrum_ui_input("reference", 15, 20, $data->reference, $readonly);	
	$page .= '</td>';	
	$page .= '</tr>';	
	
	$page .= '<tr>';
	$page .= '<td colspan="10">';
	
	if (!$readonly) {
	
		if ($access->story_edit) {
			$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_STORY_SAVE.'&id='.$id, t('LINK_SAVE'));
			$page .= ' '; 
		}
	
		if	($access->story_edit && $data->user_id>0) {
			$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_STORY_DROP.'&id='.$id, t('LINK_DROP'));
			$page .= ' '; 
		} 
	
		if (($id!=0) && $access->story_edit && ($data->user_id==0)) {
			$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_STORY_ASSIGN.'&id='.$id, t('LINK_ASSIGN'));
			$page .= ' '; 
		}
			
		if (($id!=0) && $access->story_delete) {
			$page .= plaatscrum_link_confirm('mid='.$mid.'&sid='.$sid.'&id='.$id.'&eid='.EVENT_STORY_DELETE, t('LINK_DELETE'), t('STORY_DELETE_CONFIRM'));
			$page .= ' '; 
		}
	}
	$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_STORY_CANCEL, t('LINK_CANCEL'));
	$page .= '</td>';
	$page .= '</tr>';
	
	$page .= '</table>';
	$page .= '</p>'; 
	
	$page .= '<div id="note">';
	$page .= t('GENERAL_REQUIRED_FIELD');
	$page .= '</div>';
		
	$page .= '</div>';
	$page .= '</div>';
	$page .= '<br/>';
	
	if (($id!=0) && ($data->type==TYPE_STORY)) {
		plaatscrum_tasklist($id, $readonly);
	} 
	
	$project = plaatscrum_db_project($user->project_id);
	
	if (($project->history==1) && ($data->type!=TYPE_STORY) && $id!=0) {
		plaatscrum_story_history(); 
	}
}

/*
** ------------------
** HANDLER
** ------------------
*/

/**
 * story handler 
 */
function plaatscrum_story_events() {

	/* input */
	global $mid;
	global $sid;
	global $eid;
		
	/* Event handler */
	switch ($eid) {
	
		case EVENT_STORY_NEW: 
					plaatscrum_story_new_do();	
					break;
					
		case EVENT_STORY_LOAD: 
					plaatscrum_story_load_do();	
					break;
					
		case EVENT_STORY_SAVE: 
					plaatscrum_story_save_do();	
					break;
				  
		case EVENT_STORY_DELETE: 
					plaatscrum_story_delete_do();				
					break;
					
		case EVENT_STORY_ASSIGN:
					plaatscrum_story_assign_do();
					break;		

		case EVENT_STORY_DROP: 
					plaatscrum_story_drop_do();	
					break;
					
		case EVENT_STORY_CANCEL: 
					plaatscrum_story_cancel_do();	
					break;
	}
}

/*
** ------------------
** The End
** ------------------
*/

