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

function plaatscrum_total_status( $status) {

	// input
	global $user;
	
	global $filter_project;
	global $filter_sprint;
	global $filter_status;
	global $filter_prio;
	global $filter_type;
	global $filter_owner;
	
	$total =0;
	
	$query =  'select sum(a.points) as points, count(a.story_id) as amount from story a ';
	$query .= 'left join tuser c on a.user_id=c.user_id where a.type in ('.TYPE_TASK.','.TYPE_BUG.','.TYPE_EPIC.') and ';
	$query .= 'a.deleted=0 and a.project_id='.$filter_project.' ';
	
	if ($filter_sprint>0) {
		$query .= 'and a.sprint_id='.$filter_sprint.' ';	
	}
	
	if ($status>0) {
		$query .= 'and a.status='.$status.' ';
	}
	
	if ($filter_owner>0) {
		$query .= 'and c.user_id='.$filter_owner.' ';	
	}
		
	$result = plaatscrum_db_query($query);
	if ($data=plaatscrum_db_fetch_object($result))	{
		$total = $data->points;
	}
	
	return $total;
}

function plaatscrum_status_chart_form() {
	
	/* input */
	global $user;
	global $access;
		
	/* output */
	global $page;
	global $title;

	$title = t('CHART_STATUS_TITLE');

	$page .= '<h1>';
	$page .= $title;
	$page .= '</h1>';
	
	if ($user->project_id==0) {
	
		plaatscrum_ui_box("warning", t('CHART_NO_PROJECT_SELECTED'));
		
		return;
	}
	
	$page .= t('CHART_STATUS');
	
	$data = array();
	
	$data = array( t('STATUS_1') => number_format(plaatscrum_total_status( STATUS_TODO ),1),
						t('STATUS_2') => number_format(plaatscrum_total_status( STATUS_DOING ),1),
						t('STATUS_3') => number_format(plaatscrum_total_status( STATUS_DONE ),1),
						t('STATUS_4')  => number_format(plaatscrum_total_status( STATUS_SKIPPED ),1),
						t('STATUS_5') => number_format(plaatscrum_total_status( STATUS_ONHOLD ),1), 
						t('GENERAL_TOTAL') => number_format(plaatscrum_total_status( STATUS_ALL ),1) );
	
	$graph = new PHPGraphLib();
	$graph->init(950,500, 'images/graph2.png');
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
	
	$page .= '<img src="image.php?img=graph2.png" alt="" />';
}

/*
** ------------------
** HANDLER
** ------------------
*/

/** 
 * chart handler
 */
function plaatscrum_status_chart() {
	
	/* input */
	global $pid;
	
	/* Page handler */
	switch ($pid) {
		
		case PAGE_STATUS_CHART: 	
				  plaatscrum_filter();
				  plaatscrum_status_chart_form();
				  break;
	}
}

/*
** ------------------
** THE END
** ------------------
*/