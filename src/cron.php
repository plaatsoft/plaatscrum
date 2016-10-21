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

function plaatscrum_get_uuid() {

    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

/**
 * Cron backup job 
 */
function plaatscrum_backup_event() {

	/* input */
	global $config;
	
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		// Windows
		$dump = 'C:\wamp\bin\mysql\mysql5.7.14\bin\mysqldump';
	} else {
		// Linux 
		$dump = '/usr/bin/mysqldump';
	}

	$directory = getcwd().'/backup/';
	
	/* Remove old database backup file */		
	if (@$dh = opendir($directory)) {
		while (false !== ($filename = readdir($dh))) {
			if (($filename!='.') && ($filename!='..') && ($filename!='.htaccess') && ($filename!='index.php')) {
			
				if ((time()-filemtime($directory.$filename))>($config['backup_period']*24*60*60)) {
					plaatscrum_info('Remove old backup file '.$filename);
					@unlink($directory.$filename);			
				}
			}
		}
	}
	
	$filename = $directory.'plaatscrum-'.plaatscrum_get_uuid().'.sql';
	
	/* Create new database backup file */
	$command = $dump.' --user='.$config["dbuser"].' --password='.$config["dbpass"].' --host='.$config["dbhost"].' '.$config["dbname"].' > '.$filename;
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
				plaatscrum_backup_event();				
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