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
function plaatscrum_costboard_form() {

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

/*
** ------------------
** HANDLER
** ------------------
*/

/**
 * board handler 
 */
function plaatscrum_costboard() {
	
	/* input */
	global $mid;
	global $pid;
	global $eid;
	
	/* Page handler */
	switch ($pid) {

		case PAGE_COST: 					 
				  plaatscrum_filter();
				  plaatscrum_costboard_form();
				  break;
	}
}

/*
** ------------------
** The End
** ------------------
*/

?>
