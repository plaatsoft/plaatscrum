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


/*
** ------------------
** ACTIONS
** ------------------
*/

/*
** ------------------
** UI
** ------------------
*/

/** 
 * Create status board column
 */
function plaatscrum_status_colom_part($status) {

	/* output */
	global $page;
	global $user;
	global $access;
	
	$query  = 'select a.story_id, a.type, a.number, a.summary, b.number as sprint_number, a.points, a.status, a.user_id, c.name ';
	$query .= 'from story a left join sprint b on b.sprint_id=a.sprint_id left join tuser c on a.user_id=c.user_id ';
	$query .= 'left join project d on a.project_id=d.project_id where a.deleted=0 and a.status='.$status.' ';	
	$query .= 'and a.project_id='.$user->project_id.' and a.type in ('.TYPE_STORY.') ';	
	
	if ($user->sprint_id>0) {
		$query .= 'and a.sprint_id='.$user->sprint_id.' ';	
	}
		
	$result = plaatscrum_db_query($query);
		
	while ($data=plaatscrum_db_fetch_object($result)) {		
		
		$page .= plaatscrum_board_element($data);

	}
}

/**
 * Create statusboard form 
 */
function plaatscrum_storyboard_form() {

	/* input */
	global $mid;
	global $pid;
	
	/* output */
	global $page;
	global $title;
				
	$title = t('STATUSBOARD_TITLE');
				
	$page .= '<h1>';
	$page .= $title;
	$page .= '</h1>';
		
	$page .= '<div id="taskboard">';
	
	$page .= t('STATUSBOARD_NOTE');
	
	$page .= '<table>';
	$page .= '<thead>';
	$page .= '<tr>';
	
	$page .= '<th width="17%">';
	$page .= t('STATUS_1');
	$page .= '</th>';

	$page .= '<th width="17%">';
	$page .= t('STATUS_2');
	$page .= '</th>';
	
	$page .= '<th width="17%">';
	$page .= t('STATUS_6');
	$page .= '</th>';
	
	$page .= '<th width="17%">';
	$page .= t('STATUS_3');
	$page .= '</th>';
	
	$page .= '<th width="17%">';
	$page .= t('STATUS_4');
	$page .= '</th>';
	
	$page .= '<th width="17%">';
	$page .= t('STATUS_5');
	$page .= '</th>';
	
	$page .= '</thead>';
	$page .= '</tr>';
	
	$page .= '<tbody>';
	$page .= '<tr>';
	
	$page .= '<td>';
	$page .=  plaatscrum_status_colom_part(STATUS_TODO);
	$page .= '</td>';

	$page .= '<td>';
	$page .=  plaatscrum_status_colom_part(STATUS_DOING);
	$page .= '</td>';
	
	$page .= '<td>';
	$page .=  plaatscrum_status_colom_part(STATUS_REVIEW);
	$page .= '</td>';
	
	$page .= '<td>';
	$page .=  plaatscrum_status_colom_part(STATUS_DONE);
	$page .= '</td>';
	
	$page .= '<td>';
	$page .=  plaatscrum_status_colom_part(STATUS_SKIPPED);
	$page .= '</td>';
	
	$page .= '<td>';
	$page .=  plaatscrum_status_colom_part(STATUS_ONHOLD);
	$page .= '</td>';
		
	$page .= '</tr>';
	$page .= '</tbody>';
	
	$page .= '</table>';
		
	$page .=  '<br/>';
}

/*
** ------------------
** HANDLER
** ------------------
*/

/**
 * board handler 
 */
function plaatscrum_storyboard() {
	
	/* input */
	global $mid;
	global $pid;
	global $eid;
		
	/* Page handler */
	switch ($pid) {
		
		case PAGE_STATUSBOARD: 	
				  plaatscrum_link_store($mid, $pid);
				  plaatscrum_filter();
				  plaatscrum_storyboard_form();
				  break;
	}
}

/*
** ------------------
** The End
** ------------------
*/

?>