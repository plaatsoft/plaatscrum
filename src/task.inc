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

function scrumboard_tasklist($story_id) {

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
	$query .= 'from story a left join user b on a.user_id=b.user_id ';
	$query .= 'where a.deleted=0 and a.type in ('.TYPE_TASK.','.TYPE_BUG.','.TYPE_EPIC.') and a.story_story_id='.$story_id.' ';
	$query .= 'order by a.number';	
		
	$result = scrumboard_db_get($query);
				
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
	while ($data=mysql_fetch_object($result)) {		
				
		$page .= '<tr ';
		
		if ((++$count % 2 ) == 1 ) {
			$page .= 'class="light" ';
		} else {
			$page .= 'class="dark" ';
		} 
		$page .='>';
		
		$page .= '<td>';	
		$page .= scrumboard_link('mid='.$mid.'&sid='.PAGE_STORY.'&eid='.EVENT_STORY_LOAD.'&id='.$data->story_id, $data->number);
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
		$page .= scrumboard_link('mid='.$mid.'&sid='.PAGE_STORY.'&eid='.EVENT_STORY_LOAD.'&id='.$data->story_id, t('LINK_VIEW'));
		$page .= '</td>';
		
		$page .= '</tr>';
	}
	$page .= '</tbody>';
	$page .= '</table>';	
	
	$page .= '<p>';		
	
	if ($access->story_add) {
		$data = scrumboard_db_story($story_id);
		$page .= scrumboard_link('mid='.$mid.'&sid='.PAGE_STORY.'&eid='.EVENT_NEW.'&type='.TYPE_TASK.'&story_id_ref='.$data->number, t('LINK_ADD_TASK'));
		$page .= '&nbsp;&nbsp;&nbsp;';
		$page .= scrumboard_link('mid='.$mid.'&sid='.PAGE_STORY.'&eid='.EVENT_NEW.'&type='.TYPE_BUG.'&story_id_ref='.$data->number, t('LINK_ADD_BUG'));
		$page .= '&nbsp;&nbsp;&nbsp;';		
		$page .= scrumboard_link('mid='.$mid.'&sid='.PAGE_STORY.'&eid='.EVENT_NEW.'&type='.TYPE_EPIC.'&story_id_ref='.$data->number, t('LINK_ADD_EPIC'));		
	}
	$page .= '</p>';	
}

/*
** ------------------
** HANDLER
** ------------------
*/


/*
** ------------------
** The End
** ------------------
*/
