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
** POST PARAMETERS
** ------------------
*/
	
/*
** ------------------
** ACTIONS
** ------------------
*/

/** 
 * Make mysql backup of all existing tables 
 */
function plaatscrum_backup_do($filename, $host, $user, $pass, $name) {
  
	$return="";
  
	$link = mysql_connect($host,$user,$pass);
	mysql_select_db($name, $link);
  
	$tables = array();
   $result = mysql_query('SHOW TABLES');
   while($row = mysql_fetch_row($result)) {
		$tables[] = $row[0];
	}
  
	foreach($tables as $table) {
	
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
    
		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
    
		for ($i = 0; $i < $num_fields; $i++) {
      
			while($row = mysql_fetch_row($result)) {
				
				$return .= 'INSERT INTO '.$table.' VALUES(';
				
				for($j=0; $j<$num_fields; $j++) {
					$row[$j] = addslashes($row[$j]);
          
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}
  
	/* save file */
	$handle = fopen($filename,'w+');
	fwrite($handle,$return);
	fclose($handle);
}

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
	
	/* Create new database backup file */
	$filename = 'backup/scrumboard-'.date("Ymd").'.sql';
	plaatscrum_backup_do($filename, $config["dbhost"], $config["dbuser"], $config["dbpass"], $config["dbname"]);
		
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