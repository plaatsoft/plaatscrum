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

function plaatscrum_total_status( $status, $date=0 ) {

	global $user;
	
	$total =0;
	
	$query = 'select sum(points) as points, count(story_id) as amount from story ';
	$query .= 'where type in ('.TYPE_TASK.','.TYPE_BUG.','.TYPE_EPIC.') and ';
	$query .= 'deleted=0 and project_id='.$user->project_id.' and sprint_id='.$user->sprint_id.' ';
	
	if ($status>0) {
		$query .= 'and status='.$status.' ';
	}
	
	if ($date!=0) {
		$query .= 'and CAST(`date` AS date)="'.convert_date_mysql($date).'"';
	}	
	
	$result = plaatscrum_db_query($query);
	if ($data=plaatscrum_db_fetch_object($result))	{
		$total = $data->points;
	}
	
	if ($total>100) {
		$total = round($total,0);
	} else {
		$total = round($total,1);
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
	
	if ($user->sprint_id==0) {
	
		plaatscrum_ui_box("warning", t('CHART_NO_SPRINT_SELECTED'));
		
		return;
	}
	
	$page .= t('CHART_STATUS');
	
	$data = array();
	
	$data = array( t('STATUS_1') => plaatscrum_total_status( STATUS_TODO ),
						t('STATUS_2') => plaatscrum_total_status( STATUS_DOING ),
						t('STATUS_3') => plaatscrum_total_status( STATUS_DONE ),
						t('STATUS_4')  => plaatscrum_total_status( STATUS_SKIPPED ),
						t('STATUS_5') => plaatscrum_total_status( STATUS_ONHOLD ), 
						t('GENERAL_TOTAL') => plaatscrum_total_status( STATUS_ALL ) );
	
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