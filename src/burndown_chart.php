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

function plaatscrum_burndown_status( $status, $date=0 ) {

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


function plaatscrum_burndown_chart_form() {
	
	/* input */
	global $user;
	global $access;
		
	/* output */
	global $page;
	global $title;
	
	$project = plaatscrum_db_project($user->project_id);
	
	$title = t('CHART_BURNDOWN_TITLE');

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
	
	$page .= t('CHART_BURNDOWN');
	
	$total1 = plaatscrum_burndown_status( STATUS_ALL );
	$total2 = $total1;
	
	$matrix1 = array();
	$matrix2 = array();
	
	/* Get all workdays from selected sprint */
	$data = plaatscrum_db_sprint($user->sprint_id);
	$diff = strtotime($data->end_date) - strtotime($data->start_date);
	$days = ceil($diff /60/60/24);
	$step2 = $total2 / ($days+1);
	
	$date = strtotime($data->start_date)-(60*60*24);
	$matrix1[''] = $total1;
	$matrix2[''] = $total2;
	
	/* Create matrix 1 */
	$count = 0;
	for ($day=0; $day<=$days; $day++) {
 	 
		$date = strtotime($data->start_date)+($day*60*60*24);

		if (!is_numeric(strpos($project->days, date("w", $date)))) {
			continue;
		}
	 	
		$value1 = plaatscrum_burndown_status(STATUS_DONE, date('d-m-Y', $date));
		$total1 -= $value1;
		
		$value2 = plaatscrum_burndown_status(STATUS_SKIPPED, date('d-m-Y', $date));
		$total1 -= $value2;
		
		$value3 = plaatscrum_burndown_status(STATUS_ONHOLD, date('d-m-Y', $date));
		$total1 -= $value3;
		
		if ($total1 < 0) {
			$total1 = 0;
		}
		
		$matrix1[date('d-m',$date)] = round($total1,1);
		
		$count++;
	}
	
	/* Create matrix 2 */
	$step2 = $total2 / ($count);
	
	for ($day=0; $day<=$days; $day++) {
 	 
		$date = strtotime($data->start_date)+($day*60*60*24);

		if (!is_numeric(strpos($project->days, date("w", $date)))) {
			continue;
		}
		
		if ($day==$days) {
			$total2 = 0;
		} else {
			$total2 -= $step2;
		}
		
		$matrix2[date('d-m',$date)] = round($total2, 1);
	}
	 	
	$graph = new PHPGraphLib();
	$graph->init(950, 500, 'images/graph1.png');
	$graph->addData($matrix1, $matrix2);

	$graph->setBars(false);
	$graph->setLine(true);
	$graph->setDataPoints(true);
	$graph->setDataValues(true);
	$graph->setLegend(true);
	$graph->setGrid(true);
	
	$graph->setLegendOutlineColor('gray');
	$graph->setBackgroundColor("white");
	$graph->setGridColor('gray');
	$graph->setBarColor('255,255,204');
	$graph->setupXAxis(12, 'gray');
	$graph->setupYAxis(12, 'gray');
	$graph->setTextColor('gray');
	$graph->setLineColor('red','blue');
	
	$graph->setDataValueColor('red');
	$graph->setDataPointColor('red');
	
	$graph->setLegendTitle(t('GENERAL_REAL'), t('GENERAL_OPTIMAL'));
		
	$graph->createGraph();
	
	$page .= '<img src="image.php?img=graph1.png" alt="" />';
}

/*
** ------------------
** HANDLER
** ------------------
*/

/** 
 * chart handler
 */
function plaatscrum_burndown_chart() {
	
	/* input */
	global $pid;
		
	/* Page handler */
	switch ($pid) {
						  
		case PAGE_BURNDOWN_CHART: 	
				  plaatscrum_filter();
				  plaatscrum_burndown_chart_form();
				  break;
	 	}
}

/*
** ------------------
** THE END
** ------------------
*/

?>