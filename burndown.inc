<?php


/* 
**  ==========
**  scrumboard
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
**  All copyrights reserved (c) 2008-2012 PlaatSoft
*/

/*
** ------------------
** POST PARAMETERS
** ------------------
*/

$filter_project = scrumboard_post("filter_project", "");
$filter_sprint = scrumboard_post("filter_sprint", 0);

/*
** ------------------
** ACTIONS
** ------------------
*/

function scrumboard_burndown_filter_do() {

	/* input */	
	global $filter_project;
	global $filter_sprint;
	
	/* output */
	global $user;
		
	$user->project_id = $filter_project;
	$user->sprint_id = $filter_sprint;
	
	scrumboard_db_user_update($user);	
}

/*
** ------------------
** UI
** ------------------
*/

function scrumboard_burndown_filter() {

	/* input */
	global $mid;
	global $sid;
	global $sort;

	/* output */
	global $page;
	global $user;
	
	$page .= '<div id="filter">';
	
	$page	.= t('GENERAL_PROJECT').': ';
	$page .= scrumboard_ui_project('filter_project', $user->project_id, false);		
	$page .= ' ';
	
	$page	.= t('GENERAL_SPRINT').': ';
	$page .= scrumboard_ui_sprint('filter_sprint', $user->sprint_id, false, true, false);		
	$page .= ' ';
		
	$page .= scrumboard_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_FILTER.'&sort='.$sort, t('LINK_FILTER'));
	
	$page .= '</div>';
}

function scrumboard_burndown_status( $status ) {

	global $user;
	
	$total =0;
	
	$query = 'select sum(points) as total from story where project_id='.$user->project_id.' ';
	if ($user->sprint_id>0) {
		$query .= 'and sprint_id='.$user->sprint_id.' ';
	}	
	if ($status>0) {
		$query .= 'and status='.$status;
	}
		
	$result = scrumboard_db_query($query);
	if ($data=scrumboard_db_fetch_object($result))	{
		$total = $data->total;
	}
		
	return $total;
}
	
function scrumboard_burndown_form() {
	
	/* input */
	global $user;
	global $access;
		
	/* output */
	global $page;

	$page .= '<h1>';
	$page .= t('CHART_BURNDOWN_TITLE');
	$page .= '</h1>';
		
	$data = array();
	
	$data = array( 'ToDo' => scrumboard_burndown_status( STATUS_TODO ),
						'Doing' => scrumboard_burndown_status( STATUS_DOING ),
						'Done' => scrumboard_burndown_status( STATUS_DONE ),
						'Skipped'  => scrumboard_burndown_status( STATUS_SKIPPED ),
						'OnHold' => scrumboard_burndown_status( STATUS_ONHOLD ), 
						'Alles' => scrumboard_burndown_status( STATUS_ALL ) );
	
	$graph = new PHPGraphLib(950,400, 'images/graph.png');
	$graph->addData($data);

	$graph->setBars(true);
	$graph->setLine(false);
	$graph->setDataPoints(true);
	$graph->setDataValues(true);
	$graph->setLegend(false);
	$graph->setGrid(true);
	
	$graph->setLegendOutlineColor('yellow');
	$graph->setBackgroundColor("white");
	$graph->setGridColor('gray');
	$graph->setBarColor('255,255,204');
	$graph->setupXAxis(12, 'gray');
	$graph->setupYAxis(12, 'gray');
	$graph->setTextColor('gray');
	$graph->setLineColor('red');
	
	$graph->setDataValueColor('red');
	$graph->setDataPointColor('red');
	
	$graph->setGradient('silver', 'gray');
		
	$graph->createGraph();
	
	$page .= '<img src="images/graph.png" alt="" />';
}

/*
** ------------------
** HANDLER
** ------------------
*/

function scrumboard_chart() {
	
	/* input */
	global $eid;
	global $sid;
	
	/* Event handler */
	switch ($eid) {
	
		case EVENT_FILTER:
					scrumboard_burndown_filter_do();
					break;
					
	}
	
	/* Page handler */
	switch ($sid) {
		
		case PAGE_BURNDOWN: 	
				  scrumboard_burndown_filter();
				  scrumboard_burndown_form();
				  break;
				  
	}
}