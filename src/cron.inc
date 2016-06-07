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

/**
 * Cron backup job 
 */
function plaatscrum_cron_backup() {

	/* input */
	global $config;
	
	/* Remove old database backup file */
	$prev_date = mktime(date("H"), date("i"), date("s"), date("m"), date("d")-$config['backup_period'], date("Y"));
	$filename = 'backup/scrumboard-'.date('Ymd', $prev_date).'.sql';
	plaatscrum_info('Remove old backup file '.$filename);
	@unlink($filename);			
	
	$filename = 'backup/scrumboard-'.date("Ymd").'.sql';
	
	/* Create new database backup file */
	$backupFile = 'database_backup_'.date("YmdHis").'.sql';
	$command = 'mysqldump --user='.$config["dbuser"].' --password='.$config["dbpass"].' --host='.$config["dbhost"].' '.$config["dbname"].' > '.$filename;
	system($command);
			
	$message = 'Create new backup file '.$filename.'<br/>';
	plaatscrum_info($message);
}

/*
** ------------------
** UI
** ------------------
*/

/*
** ---------------------
** HANDLER
** ---------------------
*/

/** 
 * cron handler 
 */
function plaatscrum_cron() {
	
	$query  = 'select cron_id, description from cron where DATE(last_run)!="'.date("Y-m-d").'"'; 
	$result = plaatscrum_db_query($query);	
		
	if ($data = plaatscrum_db_fetch_object($result)) {
	
		/* Event handler */
		switch ($data->cron_id) {
		
			case 1:
				plaatscrum_info('CRON JOB ['.$data->cron_id.'] '.$data->description.' - job start.');
				plaatscrum_db_cron_update($data->cron_id);
				plaatscrum_cron_backup();				
				plaatscrum_info('CRON JOB ['.$data->cron_id.'] '.$data->description.' - job end.');
				break;
	
		}		
	}	
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>