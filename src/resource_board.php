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

function plaatscrum_resourceboard_ticket($user_id, $status) {

	/* output */
	global $page;
	global $user;
	global $access;
	
	$query  = 'select a.story_id, a.type, a.number, a.summary, b.number as sprint_number, a.points, a.status, a.user_id, c.name ';
	$query .= 'from story a left join sprint b on b.sprint_id=a.sprint_id left join tuser c on a.user_id=c.user_id ';
	$query .= 'where a.deleted=0 and a.status='.$status.' and a.user_id='.$user_id.' and a.project_id='.$user->project_id.' ';	
	$query .= 'and a.type!='.TYPE_STORY.' ';
	
	if ($user->sprint_id>0) {
		$query .= 'and a.sprint_id='.$user->sprint_id.' ';	
	}
	
	$result = plaatscrum_db_query($query);
		
	while ($data=plaatscrum_db_fetch_object($result)) {		
		
		$page .= plaatscrum_board_element($data);
				
	}
}

function plaatscrum_resourceboard_form() {

	/* input */
	global $mid;
	global $sid;
	global $user;
	global $access;
	
	/* output */
	global $page;
	global $title;
				
	$title = t('RESOURCEBOARD_TITLE');
				
	$page .= '<h1>';
	$page .= $title;
	$page .= '</h1>';
	
	$page .= '<div id="taskboard">';

	$page .= t('RESOURCEBOARD_NOTE');

	$page .= '<table>';
	$page .= '<thead>';
	$page .= '<tr>';
	
	$page .= '<th width="15%">';
	$page .= t('GENERAL_RESOURCE');
	$page .= '</th>';
	
	$page .= '<th width="15%">';
	$page .= t('STATUS_1');
	$page .= '</th>';

	$page .= '<th width="15%">';
	$page .= t('STATUS_2');
	$page .= '</th>';
	
	$page .= '<th width="15%">';
	$page .= t('STATUS_6');
	$page .= '</th>';
	
	$page .= '<th width="15%">';
	$page .= t('STATUS_3');
	$page .= '</th>';
	
	$page .= '<th width="15%">';
	$page .= t('STATUS_4');
	$page .= '</th>';
	
	$page .= '<th width="15%">';
	$page .= t('STATUS_5');
	$page .= '</th>';
		
	$page .= '</tr>';
	$page .= '</thead>';
	$page .= '<tbody>';
	
	$query  = 'select a.user_id, b.name from story a ';
	$query .= 'left join tuser b on a.user_id=b.user_id ';
	$query .= 'where a.deleted=0 and a.project_id='.$user->project_id.' ';	
	
	if ($user->sprint_id>0) {
		$query .= 'and a.sprint_id='.$user->sprint_id.' ';	
	}
	
	$query .= 'group by a.user_id order by b.name';

	$result = plaatscrum_db_query($query);
		
	while ($data=plaatscrum_db_fetch_object($result)) {		
	
		$page .= '<tr>';
	
		$page .= '<td>';
		$page .= $data->name;
		$page .= '</td>';
		
		$page .= '<td>';
		$page .= plaatscrum_resourceboard_ticket($data->user_id, STATUS_TODO);
		$page .= '</td>';
		
		$page .= '<td>';
		$page .= plaatscrum_resourceboard_ticket($data->user_id, STATUS_DOING);
		$page .= '</td>';
		
		$page .= '<td>';
		$page .= plaatscrum_resourceboard_ticket($data->user_id, STATUS_REVIEW);
		$page .= '</td>';
		
		$page .= '<td>';
		$page .= plaatscrum_resourceboard_ticket($data->user_id, STATUS_DONE);
		$page .= '</td>';
		
		$page .= '<td>';
		$page .= plaatscrum_resourceboard_ticket($data->user_id, STATUS_SKIPPED);
		$page .= '</td>';
		
		$page .= '<td>';
		$page .= plaatscrum_resourceboard_ticket($data->user_id, STATUS_ONHOLD);
		$page .= '</td>';
				
		$page .= '</tr>';
	}	
	
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
function plaatscrum_resourceboard() {
	
	/* input */
	global $mid;
	global $sid;
	global $eid;
	
	/* Page handler */
	switch ($sid) {
						  
		case PAGE_RESOURCEBOARD: 	
				  plaatscrum_link_store($mid, $sid);
				  plaatscrum_filter();
				  plaatscrum_resourceboard_form();
				  break;
	}
}

/*
** ------------------
** The End
** ------------------
*/

?>