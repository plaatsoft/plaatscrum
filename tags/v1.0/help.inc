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
** UI
** ------------------
*/

function plaatscrum_help_instructions_form() {

	/* output */
	global $page;
	global $title;
	
	$title = t('HELP_INSTRUCTIONS_TITLE');
	
	$page .= '<div id="content">';
	
 	$page .= '<h1>'.$title.'</h1>';
			
	$page .= t('HELP_INSTRUCTIONS');
	
	$page .= '</div>';
}

function plaatscrum_help_releasenotes_form() {

	/* input */
	global $releasenotes;
	
	/* output */
	global $page;
	global $title;
	
	$title = t('HELP_RELEASENOTES_TITLE');
	
	$page .= '<div id="content">';
	
 	$page .= '<h1>'.$title.'</h1>';
			
	$page .= $releasenotes;
	
	$page .= '</div>';
}
	
function plaatscrum_help_credits_form() {

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
}

function plaatscrum_help_donate_form() {

	/* output */
	global $page;
	global $title;
	global $config;
	
	$title = t('HELP_DONATE_TITLE');
	
	$page .= '<div id="content">';
	
 	$page .= '<h1>'.$title.'</h1>';
	
	$page .= '<h2>'.t('HELP_SUBTITLE_DONATE1').'</h2>';
	$page .= '<p>';
	$page .= t('HELP_DONATE1');
	$page .= '</p>';
	
	$page .= '<h2>'.t('HELP_SUBTITLE_DONATE2').'</h2>';
	$page .= '<p>';
	$page .= t('HELP_DONATE2');
	$page .= '</p>';
	
	$page .=	'</form>';
		
	$page .=	'<form action="https://www.paypal.com/cgi-bin/webscr" method="post">';
	   
	$page .=	'<input type="hidden" name="cmd" value="_s-xclick">';
	$page .=	'<input type="hidden" name="item_name" value="'.$config['applName'].'">';
	$page .=	'<input type="hidden" name="hosted_button_id" value="HYE3BQFZPBDFJ">';
	$page .=	'<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="">';
	$page .=	'<img alt="" border="0" src="https://www.paypalobjects.com/nl_NL/i/scr/pixel.gif" width="1" height="1">';
	$page .=	'</form>';
				
	$page .= '<br/>';
				
	$page .= '</div>';
}

function plaatscrum_help_about_form() {
	
	/* output */
	global $page;
	global $title;
	
	$title = t('HELP_ABOUT_TITLE');
	
	$page .= '<div id="content">';
 	$page .= '<h1>'.$title.'</h1>';
			
	$page .= t('HELP_ABOUT');
	
	$page .= '</div>';
}

/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_help() {

	/* input */
	global $sid;
		
	switch ($sid) {
	
		case PAGE_INSTRUCTIONS: 
					plaatscrum_help_instructions_form();
					break;
				  
		case PAGE_RELEASE_NOTES: 
					plaatscrum_help_releasenotes_form();
					break;
				  
		case PAGE_CREDITS: 
					plaatscrum_help_credits_form();
					break;
		
		case PAGE_DONATE: 
					plaatscrum_help_donate_form();
					break;
				  
		case PAGE_ABOUT: 
					plaatscrum_help_about_form();
					break;
	}
}

/*
** ------------------
** The End
** ------------------
*/

