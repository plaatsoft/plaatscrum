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
 * Back log form 
 */
function plaatscrum_backlog_form() {

	/* input */
	global $mid;
	global $pid;
	global $user;
	global $access;
	global $sprint;
	global $search;
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
	
	$title = t('BACKLOG_TITLE');
					
	$page .= '<h1>';	
	$page .= $title;
	$page .= '</h1>';
		
	$page .= '<p>';
	$page .= t('BACKLOG_NOTE');
	$page .= '</p>';
	
	$query  = 'select a.story_id, a.type, a.number, a.summary, a.sprint_id, a.story_story_id, a.prio, d.number as sprint_number, d.locked, ';
	$query .= 'a.points, a.status, a.user_id, c.name, c.user_id, ';
	$query .= 'if(a.story_story_id=0,a.story_id, a.story_story_id) as sort2 ';
	$query .= 'from story a left join project b on a.project_id=b.project_id left join tuser c on a.user_id=c.user_id ';
	$query .= 'left join sprint d on d.sprint_id=a.sprint_id where a.deleted=0 and d.deleted=0 ';
		
	if ($filter_project>0) {
		$query .= 'and a.project_id='.$filter_project.' ';	
	}
	
	if ($filter_sprint>0) {
		$query .= 'and a.sprint_id='.$filter_sprint.' ';	
	}
	
	if ($filter_owner>0) {
		$query .= 'and c.user_id='.$filter_owner.' ';	
	}
	
	if (strlen($filter_status)>0) {
		$query .= 'and a.status in ('.$filter_status.') ';
	}
	
	if ($filter_prio > 0) {
		$query .= 'and a.prio in ('.$filter_prio.') ';	
	}	
	
	if (strlen($filter_type) > 0) {
		$query .= 'and a.type in ('.$filter_type.') ';	
	} 
	
	if ((strlen($search)>0) && ($search!=t('HELP'))) {
		$query .= 'and a.summary LIKE "%'.$search.'%" ';	
	}

	switch ($sort) {
	    
		 default:$query .= 'order by sort2 asc, a.story_id';
				   break;
	
		 case 1: $query .= 'order by a.number';
				   break;
					
	    case 2: $query .= 'order by sprint_number, a.number';
				   break;					

	    case 3: $query .= 'order by a.points desc';
				   break;
					
		 case 4: $query .= 'order by a.type';
				   break;
					
		 case 5: $query .= 'order by a.status';
				   break;
					
		 case 6: $query .= 'order by a.prio desc';
				   break;
					
		 case 7: $query .= 'order by a.user_id';
				   break;
	}
		
	$result = plaatscrum_db_query($query);
							
	$page .= '<p>';
	$page .= '</p>';
				
	$page .= '<table>';
			
	$page .= '<thead>';
	$page .= '<tr>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=1', t('GENERAL_US'));
	$page .= '</th>';
		
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=0', t('GENERAL_SUMMARY'));	
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=2', t('GENERAL_SPRINT'));
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=3', t('GENERAL_SP_WORK'));
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=4', t('GENERAL_TYPE'));
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=5', t('GENERAL_PRIORITY'));
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=6', t('GENERAL_STATUS'));
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&sort=7', t('GENERAL_OWNER'));
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
			if ($data->type==TYPE_STORY) {
				$page .= 'class="light bold" ';
			} else {
				$page .= 'class="light" ';
			}
		} else {
			if ($data->type==TYPE_STORY) {
				$page .= 'class="dark bold" ';
			} else {
				$page .= 'class="dark" ';
			}
		} 
		$page .='>';
		
		$page .= '<td>';	
		$page .= plaatscrum_link('mid='.$mid.'&pid='.PAGE_STORY.'&eid='.EVENT_STORY_LOAD.'&id='.$data->story_id, $data->number);
		$page .= '</td>';
		
		$page .= '<td>';		
		if ($data->story_story_id>0) {
			$page .= plaatscrum_ui_image("link.png",' width="14" heigth="14" ').' ';
		}				
		$page	.= $data->summary;		
		$page .= '</td>';
		
		$page .= '<td>';
		$page	.= $data->sprint_number;
		$page .= '</td>';

		$page .= '<td>';		
		$page	.= $data->points;
		$page .= '</td>';
		
		$page .= '<td>';
		$page	.= t('TYPE_'.$data->type);
		$page .= '</td>';
		
		$page .= '<td >';
		$page	.= t('PRIO_'.$data->prio);
		$page .= '</td>';
		
		$page .= '<td>';
		$page	.= t('STATUS_'.$data->status);
		$page .= '</td>';
				
		$page .= '<td >';
		$page	.= $data->name;
		$page .= '</td>';
		
		$page .= '<td >';

		if (($access->story_edit) && (!isset($data->user_id)) && ($data->locked==0)) {
			$page .= plaatscrum_link('mid='.$mid.'&pid='.PAGE_BACKLOG_FORM.'&eid='.EVENT_STORY_ASSIGN.'&id='.$data->story_id, t('LINK_ASSIGN'));
		} 

		$page .= '</td>';
		
		$page .= '</tr>';
	}
	$page .= '</tbody>';
	$page .= '</table>';	
	
	$page .= '<p>';		
	
	if ($access->story_add) {
		$page .= plaatscrum_link('mid='.$mid.'&pid='.PAGE_STORY.'&eid='.EVENT_STORY_NEW.'&type='.TYPE_STORY.'&id=0', t('LINK_ADD_STORY'));
	}
	$page .= '</p>';	
}

/*
** ------------------
** HANDLERS
** ------------------
*/

/** 
 * backlog handler 
 */
function plaatscrum_backlog() {

	/* input */
	global $mid;
	global $pid;
		
	/* Page handler */
	switch ($pid) {
	
		case PAGE_BACKLOG_FORM: 
					plaatscrum_link_store($mid, $pid);
					plaatscrum_filter();
					plaatscrum_backlog_form();	
					break;
	}
}


/*
** ------------------
** The End
** ------------------
*/
