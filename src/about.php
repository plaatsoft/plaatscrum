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
** UI
** ------------------
*/
	
function plaatscrum_about_form() {
	
	/* output */
	global $page;
	global $title;
	
	$title = t('HELP_ABOUT_TITLE');
	
	$page .= '<div id="content">';
 	$page .= '<h1>'.$title.'</h1>';			
	$page .= t('HELP_ABOUT');	
	$page .= '</div>';
	
	$page .= '<div id="column">';
   $page .= '<img class="imgr" src="images/help.svg" width="256" height="256" alt="" />';
	$page .= '</div>';	
}

/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_about() {

	/* input */
	global $pid;
		
	switch ($pid) {

		case PAGE_ABOUT: 
					plaatscrum_about_form();
					break;
	}
}

/*
** ------------------
** The End
** ------------------
*/

