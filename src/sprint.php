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

$sprint_number = plaatscrum_post("sprint_number", "");
$sprint_description = plaatscrum_post("sprint_description", "");
$sprint_start_date = plaatscrum_post("sprint_start_date", date("Y-m-d", time()));
$sprint_end_date = plaatscrum_post("sprint_end_date", date("Y-m-d", time()+(60*60*24*7)));
$sprint_release = plaatscrum_post("sprint_release", "");
$sprint_locked = plaatscrum_post("sprint_locked", 0);
	
/*
** ------------------
** ACTIONS
** ------------------
*/

function plaatscrum_sprint_save_do() {
	
	/* input */
	global $mid;	
	global $access;
	global $user;
	
	global $sprint_number;
	global $sprint_description;
	global $sprint_start_date;
	global $sprint_end_date;
	global $sprint_release;
	global $sprint_locked;
	
	/* output */
	global $id;
	global $pid;
	
	if (strlen($sprint_number)==0) {
	
		$message = t('SPRINT_NUMBER_NOT_SET');
		plaatscrum_ui_box('warning', $message);
		
	} else if ($sprint_release==0) {
	
		$message = t('SPRINT_RELEASE_NOT_SET');
		plaatscrum_ui_box('warning', $message);
	
	} else {
	
		if ($id>0) {
			$data = plaatscrum_db_sprint($id);
			
			$data->number = $sprint_number;
			$data->description = $sprint_description;
			$data->start_date = convert_date_mysql($sprint_start_date);
			$data->end_date = convert_date_mysql($sprint_end_date);
			$data->project_id = $user->project_id;
			$data->release_id = $sprint_release;
			$data->locked = $sprint_locked;
			
			plaatscrum_db_sprint_update($data);
		
		} else  {
		
			plaatscrum_db_sprint_insert($sprint_number, $sprint_description, 
				convert_date_mysql($sprint_start_date), convert_date_mysql($sprint_end_date), 
				$user->project_id, $sprint_release, $sprint_locked);
		}
		
		plaatscrum_ui_box('info', t('SPRINT_SAVED'));
		plaatscrum_info($user->name.' ['.$user->user_id.'] save sprint '.$id);
		
		/* return parameters for project view */
		$pid = PAGE_PROJECT_FORM;
		$id = $user->project_id;	
	} 	
}

function plaatscrum_sprint_delete_do() {
	
	/* input */
	global $mid;	
	global $access;
	global $user;
	
	/* output */
	global $pid;
	global $id;
				
	$query = 'select count(story_id) as total from story where deleted=0 and sprint_id='.$id;
	$result = plaatscrum_db_query($query);
	
	$total=0;
	if ($data = plaatscrum_db_fetch_object($result)) {
		$total = $data->total;
	}	
	
	$data = plaatscrum_db_sprint($id);
	
	if (isset($data->sprint_id)) {

		if ($total>0) {
			
			plaatscrum_ui_box('info', t('SPRINT_IS_USED',$total));
			
		} else {
		
			plaatscrum_db_sprint_delete($id);

			plaatscrum_ui_box('info', t('SPRINT_DELETED'));
			plaatscrum_info($user->name.' ['.$user->user_id.'] delete sprint '.$id);
		
			/* return parameters for project view */
			$pid=PAGE_PROJECT_FORM;
			$id=$user->project_id;
		}
	} 
}

/*
** ------------------
** UI
** ------------------
*/

function plaatscrum_sprint_form() {

	/* input */
	global $mid;
	global $pid;
	global $id;
	global $user;
	global $access;
	
	/* output */
	global $page;
	global $title;
	
	global $sprint_number;
	global $sprint_description ;
	global $sprint_start_date;
	global $sprint_end_date;
	global $sprint_release;
	global $sprint_locked;
		
	if ((strlen($sprint_number==0)) && ($id!=0)) {
	
		$data = plaatscrum_db_sprint($id);
		
		$sprint_number = $data->number;
		$sprint_description = $data->description;
		$sprint_start_date = $data->start_date;
		$sprint_end_date = $data->end_date;
		$sprint_release = $data->release_id;
		$sprint_locked = $data->locked;
	}

	$readonly = true;
	if ($access->project_edit) {
		$readonly = false;
	}
	
	$title = t('SPRINT_TITLE');
	
	$page .= '<h1>';
	$page .= $title;
	$page .= '</h1>';
	
	$page .= '<div id="detail">';
				
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_SPRINT').': *</label>';
	$page .= plaatscrum_ui_input("sprint_number", 5, 5, $sprint_number, $readonly);
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_DESCRIPTION').':</label>';
	$page .= plaatscrum_ui_input('sprint_description', 70, 50, $sprint_description, $readonly);
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_START_DATE').': *</label>';
	$page .= plaatscrum_ui_datepicker("sprint_start_date", 10, 10, convert_date_php($sprint_start_date), $readonly);
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_END_DATE').': *</label>';
	$page .= plaatscrum_ui_datepicker("sprint_end_date", 10, 10, convert_date_php($sprint_end_date), $readonly);
	$page .= '</p>';
				
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_RELEASE').': *</label>';
	$page .= plaatscrum_ui_release("sprint_release", $sprint_release, $readonly, true);
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_LOCKED').':</label>';
	$page .= plaatscrum_ui_checkbox("sprint_locked", $sprint_locked, $readonly);
	$page .= '<span id="tip">'.t('SPRINT_LOCKED_NOTE').'</span>';
	$page .= '</p>';
	
	$page .= '<p>';
	if (!$readonly) {
		$page .= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&id='.$id.'&eid='.EVENT_SPRINT_SAVE, t('LINK_SAVE'));
		$page .= ' ';
	}
	
	if (($id!=0) && !$readonly) {
		$page .= plaatscrum_link_confirm('mid='.$mid.'&pid='.$pid.'&id='.$id.'&eid='.EVENT_SPRINT_DELETE, t('LINK_DELETE'), t('SPRINT_DELETE_CONFIRM'));
		$page .= ' ';
	}	
	
	$page .= plaatscrum_link('mid='.$mid.'&pid='.PAGE_PROJECT_FORM.'&id='.$user->project_id, t('LINK_CANCEL'));
			
	$page .= '</p>';
	
	$page .= '</div>';
}

function plaatscrum_sprintlist_form() {

	/* input */
	global $mid;
	global $pid;
	
	global $user;
	global $access;
	
	/* output */
	global $page;
	
	$readonly = true;
	if ($access->project_edit) {
		$readonly = false;
	}
	
	$page .= '<fieldset>' ;
	$page .= '<legend>'.t('SPRINT_TITLE').'</legend>';	
	
	$page .= '<p>';
	$page .= t('SPRINT_TEXT');
	$page .= '</p>';
		
	$query  = 'select a.sprint_id, a.number, a.description, a.start_date, a.end_date, b.name, a.locked ';
	$query .= 'from sprint a ';
	$query .= 'left join released b on a.release_id=b.release_id ';
	$query .= 'where a.deleted=0 and a.project_id='.$user->project_id.' order by a.number';	
	$result = plaatscrum_db_query($query);

	$page .= '<table>';
			
	$page .= '<thead>';
	$page .= '<th>';
	$page	.= t('GENERAL_SPRINT');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_DESCRIPTION');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_START_DATE');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_END_DATE');
	$page .= '</th>';
		
	$page .= '<th>';
	$page	.= t('GENERAL_DURATION');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_RELEASE');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_LOCKED');
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
		$page .= $data->number;
		$page .= '</td>';
	
		$page .= '<td>';
		$page	.= $data->description;
		$page .= '</td>';
	
		$page .= '<td>';	
		$page	.= convert_date_php($data->start_date);
		$page .= '</td>';
		
		$page .= '<td>';
		$page	.= convert_date_php($data->end_date);
		$page .= '</td>';
		
		$page .= '<td>';
		$page	.= round((strtotime($data->end_date) - strtotime($data->start_date) + (60*60*24))/60/60/24).' '.t('GENERAL_DAYS');
		$page .= '</td>';
		
		$page .= '<td>';
		$page	.= $data->name;
		$page .= '</td>';
		
		$page .= '<td>';
		$page	.= t('GENERAL_'.$data->locked);
		$page .= '</td>';
		
		$page .= '<td>';
		$page .= plaatscrum_link('mid='.$mid.'&pid='.PAGE_SPRINT_FORM.'&id='.$data->sprint_id, t('LINK_VIEW'));
		$page .= '</td>';
				
		$page .= '</tr>';	
	}
	$page .= '</tbody>';
	$page .= '</table>';
	
	$page .= '<p>';
	if ($access->project_edit && !$readonly) {
		$page .= plaatscrum_link('mid='.$mid.'&pid='.PAGE_SPRINT_FORM.'&id=0', t('LINK_ADD'));
	}
	$page .= '</p>';
	
	$page .= '</fieldset>';
}

/*
** ------------------
** HANDLERS
** ------------------
*/

function plaatscrum_sprint_event_handler() {

	/* input */
	global $eid;
	
	/* Event handler */
	switch ($eid) {
		
		case EVENT_SPRINT_SAVE: 
					plaatscrum_sprint_save_do();
					break;
				  
		case EVENT_SPRINT_DELETE: 
					plaatscrum_sprint_delete_do();
					break;			  	  		  
	
	}
}

function plaatscrum_sprint_page_handler() {

	/* input */
	global $pid;
	
	/* Page handler */
	switch ($pid) {
	
 	   case PAGE_SPRINTLIST_FORM: 
					plaatscrum_sprintlist_form();	
					break;		
				  
		case PAGE_SPRINT_FORM: 
					plaatscrum_sprint_form();
					break;
				  
	}
}

/*
** ------------------
** THE END
** ------------------
*/

?>
