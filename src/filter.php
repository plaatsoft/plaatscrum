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

$filter_project = plaatscrum_post("filter_project", 0);
$filter_sprint = plaatscrum_post("filter_sprint", 0);
$filter_status = plaatscrum_multi_post("filter_status", "");
$filter_owner = plaatscrum_post("filter_owner", 0);
$filter_prio = plaatscrum_multi_post("filter_prio", "");
$filter_type = plaatscrum_multi_post("filter_type", "");
$filter_month = plaatscrum_post("filter_month", date('m'));
$filter_year = plaatscrum_post("filter_year", date('Y'));

/*
** ------------------
** ACTIONS
** ------------------
*/

function plaatscrum_filter_do() {

	/* input */	
	global $filter_sprint;
	global $filter_status;
	global $filter_project;
	global $filter_owner;
	global $filter_prio;
	global $filter_type;
	
	/* output */
	global $user;
	global $access;
		
	if ($filter_project!=$user->project_id) {
		$user->sprint_id = plaatscrum_db_sprint_first($filter_project);
		$user->project_id = $filter_project;
		
	} else {
	
		$user->sprint_id = $filter_sprint;
	}
	$user->status = $filter_status;
	$user->owner = $filter_owner;
	$user->prio = $filter_prio;
	$user->type = $filter_type;
		
	plaatscrum_db_user_update($user);
	
	/* Refresh access rights */
	$data = plaatscrum_db_project_user($user->project_id, $user->user_id);
	if (isset($data->role_id)) {
		$access = plaatscrum_db_role($data->role_id);
	} else {
		$access = plaatscrum_db_role(ROLE_GUEST);
	}
	
}

/*
** ------------------
** UI
** ------------------
*/


function plaatscrum_filter_project() {

	/* input */
	global $user;
	
	/* output */
	global $page;
		
	$page	.= t('GENERAL_PROJECT').': ';
	$page .= plaatscrum_ui_project('filter_project', $user->project_id, false);		
	$page .= ' ';
}

function plaatscrum_filter_sprint() {

	/* input */
	global $user;
	
	/* output */
	global $page;

	$page	.= t('GENERAL_SPRINT').': ';
	$page .= plaatscrum_ui_sprint('filter_sprint', $user->sprint_id, false, true, false);		
	$page .= ' ';
}

function plaatscrum_filter_status() {

	/* input */
	global $user;
	
	/* output */
	global $page;
	
	$page	.= t('GENERAL_STATUS').': ';
	$page .= plaatscrum_ui_multi_status('filter_status', $user->status, false, true);		
	$page .= ' ';
}
		

function plaatscrum_filter_owner() {

	/* input */
	global $user;
	
	/* output */
	global $page;
	
	$page	.= t('GENERAL_OWNER').': ';
	$page .= plaatscrum_ui_project_user('filter_owner', $user->owner, false, true);		
	$page .= ' ';
}
	
function plaatscrum_filter_prio() {

	/* input */
	global $user;
	
	/* output */
	global $page;
	
	$page	.= t('GENERAL_PRIO').': ';
	$page .= plaatscrum_ui_multi_prio('filter_prio', $user->prio, false, true);		
	$page .= ' ';
}

function plaatscrum_filter_type() {

	/* input */
	global $user;
	
	/* output */
	global $page;
	
	$page	.= t('GENERAL_TYPE').': ';
	$page .= plaatscrum_ui_multi_type('filter_type', $user->type, false, true);		
	$page .= ' ';	
}

function plaatscrum_filter_month() {

	/* input */
	global $user;
	global $filter_month;
	global $filter_year;
	
	/* output */
	global $page;
	
	$page	.= t('GENERAL_DATE').': ';
	
	/* select month control */
	$page .= '<select name="filter_month" id="filter_month">';
	for($x = 1; $x <= 12; $x++) {
		$page .= '<option value="'.$x.'"'.($x != $filter_month ? '' : ' selected="selected"').'>'.date('F',mktime(0,0,0,$x,1,$filter_year)).'</option>';
	}
	$page .= '</select>';	
}
	
function plaatscrum_filter_year() {

	/* input */
	global $user;
	global $filter_year;
	
	/* output */
	global $page;
	
	/* select year control */
	$year_range = 7;
	$page .= '<select name="filter_year" id="filter_year">';
	for($x = ($filter_year-floor($year_range/2)); $x <= ($filter_year+floor($year_range/2)); $x++) {
		$page .= '<option value="'.$x.'"'.($x != $filter_year ? '' : ' selected="selected"').'>'.$x.'</option>';	
	}
	$page .= '</select> ';
}

function plaatscrum_search() {
	
	// input
	global $mid;
	global $pid;
	global $sort;
	
	// output
	global $page;
	
	$page .= '<div id="search">';		
	$page .= '<input type="text" name="search" id="search" size="10" value="'.t('HELP').'" onfocus="this.value=(this.value==\''.t('HELP').'\')? \'\' : this.value ;" />'; 
	$page .= '</div>';
	
	$page .= '<script> ';
	$page .= 'document.getElementById("search").addEventListener("keydown", function(event) { ';
	$page .= 'if (event.keyCode == 13) { ';
	$page .= 'link(\''.plaatscrum_token('mid='.$mid.'&pid='.$pid.'&eid='.EVENT_SEARCH.'&sort='.$sort).'\'); ';
	$page .= '} ';
	$page .= '}); ';
	$page .= '</script>';		
}

/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_filter() {

	/* input */
	global $eid;
	global $mid;
	global $pid;
	global $sort;
	
	/* output */
	
	global $page;
	
	/* Event handler */
	switch ($eid) {
						
		case EVENT_FILTER:
					plaatscrum_filter_do();
					break;
	}
	
	$page .= '<div id="filter">';
	
	/* Page handler */
	switch ($pid) {
	
		case PAGE_HOME: 
					plaatscrum_filter_project();	
					plaatscrum_filter_sprint();	
					plaatscrum_filter_status();	
					plaatscrum_filter_prio();
					plaatscrum_filter_type();
					plaatscrum_search();
					break;
					
		case PAGE_BACKLOG_FORM: 
					plaatscrum_filter_project();	
					plaatscrum_filter_sprint();	
					plaatscrum_filter_status();	
					plaatscrum_filter_owner();
					plaatscrum_filter_prio();
					plaatscrum_filter_type();
					plaatscrum_search();
					break;
							
		case PAGE_STATUS_CHART: 
		case PAGE_BURNDOWN_CHART:
		case PAGE_VELOCITY_CHART: 	
		case PAGE_COST: 
					plaatscrum_filter_project();	
					plaatscrum_filter_sprint();	
					break;
					
		case PAGE_STATUSBOARD: 	
		case PAGE_RESOURCEBOARD: 
					plaatscrum_filter_project();	
					plaatscrum_filter_sprint();	
					plaatscrum_search();
					break;
		
		case PAGE_TASKBOARD: 	
					plaatscrum_filter_project();	
					plaatscrum_filter_sprint();	
					plaatscrum_filter_owner();
					plaatscrum_search();
					break;
									
		case PAGE_BACKLOG_IMPORT: 	
		case PAGE_BACKLOG_EXPORT: 		
					plaatscrum_filter_project();	
					break;
					
		case  PAGE_CALENDER_CHART: 	
					plaatscrum_filter_project();	
					plaatscrum_filter_status();	
					plaatscrum_filter_type();
					plaatscrum_filter_month();	
					plaatscrum_filter_year();						
					break;
					
	}
	
	$page .= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&eid='.EVENT_FILTER.'&sort='.$sort, t('LINK_FILTER'));
	
	$page .= '</div>';
}

/*
** ------------------
** The End
** ------------------
*/


