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

function plaatscrum_delete_event() {

	global $id;
	
	plaatscrum_db_log_user_delete($id);
	
	$id=0;
}

/*
** ------------------
** UI
** ------------------
*/


function plaatscrum_event_form() {

	/* input */
	global $mid;
	global $pid;
	global $id;
	
	/* output */
	global $page;
	global $title;
				
	$title = t('EVENT_TITLE');	
			
	$page .= '<h1>';	
	$page .= $title;
	$page .= '</h1>';

	$page .= '<p>';
	$page .= t('EVENT_CONTENT');
	$page .= '</p>';
	
	$page .= '<div class="fl_left">';
	$page .= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&eid='.EVENT_PREV.'&id='.$id, t('LINK_PREV'));
	$page .= '</div>';
	
	$page .= '<div class="fl_right">';
	$page .= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&eid='.EVENT_NEXT.'&id='.$id, t('LINK_NEXT'));
	$page .= '</div>';
		
	$page .= '<table>';
			
	$page .= '<thead>';
	$page .= '<tr>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_TIMESTAMP');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_NAME');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_CATEGORY');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_DESCRIPTION');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_ACTION');
	$page .= '</th>';
	
	$page .= '</tr>';
	
	$page .= '</thead>';
	$page .= '<tbody>';
		
	$query  = 'select a.log_id,  a.timestamp, b.name, a.address, a.category, a.description from log a ';
	$query .= 'left join tuser b on a.user_id=b.user_id ';
	$query .= 'order by a.log_id desc ';
	$query .= 'limit '.($id*20).',20 ';
	$result = plaatscrum_db_query($query);
	
	$count = 0;
	while ($data=plaatscrum_db_fetch_object($result)) {	
			
		$page .= '<tr ';		
		if ((++$count % 2 ) == 1 ) {
			$page .= 'class="light" ';
		} else {
			$page .= 'class="dark" ';
		}				
		$page .='>';

		$page .= '<td width="18%">';
		$page .= convert_datetime_php($data->timestamp);
		$page .= '</td>';
		
		$page .= '<td>';
		$page .= $data->name;
		$page .= '</td>';
				
		$page .= '<td>';
		$page .= strtolower(t('CATEGORY_'.$data->category));
		$page .= '</td>';
			
		$page .= '<td>';
		$page .=  $data->description;
		$page .= '</td>';
		
		$page .= '<td>';
		$page .= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&eid='.EVENT_DELETE.'&id='.$data->log_id, t('LINK_DELETE'));
		$page .= '</td>';
	
		$page .= '</tr>';
	}
	
	$page .= '</tbody>';
	$page .= '</table>';		
}

/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_event() {

	/* input */
	global $id;
	global $pid;
	global $eid;
	
	/* Event handler */
	switch ($eid) {
	
		case EVENT_DELETE:
					plaatscrum_delete_event();
					break;
	
		case EVENT_NEXT:				
					$id--;
					if ($id<0) {
						$id=0;
					}
					break;

		case EVENT_PREV;
					$id++;
					break;	
	}
	
	/* Page handler */
	switch ($pid) {
	
		case PAGE_EVENT: 
					plaatscrum_event_form();	
					break;
	}
}


/*
** ------------------
** The End
** ------------------
*/

