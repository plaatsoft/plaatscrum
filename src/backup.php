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

function plaatscrum_delete_file_event() {

	/* input */
	global $filename;
	
	$directory = getcwd().'/backup/';
	
	unlink ($directory.$filename);
}

/*
** ------------------
** UI
** ------------------
*/


function plaatscrum_backup_form() {

	/* input */
	global $mid;
	global $pid;
	
	/* output */
	global $page;
	global $title;
				
	$title = t('BACKUP_TITLE');	
			
	$page .= '<h1>';	
	$page .= $title;
	$page .= '</h1>';

	$page .= '<p>';
	$page .= t('BACKUP_CONTENT');
	$page .= '</p>';
	
	$page .= '<table>';
			
	$page .= '<thead>';
	$page .= '<tr>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_FILENAME');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_FILESIZE');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_TIMESTAMP');
	$page .= '</th>';
	
	$page .= '<th>';
	$page	.= t('GENERAL_ACTION');
	$page .= '</th>';
	
	$page .= '</tr>';
	
	$page .= '</thead>';
	$page .= '<tbody>';
		
	$count=0;
		
	$directory = getcwd().'/backup/';
		
	if (@$dh = opendir($directory)) {
		while (false !== ($filename = readdir($dh))) {
	
			if (($filename!='.') && ($filename!='..') && ($filename!='.htaccess') && ($filename!='index.php')) {
			
				$page .= '<tr ';		
				if ((++$count % 2 ) == 1 ) {
					$page .= 'class="light" ';
				} else {
					$page .= 'class="dark" ';
				}				
				$page .='>';
		
				$page .= '<td>';
				$page .= '<a href="backup/'.$filename.'">'.$filename.'</a>';
				$page .= '</td>';
				
				$page .= '<td>';
				$page .= filesize($directory.$filename);
				$page .= '</td>';
		
				$page .= '<td>';
				$page .= date("d-m-Y H:i:s",filemtime($directory.$filename));
				$page .= '</td>';

				$page .= '<td>'.plaatscrum_link('pid='.$pid.'&mid='.$mid.'&eid='.EVENT_DELETE.'&filename='.$filename, t('LINK_DELETE')).'</td>';
				$page .= '</tr>';
			}
		}	
	}
	
	$page .= '</tbody>';
	$page .= '</table>';	
	
	$page .= '<p>';		
	$page .= plaatscrum_link('mid='.$mid.'&pid='.$pid.'&eid='.EVENT_BACKUP, t('LINK_BACKUP'));
	$page .= '</p>';	
}

/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_backup() {

	/* input */
	global $mid;
	global $pid;
	global $eid;
	
	/* Event handler */
	switch ($eid) {
	
		case EVENT_BACKUP: 
					plaatscrum_backup_event();
					break;
	
		case EVENT_DELETE: 
					plaatscrum_delete_file_event();
					break;
	}
	
	/* Page handler */
	switch ($pid) {
	
		case PAGE_BACKUP: 
					plaatscrum_backup_form();	
					break;
	}
}


/*
** ------------------
** The End
** ------------------
*/

