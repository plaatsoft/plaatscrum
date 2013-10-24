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
** MENU
** ------------------
*/

function plaatscrum_login_menu() {
	
	/* input */
	global $mid;
	global $sid;
					
	$menu = '<ul>';
	
	if (($sid==PAGE_LOGIN) || ($sid==0)) $menu .= '<li class="active">'; else $menu .= '<li>';
	$menu .= plaatscrum_link('mid='.$mid.'&sid='.PAGE_LOGIN, t('LINK_LOGIN'));
	$menu .= '</li>';
	
	if ($sid==PAGE_REGISTER) $menu .= '<li class="active">'; else $menu .= '<li>';
	$menu .= plaatscrum_link('mid='.$mid.'&sid='.PAGE_REGISTER, t('LINK_REGISTER'));
	$menu .= '</li>';
	
	if ($sid==PAGE_RECOVER) $menu .= '<li class="active">'; else $menu .= '<li>';
	$menu .= plaatscrum_link('mid='.$mid.'&sid='.PAGE_RECOVER, t('LINK_RECOVER'));
	$menu .= '</li>';
	
	$menu .= '</ul>';
		
	return $menu;
}


function plaatscrum_main_menu() {
	
	/* input */
	global $mid;
	global $sid;
	global $user;
	global $access;
				
	$menu = '<ul>';
	
	if ($mid==MENU_HOME) $menu .= '<li class="active">'; else $menu .= '<li>';
	$menu .= plaatscrum_link('mid='.MENU_HOME.'&sid='.PAGE_HOME, t('LINK_HOME'));
	$menu .= '</li>';
	
	/* -----------------*/
	
	if ($mid==MENU_BACKLOG) $menu .= '<li class="active">'; else $menu .= '<li>';
	$menu .= plaatscrum_link('mid='.MENU_BACKLOG.'&sid='.PAGE_BACKLOG_FORM, t('LINK_BACKLOG'));
		
		$menu .= '<ul>';
	
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_BACKLOG.'&sid='.PAGE_BACKLOG_FORM, t('LINK_PRODUCT'));
		$menu .= '</li>';
	
		if ($access->story_export) {
		
			$menu .= '<li>';
			$menu .= plaatscrum_link('mid='.MENU_BACKLOG.'&sid='.PAGE_BACKLOG_EXPORT, t('LINK_EXPORT'));
			$menu .= '</li>';
		}
		
		if ($access->story_import) {
		
			$menu .= '<li>';
			$menu .= plaatscrum_link('mid='.MENU_BACKLOG.'&sid='.PAGE_BACKLOG_IMPORT, t('LINK_IMPORT'));
			$menu .= '</li>';
		}
		
		if ($access->story_add) {
		
			$menu .= '<li>';		
			$menu .= '<label>';	
			$menu .= '<hr/>';			
			$menu .= '</label>';	
			$menu .= '</li>';
			
			$menu .= '<li>';
			$menu .= plaatscrum_link('mid='.$mid.'&sid='.PAGE_STORY.'&eid='.EVENT_STORY_NEW.'&type='.TYPE_STORY.'&id=0', t('LINK_ADD_STORY'));
			$menu .= '</li>';

		}
		
		$menu .= '</ul>';
			
	$menu .= '</li>';
	
		
	/* -----------------*/
	
	if ($mid==MENU_BOARD) $menu .= '<li class="active">'; else $menu .= '<li>';
	$menu .= plaatscrum_link('mid='.MENU_BOARD.'&sid='.PAGE_TASKBOARD, t('LINK_BOARD'));
	
	$menu .= '<ul>';
		
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_BOARD.'&sid='.PAGE_TASKBOARD, t('LINK_TASKBOARD'));
		$menu .= '</li>';
							
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_BOARD.'&sid='.PAGE_RESOURCEBOARD, t('LINK_RESOURCEBOARD'));
		$menu .= '</li>';
		
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_BOARD.'&sid='.PAGE_STATUSBOARD, t('LINK_STATUSBOARD'));
		$menu .= '</li>';
				
		if ($access->role_id==ROLE_SCRUM_MASTER) {
			$menu .= '<li>';
			$menu .= plaatscrum_link('mid='.MENU_BOARD.'&sid='.PAGE_COST, t('LINK_COST'));
			$menu .= '</li>';
		}
		
		$menu .= '</ul>';
		
		
	$menu .= '</li>';
	
	/* -----------------*/
	
	if ($mid==MENU_CHART) $menu .= '<li class="active">'; else $menu .= '<li>';
	$menu .= plaatscrum_link('mid='.MENU_CHART.'&sid='.PAGE_BURNDOWN_CHART, t('LINK_CHART'));
	
		$menu .= '<ul>';
	
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_CHART.'&sid='.PAGE_BURNDOWN_CHART, t('LINK_BURNDOWN'));
		$menu .= '</li>';
	
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_CHART.'&sid='.PAGE_VELOCITY_CHART, t('LINK_VELOCITY'));
		$menu .= '</li>';
		
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_CHART.'&sid='.PAGE_STATUS_CHART, t('LINK_STATUS'));
		$menu .= '</li>';
		
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_CHART.'&sid='.PAGE_CALENDER, t('LINK_CALENDER'));
		$menu .= '</li>';
			
		$menu .= '</ul>';
	
	$menu .= '</li>';	
	
	/* -----------------*/
	
	if ($mid==MENU_SETTINGS) $menu .= '<li class="active">'; else $menu .= '<li>';
	$menu .= plaatscrum_link('mid='.MENU_SETTINGS.'&sid='.PAGE_GENERAL, t('LINK_SETTINGS'));
	
		$menu .= '<ul>';
		
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_SETTINGS.'&sid='.PAGE_GENERAL, t('LINK_GENERAL'));
		$menu .= '</li>';
	
		if ($user->role_id==ROLE_ADMINISTRATOR) {
			$menu .= '<li>';
			$menu .= plaatscrum_link('mid='.MENU_SETTINGS.'&sid='.PAGE_USERLIST, t('LINK_USERS'));
			$menu .= '</li>';
		}
		
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_SETTINGS.'&sid='.PAGE_PROJECTLIST_FORM, t('LINK_PROJECTS'));
		$menu .= '</li>';
				
		$menu .= '</ul>';
	
	/* -----------------*/
		
	if ($mid==MENU_HELP) $menu .= '<li class="active">'; else $menu .= '<li>';
	$menu .= plaatscrum_link('mid='.MENU_HELP.'&sid='.PAGE_INSTRUCTIONS, t('LINK_HELP'));
	
		$menu .= '<ul>';
	
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_HELP.'&sid='.PAGE_INSTRUCTIONS, t('LINK_INSTRUCTIONS'));
		$menu .= '</li>';
	
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_HELP.'&sid='.PAGE_RELEASE_NOTES, t('LINK_RELEASENOTES'));
		$menu .= '</li>';
	
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_HELP.'&sid='.PAGE_CREDITS, t('LINK_CREDITS'));
		$menu .= '</li>';
	
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_HELP.'&sid='.PAGE_DONATE, t('LINK_DONATE'));
		$menu .= '</li>';
		
		$menu .= '<li>';
		$menu .= plaatscrum_link('mid='.MENU_HELP.'&sid='.PAGE_ABOUT, t('LINK_ABOUT'));
		$menu .= '</li>';
	
		$menu .= '</ul>';

	$menu .= '</li>';

	/* -----------------*/
		
	if ($mid==MENU_LOGIN) $menu .= '<li class="active">'; else $menu .= '<li>';
	$menu .= plaatscrum_link('mid='.MENU_LOGIN.'&eid='.EVENT_LOGOUT, t('LINK_LOGOUT'));
	$menu .= '</li>';
	
	$menu .= '</ul>';
		
	return $menu;
}

function plaatscrum_menu() {

	/* input */
	global $mid;
	global $menu;
	
	$menu = '<div id="topnav">';
	
	switch ($mid) {
	
		case MENU_LOGIN:
			$menu .= plaatscrum_login_menu();
			break;
					
		default: 
			$menu .= plaatscrum_main_menu();
			break;
	}
	
	$menu .= '</div>';	
		
	return $menu;
}


?>