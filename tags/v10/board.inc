<?php

/* 
**  ==========
**  plaatscrum
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
**  All copyrights reserved (c) 2008-2013 PlaatSoft
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
 * Get user hours of user in sprint, project and status
 */
function plaatscrum_cost_element($sprints, $user_id, $status) {

	/* input */
	global $user;
	
	$query  = 'select sum(a.points) as total from story a ';
	$query .= 'where a.deleted=0 and a.project_id='.$user->project_id.' ';
	$query .= 'and a.sprint_id in ('.$sprints.') and a.type in ('.TYPE_TASK.','.TYPE_BUG.','.TYPE_EPIC.') ';	
	$query .= 'and a.user_id= '.$user_id.' '; 
	$query .= 'and a.status = '.$status;

	$total = 0;
	
	$result = plaatscrum_db_query($query);
	
	$data = plaatscrum_db_fetch_object($result);	
	if ($data) {
		$total = $data->total;
	}
	
	return $total;
}


/** 
 * Cost board form
 */
function plaatscrum_cost_form() {

	/* input */
	global $user;

	/* output */
	global $page;
	global $title;
				
	$title = t('COST_TITLE');
				
	$page .= '<h1>';
	$page .= $title;
	$page .= '</h1>';
	
	if ($user->sprint_id==0) {
		$query1 = 'select sprint_id from sprint where project_id='.$user->project_id;
		$result1 = plaatscrum_db_query($query1);
		$tmp = "";
		while ($data1 = plaatscrum_db_fetch_object($result1)) {	
			if (strlen($tmp)>0) {
				$tmp .= ',';
			}
			$tmp .= $data1->sprint_id;
		}
	} else {
		$tmp = $user->sprint_id;
	}
			
	$page .= t('COST_NOTE');
		
	$query2  = 'select a.user_id, b.name, sum(a.points) as total, c.bcr from story a ';
	$query2 .= 'left join tuser b on a.user_id=b.user_id ';
	$query2 .= 'left join project_user c on a.user_id=c.user_id ';
	$query2 .= 'where a.deleted=0 and c.project_id='.$user->project_id.' ';
	$query2 .= 'and a.sprint_id in ('.$tmp.') and a.type in ('.TYPE_TASK.','.TYPE_BUG.','.TYPE_EPIC.') ';	
	$query2 .= 'group by a.user_id order by b.name';
		
	$result2 = plaatscrum_db_query($query2);
		
	$page .= '<table>';
	
	$page .= '<thead>';
	$page .= '<tr>';
			
	$page .= '<th>';
	$page .= t('GENERAL_RESOURCE');
	$page .= '</th>';
	
	$page .= '<th>';
	$page .= t('GENERAL_TODO_HOURS');
	$page .= '</th>';
	
	$page .= '<th>';
	$page .= t('GENERAL_DOING_HOURS');
	$page .= '</th>';

	$page .= '<th>';
	$page .= t('GENERAL_REVIEW_HOURS');
	$page .= '</th>';
	
	$page .= '<th>';
	$page .= t('GENERAL_DONE_HOURS');
	$page .= '</th>';
	
	$page .= '<th>';
	$page .= t('GENERAL_SKIPPED_HOURS');
	$page .= '</th>';
		
	$page .= '<th>';
	$page .= t('GENERAL_ONHOLD_HOURS');
	$page .= '</th>';
	
	$page .= '<th>';
	$page .= t('GENERAL_TOTAL_HOURS');
	$page .= '</th>';
			
	$page .= '<th>';
	$page .= t('GENERAL_BCR');
	$page .= '</th>';
		
	$page .= '<th>';
	$page .= t('GENERAL_TOTAL_COST');
	$page .= '</th>';
			
	$page .= '</tr>';
			
	$page .= '</thead>';
	$page .= '<tbody>';
		
	$count = 0;	
	$total1 = 0;
	$total2 = 0;
	$total3 = 0;
	$total4 = 0;
	$total5 = 0;
	$total6 = 0;
	$total7 = 0;
	$total10 = 0;
	
	while ($data2 = plaatscrum_db_fetch_object($result2)) {	
		
		$page .= '<tr ';
		if ((++$count % 2 ) == 1 ) {
			$page .= 'class="light" ';
		} else {
			$page .= 'class="dark" ';
		} 
		$page .='>';
		
		$page .= '<td>';
		$page .= $data2->name;
		$page .= '</td>';
		
		$page .= '<td>';
		$total = plaatscrum_cost_element($tmp, $data2->user_id, STATUS_TODO);
		$total1 += $total;
		$page .= round($total,2);
		$page .= '</td>';
		
		$page .= '<td>';
		$total = plaatscrum_cost_element($tmp, $data2->user_id, STATUS_DOING);
		$total2 += $total;
		$page .= round($total,2);
		$page .= '</td>';
		
		$page .= '<td>';
		$total = plaatscrum_cost_element($tmp, $data2->user_id, STATUS_REVIEW);
		$total3 += $total;
		$page .= round($total,2);
		$page .= '</td>';
		
		$page .= '<td>';
		$total = plaatscrum_cost_element($tmp, $data2->user_id, STATUS_DONE);
		$total4 += $total;
		$page .= round($total,2);
		$page .= '</td>';
		
		$page .= '<td>';
		$total = plaatscrum_cost_element($tmp, $data2->user_id, STATUS_SKIPPED);
		$total5 += $total;
		$page .= round($total,2);
		$page .= '</td>';
		
		$page .= '<td>';
		$total = plaatscrum_cost_element($tmp, $data2->user_id, STATUS_ONHOLD);
		$total6 += $total;
		$page .= round($total,2);
		$page .= '</td>';
		
		$total7 += $data2->total;
		$page .= '<td>';
		$page .= round($data2->total,2);
		$page .= '</td>';
			
		$page .= '<td>';
		$page .= '&#8364; '.$data2->bcr;
		$page .= '</td>';
			
		$sum = $data2->total * $data2->bcr;
		$total10 += $sum;
		$page .= '<td>';
		$page .= '&#8364; '.number_format(round($sum,2),0,",",".");
		$page .= '</td>';
			
		$page .= '</tr>';
	}
		
	$page .= '<tr ';
	if ((++$count % 2 ) == 1 ) {
		$page .= 'class="light" ';
	} else {
		$page .= 'class="dark" ';
	} 
	$page .='>';
			
	$page .= '<td>';
	$page .= '<b>'.t('GENERAL_TOTAL').'</b>';
	$page .= '</td>';

	$page .= '<td>';
	$page .= '<b>'.round($total1, 2).'</b>';
	$page .= '</td>';
	
	$page .= '<td>';
	$page .= '<b>'.round($total2, 2).'</b>';
	$page .= '</td>';
					
	$page .= '<td>';
	$page .= '<b>'.round($total3, 2).'</b>';
	$page .= '</td>';
	
	$page .= '<td>';
	$page .= '<b>'.round($total4, 2).'</b>';
	$page .= '</td>';
			
	$page .= '<td>';
	$page .= '<b>'.round($total5, 2).'</b>';
	$page .= '</td>';
			
	$page .= '<td>';
	$page .= '<b>'.round($total6, 2).'</b>';
	$page .= '</td>';
	
	$page .= '<td>';
	$page .= '<b>'.round($total7, 2).'</b>';
	$page .= '</td>';
			
			
	$page .= '<td>';
	$page .= '</td>';

	$page .= '<td>';
	$page .= '<b>	&#8364; '.number_format(round($total10, 2),0,",",".").'</b>';
	$page .= '</td>';
			
	$page .= '</tr>';
			
	$page .= '</tbody>';
	$page .= '</table>';
		
	$page .= '<br/>';
}


/* ----------------------------*/

/** 
 * Create story/task/bug/epic record for board
 */
function plaatscrum_board_element($data) {

	/* input */
	global $mid;

	/* output */
	global $access;
	
	$page = '<div class="'.strtolower( t('TYPE_'.$data->type)).'">';
		
	$page .= '<table>';
		
	$page .= '<tr>';
		
	$page .= '<td>';
	$page	.= '<label title="'.t('GENERAL_US').'">'.$data->number.'</label>';
	$page .= '</td>';
	$page .= '<td align="right">';
	$page	.= '<label title="'.t('GENERAL_POINTS').'">'.$data->points.'</label>';
	$page .= '</td>';
		
	$page .= '</tr>';
		
	$page .= '<tr>';

	$page .= '<td colspan="2">';
	$page .= plaatscrum_link_hidden('mid='.$mid.'&sid='.PAGE_STORY.'&eid='.EVENT_STORY_LOAD.'&id='.$data->story_id, $data->summary);
				
	$page .= '</td>';

	$page .= '</tr>';
		
	$page .= '<tr>';
		
	$page .= '<td>';
	$page	.= '<label title="'.t('GENERAL_SPRINT').'">'.$data->sprint_number.'</label>';
	$page .= '</td>';
		
	$page .= '<td align="right">';
	$page	.= '<label title="'.t('GENERAL_OWNER').'">'.$data->name.'</label>';
	$page .= '</td>';
		
	$page .= '</tr>';
		
	$page .= '</table>';
	$page .= '</div>';
	
	return $page;
}

/* ----------------------------*/

/** 
 * Create resource board colomn
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

/** 
 * Create resource board form
 */
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

/* ----------------------------*/

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
	global $sid;
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

/* ----------------------------*/

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
function plaatscrum_statusboard_form() {

	/* input */
	global $mid;
	global $sid;
	
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
function plaatscrum_board() {
	
	/* input */
	global $mid;
	global $sid;
	global $eid;
	
	/* Event handler */
	plaatscrum_story_events();
	
	/* Page handler */
	switch ($sid) {
		
		case PAGE_STATUSBOARD: 	
				  plaatscrum_link_store($mid, $sid);
				  plaatscrum_filter();
				  plaatscrum_statusboard_form();
				  break;
				  
		case PAGE_RESOURCEBOARD: 	
				  plaatscrum_link_store($mid, $sid);
				  plaatscrum_filter();
				  plaatscrum_resourceboard_form();
				  break;
				  
		case PAGE_TASKBOARD: 	
				  plaatscrum_link_store($mid, $sid);
				  plaatscrum_filter();
				  plaatscrum_taskboard_form();
				  break;
				 
		case PAGE_COST: 					 
				  plaatscrum_filter();
				  plaatscrum_cost_form();
				  break;
				  
		case PAGE_STORY: 
					plaatscrum_story_form();	
					break;	
	}
}