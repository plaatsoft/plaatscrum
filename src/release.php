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

$release_name = plaatscrum_post("release_name", "");
$release_note = plaatscrum_post("release_note", "");

/*
** ------------------
** ACTIONS
** ------------------
*/

function plaatscrum_release_save_do() {
	
	/* input */
	global $mid;	
	global $access;
	global $user;
	
	global $release_name;
	global $release_note;

	/* output */
	global $id;
	global $pid;
	
	$data = plaatscrum_db_release($id);
	
	if (strlen($release_name)==0) {
	
		$message = t('RELEASE_NAME_NOT_SET');
		plaatscrum_ui_box('warning', $message);
	
	} else if (($id>0) && ($data->name!=$release_name) && plaatscrum_db_release_unique_name($release_name)>0) {
	
		$message = t('RELEASE_NAME_ALREADY_USED');
		plaatscrum_ui_box('warning', $message);
		
	} else if (($id==0) && plaatscrum_db_release_unique_name($release_name)>0) {
	
		$message = t('RELEASE_NAME_ALREADY_USED');
		plaatscrum_ui_box('warning', $message);
		
	} else {
	
		if ($id>0) {
					
			$data->name = $release_name;
			$data->note = $release_note;
			$data->project_id = $user->project_id;
						
			plaatscrum_db_release_update($data);
		
		} else  {
		
			plaatscrum_db_release_insert($release_name, $release_note, $user->project_id);
		}
		
		plaatscrum_ui_box('info', t('RELEASE_SAVED'));
		plaatscrum_info('release ['.$id.'] saved');
		
		/* return parameters for project view */
		$pid=PAGE_PROJECT_FORM;
		$id=$user->project_id;	
	}
}

function plaatscrum_release_delete_do() {
	
	/* input */
	global $mid;	
	global $access;
	global $user;
	
	/* output */
	global $pid;
	global $id;
	
	$query = 'select sprint_id from sprint where deleted=0 and release_id='.$id	;
	$result = plaatscrum_db_query($query);
		
	$found=0;
	if ($data = plaatscrum_db_fetch_object($result)) {
		$found=1;
	}	
	
	$data = plaatscrum_db_release($id);
	
	if ($found==1) {
		
		$message = t('RELEASE_USED_IN_SPRINT');
		plaatscrum_ui_box('warning', $message);
		
	} else if (isset($data->release_id)) {

		plaatscrum_db_release_delete($id);

		plaatscrum_ui_box('info', t('RELEASE_DELETED'));
		plaatscrum_info('release ['.$id.'] deleted');
		
		/* return parameters for project view */
		$pid=PAGE_PROJECT_FORM;
		$id=$user->project_id;
	} 
}

/*
** ------------------
** UI
** ------------------
*/

function plaatscrum_release_form() {

	/* input */
	global $mid;
	global $pid;
	global $id;
	global $user;
	global $access;
	
	global $release_name;
	global $release_note;
	
	/* output */
	global $page;
	global $title;
	
	$readonly = true;
	if ($access->project_edit) {
		$readonly = false;
	}
		
	if ( (strlen($release_name)==0) && ($id!=0)) {
	
		$data = plaatscrum_db_release($id);
		
		$release_name = $data->name;
		$release_note = $data->note;
	}

	$title = t('RELEASE_TITLE');
	
	$page .= '<h1>';
	$page .= $title;
	$page .= '</h1>';
	
	$page .= '<div id="detail">';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_NAME').': *</label>';
	$page .= plaatscrum_ui_input('release_name', 10, 10, $release_name, $readonly);
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_NOTE').':</label>';
	$page .= plaatscrum_ui_input('release_note', 100, 100, $release_note, $readonly);
	$page .= '</p>';
	
	if (!$readonly) {
		$page .= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&id='.$id.'&eid='.EVENT_RELEASE_SAVE, t('LINK_SAVE'));
		$page .= ' ';
	}
	
	if (($id!=0) && !$readonly) {
		$page .= plaatscrum_link_confirm('mid='.$mid.'&pid='.$pid.'&id='.$id.'&eid='.EVENT_RELEASE_DELETE, t('LINK_DELETE'), t('RELEASE_DELETE_CONFIRM'));
		$page .= ' ';
	}	
	
	$page .= plaatscrum_link('mid='.$mid.'&pid='.PAGE_PROJECT_FORM.'&id='.$user->project_id, t('LINK_CANCEL'));
			
	$page .= '</p>';
			
	$page .= '</div>';
}

function plaatscrum_releaselist_form() {

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
	$page .= '<legend>'.t('RELEASE_TITLE').'</legend>';	
		
	$page .= '<p>';
	$page .= t('RELEASE_TEXT');
	$page .= '</p>';
		
	$query  = 'select release_id, name, note from released ';
	$query .= 'where project_id='.$user->project_id.' and deleted=0 ';
	$query .= 'order by release_id';	
	$result = plaatscrum_db_query($query);
	
	$page .= '<table>';
			
	$page .= '<thead>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_NAME');
	$page .= '</th>';
			
	$page .= '<th>';
	$page	.= t('GENERAL_NOTE');
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
		$page .= plaatscrum_link('mid='.$mid.'&pid='.PAGE_RELEASE_FORM.'&id='.$data->release_id, $data->name);	
		$page .= '</td>';
	
		$page .= '<td>';
		$page	.= $data->note;
		$page .= '</td>';
				
		$page .= '</tr>';	
	}
	$page .= '</tbody>';
	$page .= '</table>';
	
	$page .= '<p>';
	if ($access->project_edit && !$readonly) {
		$page .= plaatscrum_link('mid='.$mid.'&pid='.PAGE_RELEASE_FORM.'&id=0', t('LINK_ADD'));
	}
	$page .= '</p>';
	
	$page .= '</fieldset>';

}

/*
** ------------------
** HANDLERS
** ------------------
*/

function plaatscrum_release() {

	/* input */
	global $eid;
		global $pid;
	
	/* Event handler */
	switch ($eid) {
		
		case EVENT_RELEASE_SAVE: 
					plaatscrum_release_save_do();
					break;
				  
		case EVENT_RELEASE_DELETE: 
					plaatscrum_release_delete_do();
					break;			  	  		  
	}
	
	/* Page handler */
	switch ($pid) {
	
 	   case PAGE_RELEASELIST_FORM: 
					plaatscrum_releaselist_form();	
					break;		
				  
		case PAGE_RELEASE_FORM: 
					plaatscrum_release_form();
					break;
	}
}

/*
** ------------------
** THE END
** ------------------
*/

?>
