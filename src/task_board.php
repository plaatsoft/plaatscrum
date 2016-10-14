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

	/* output */	
	global $user;
	global $access;
	
	$tmp1='';
	$tmp2='';
	$tmp3='';
	$tmp4='';
	$tmp5='';
	$tmp6='';
		
	$query  = 'select a.story_id, a.type, a.number, a.summary, b.number as sprint_number, a.points, a.status, a.user_id, c.name ';
	$query .= 'from story a left join sprint b on b.sprint_id=a.sprint_id left join tuser c on a.user_id=c.user_id ';
	$query .= 'left join project d on a.project_id=d.project_id where a.deleted=0 and a.story_story_id='.$id.' ';	
	$query .= 'and a.type!='.TYPE_STORY.' order by a.number';
	
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
	global $user;
	global $access;
	
	/* output */
	global $page;
	global $title;
				
	$title = t('TASKBOARD_TITLE');
				
	$page .= '<h1>';
	$page .= $title;
	$page .= '</h1>';
	
	$page .= '<div id="taskboard">';

	$page .= t('TASKBOARD_NOTE');

	$page .= '<table>';
	$page .= '<thead>';
	$page .= '<tr>';
	
	$page .= '<th width="15%">';
	$page .= t('GENERAL_STORY');
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
	
	$query  = 'select a.story_id, a.type, a.number, a.summary, b.number as sprint_number, a.points, a.status, a.user_id, c.name ';
	$query .= 'from story a left join sprint b on b.sprint_id=a.sprint_id left join tuser c on a.user_id=c.user_id ';
	$query .= 'left join project d on a.project_id=d.project_id where a.deleted=0 and a.type='.TYPE_STORY.' ';	
	$query .= 'and a.project_id='.$user->project_id.' ';	
	
	if ($user->sprint_id>0) {
		$query .= 'and a.sprint_id='.$user->sprint_id.' ';	
	}
	
	$query .= 'order by b.number, a.number';

	$result = plaatscrum_db_query($query);
		
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