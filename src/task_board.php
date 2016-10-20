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
 * Create taskboard colomn 
 */
function plaatscrum_taskboard_ticket($id) {

	/* input */	
	global $filter_project;
	global $filter_sprint;
	global $filter_status;
	global $filter_prio;
	global $filter_type;
	global $filter_owner;
	
	$tmp1='';
	$tmp2='';
	$tmp3='';
	$tmp4='';
	$tmp5='';
	$tmp6='';
		
	$query  = 'select a.story_id, a.type, a.number, a.summary, b.number as sprint_number, a.points, a.status, a.user_id, c.name ';
	$query .= 'from story a left join sprint b on b.sprint_id=a.sprint_id left join tuser c on a.user_id=c.user_id ';
	$query .= 'left join project d on a.project_id=d.project_id where a.deleted=0 and a.story_story_id='.$id.' ';	
	$query .= 'and a.type!='.TYPE_STORY.' ';
	
	if ($filter_owner > 0) {
		$query .= 'and c.user_id='.$filter_owner.' ';	
	}	
	
	$query .= 'order by a.number';
	
	$result = plaatscrum_db_query($query);
		
	$col =0;
	while ($data=plaatscrum_db_fetch_object($result)) {		
		
		switch ($data->status) {
		
			case STATUS_TODO: 			
					$tmp1 .= plaatscrum_board_element($data);
					break;
					
			case STATUS_DOING: 			
					$tmp2 .= plaatscrum_board_element($data);
					break;
					
			case STATUS_REVIEW: 			
					$tmp3 .= plaatscrum_board_element($data);
					break;

			case STATUS_DONE: 			
					$tmp4 .= plaatscrum_board_element($data);
					break;				
					
			case STATUS_SKIPPED: 			
					$tmp5 .= plaatscrum_board_element($data);
					break;
					
			case STATUS_ONHOLD: 			
					$tmp6 .= plaatscrum_board_element($data);
					break;
		}
	}
		
	$page  = '<td>';
	$page .= $tmp1;
	$page .= '</td>';
		
	$page .= '<td>';
	$page .= $tmp2;
	$page .= '</td>';
		
	$page .= '<td>';
	$page .= $tmp3;
	$page .= '</td>';
		
	$page .= '<td>';
	$page .= $tmp4;
	$page .= '</td>';
		
	$page .= '<td>';
	$page .= $tmp5;
	$page .= '</td>';
		
	$page .= '<td>';
	$page .= $tmp6;
	$page .= '</td>';

	return $page;
}


/**
 * Create taskboard form 
 */
function plaatscrum_taskboard_form() {

	/* input */
	global $mid;
	global $pid;
	global $sort;
	
	global $filter_project;
	global $filter_sprint;
	global $filter_status;
	global $filter_prio;
	global $filter_type;
	global $filter_owner;

	/* output */
	global $page;
	global $title;
				
	$title = t('TASKBOARD_TITLE');

	$query  = 'select a.story_id, a.type, a.number, a.summary, b.number as sprint_number, a.points, a.status, a.user_id, c.name ';
	$query .= 'from story a left join sprint b on b.sprint_id=a.sprint_id left join tuser c on a.user_id=c.user_id ';
	$query .= 'left join project d on a.project_id=d.project_id where a.deleted=0 and a.type='.TYPE_STORY.' ';	
		
	if ($filter_project>0) {
		$query .= 'and a.project_id='.$filter_project.' ';	
	}
	
	if ($filter_owner>0) {
		$query .= 'and c.user_id='.$filter_owner.' ';	
	}	
	
	if ($filter_sprint>0) {
		$query .= 'and a.sprint_id='.$filter_sprint.' ';	
	}

	switch ($sort) {

		default: 
			$query .= 'order by b.number, a.number asc';
			break;
			
		case 1: 
			$query .= 'order by b.number, a.number desc';
			break;
	}

	$result = plaatscrum_db_query($query);
	
	$page .= '<h1>';
	$page .= $title;
	$page .= '</h1>';
	
	$page .= '<div id="taskboard">';

	$page .= t('TASKBOARD_NOTE');

	$page .= '<table>';
	$page .= '<thead>';
	$page .= '<tr>';
	
	$page .= '<th width="14%">';
	if ($sort==0) {
		$sort=1;
	} else {
		$sort=0;
	}
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort='.$sort, t('GENERAL_STORY'));
	$page .= '</th>';
	
	$page .= '<th width="14%">';
	$page .= t('STATUS_1');
	$page .= '</th>';

	$page .= '<th width="14%">';
	$page .= t('STATUS_2');
	$page .= '</th>';
	
	$page .= '<th width="14%">';
	$page .= t('STATUS_6');
	$page .= '</th>';
	
	$page .= '<th width="14%">';
	$page .= t('STATUS_3');
	$page .= '</th>';
	
	$page .= '<th width="14%">';
	$page .= t('STATUS_4');
	$page .= '</th>';
	
	$page .= '<th width="14%">';
	$page .= t('STATUS_5');
	$page .= '</th>';
		
	$page .= '</tr>';
	$page .= '</thead>';
	$page .= '<tbody>';
		
	while ($data=plaatscrum_db_fetch_object($result)) {		
	
		$page .= '<tr>';
	
		$page .= '<td>';
		$page .= plaatscrum_board_element($data);
		$page .= '</td>';
		
		$page .= plaatscrum_taskboard_ticket($data->story_id);
		
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
function plaatscrum_taskboard() {
	
	/* input */
	global $mid;
	global $pid;
	global $eid;
	
	/* Page handler */
	switch ($pid) {
						  
		case PAGE_TASKBOARD: 	
				  plaatscrum_link_store($mid, $pid);
				  plaatscrum_filter();
				  plaatscrum_taskboard_form();
				  break;

	}
}

/*
** ------------------
** The End
** ------------------
*/

?>