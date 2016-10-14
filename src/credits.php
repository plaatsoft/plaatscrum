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

function plaatscrum_credits_form() {

	/* input */
	global $credits;
	
	/* output */
	global $page;
	global $title;
	
	$title = t('HELP_CREDITS_TITLE');
	
	$page .= '<div id="content">';	
 	$page .= '<h1>'.$title.'</h1>';			
	$page .= t('HELP_CREDITS');	
	$page .= '</div>';
	
	$page .= '<div id="column">';
   $page .= '<img class="imgr" src="images/info.svg" width="256" height="256" alt="" />';
	$page .= '</div>';	
}


/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_credits() {

	/* input */
	global $sid;
		
	switch ($sid) {

		case PAGE_CREDITS: 
					plaatscrum_credits_form();
					break;
	}
}

/*
** ------------------
** The End
** ------------------
*/

