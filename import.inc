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
** Actions
** ------------------
*/

/* 
** Convert text status label to number.
** If status can not be convertered return 0.
*/
function plaatscrum_import_status_convert($status) {

	$status = trim($status);
	
	if ($status==t('STATUS_1')) {
		return 1;
	} else if ($status==t('STATUS_2')) {
		return 2;
	} else if ($status==t('STATUS_3')) {
		return 3;
	} else if ($status==t('STATUS_4')) {
		return 4;
	} else if ($status==t('STATUS_5')) {
		return 5;
	} else if ($status==t('STATUS_6')) {
		return 6;
	}	else {
		return 0;
	}
}

function plaatscrum_import_prio_convert($prio) {

	$prio = trim($prio);

	if ($prio==t('PRIO_1')) {
		return 1;
	} else if ($prio==t('PRIO_2')) {
		return 2;
	} else if ($prio==t('PRIO_3')) {
		return 3;
	} else {
		return 0;
	}
}

function plaatscrum_import_type_convert($type) {

	$type = trim($type);
	
	if ($type==t('TYPE_1')) {
		return 1;
	} else if ($type==t('TYPE_2')) {
		return 2;
	} else if ($type==t('TYPE_3')) {
		return 3;
	} else if ($type==t('TYPE_4')) {
		return 4;
	} else {
		return 0;
	}
}

/* 
** Check if value is number, if not return 0 
*/
function plaatscrum_import_number_convert($value) {

	$value = trim($value);
	
	if (is_numeric($value)) {
		return $value;
	} else {
		return 0;
	}
}

/* 
** Process csv input file.
*/
function plaatscrum_import_do() {

	/* input */
	global $user;
	
	if (strlen($_FILES['file']['tmp_name'])==0) {
	
		plaatscrum_ui_box('warning', t('IMPORT_NO_FILE_FOUND'));
	
	} else {
		
		if (!strstr($_FILES['file']['name'],'.csv')) {
			  			  
			 plaatscrum_ui_box('warning', t('IMPORT_ONLY_CSV_SUPPORTED'));
			  
		} else {
	
			$size = filesize($_FILES['file']['tmp_name']);
			$file = fopen($_FILES['file']['tmp_name'], 'r');
			$content = fread($file,$size);
			
			
			/* Add extra delimiter after each line */
			$open=true;			
			$len = strlen($content);		
			for ($i=0; $i<$len; $i++) {
			
				if ($content[$i]=='"') {
					$open=!$open;			
				}
				
				if ($open && ($content[$i]=="\n")) {
					$content[$i]=';';
				}		
			}
			$content = str_replace('"', '', $content);
									
			$cols = explode(";", $content);
			
			$offset = 11;
			$count = count($cols) / $offset;
			
			$row=0;
			$skipped=0;
			$insert=0;
			$update=0;
			while ($row < $count ) {
				
				$row++;
				
				/* User Story Number */				
				$number = @plaatscrum_import_number_convert($cols[0+($row * $offset)]);
				
				/* User Story Status */		
				$status = @plaatscrum_import_status_convert($cols[1+($row * $offset)]);
				
				/* User Story Sprint number */	
				$tmp = @plaatscrum_import_number_convert($cols[2+($row * $offset)]);
				$sprint = plaatscrum_db_sprint_check($tmp, $user->project_id);
				
				/* User Story Title */		
				$title = @$cols[3+($row * $offset)];
				
				/* User Story Description */		
				$description = @$cols[4+($row * $offset)];
				
				/* User Story Owner */	
				$tmp = $cols[5+($row * $offset)];
				$owner = plaatscrum_db_user_check($tmp);			
				
				/* User Story Points */	
				$points = @plaatscrum_import_number_convert($cols[6+($row * $offset)]);
				
				/* User Story Reference */	
				$reference = @$cols[7+($row * $offset)];
				
				/* User Story Date */	
				$date = @$cols[8+($row * $offset)];
				
				/* User Prio */	
				$prio = @plaatscrum_import_prio_convert($cols[9+($row * $offset)]);
				
				/* User Type */	
				$type = @plaatscrum_import_type_convert($cols[10+($row * $offset)]);
				
				/* If use case has no number then skip it */
				if ($number==0) {
					$skipped++;
					continue;
				}
					
				/*echo "01. number=".$number."<br/>";
				echo "02. status_id=".$status."<br/>";
				echo "03. sprint_id=".$sprint."<br/>";
				echo "04. title=".$title."<br/>";
				echo "05. description=".$description."<br/>";
				echo "06. user_id=".$owner."<br/>";
				echo "07. points=".$points."<br/>";
				echo "08. reference=".$reference."<br/>";
				echo "09. date=".$date."<br/>";
				echo "10. prio_id=".$prio."<br/>";
				echo "11. type_id=".$type."<br/>";
				echo "-----------------------<br/>";*/
								
				/* Check if user story already exist */				
				$story_id = plaatscrum_db_story_number_check($number, $user->project_id);
				
				if ($story_id>0) {

					$update++;
					
					/* Yes it exist, then update */					
					$data = plaatscrum_db_story($story_id);
												
					$data->status = $status;	
					$data->sprint_id = $sprint;					
					$data->title = $title;
					$data->description = $description;
					$data->user_id = $owner;	
					$data->points = $points;
					$data->reference = $reference;			
					$data->date = convert_date_mysql($date);	
					$data->prio = $prio;	
					$data->type = $type;						
																							
					plaatscrum_db_story_update($data);
					
				} else {
					
					$insert++;
					
					/* User store is new, insert new record */					
					plaatscrum_db_story_insert($number, $title, $description, $status, $points, $reference, $sprint, $user->project_id, $owner, convert_date_mysql($date), $prio, $type, 0);
				}
			}
			fclose($file);
	
			/* Delete tmp file */
			unlink($_FILES['file']['tmp_name']);			
			
			plaatscrum_ui_box('info', t('IMPORT_SUCCESSFULL', ($row-$skipped), $insert, $update));
			plaatscrum_info($user->name.' ['.$user->user_id.'] import to project ['.$user->project_id.'] insert='.$insert.' update='.$update);
		} 					
	}
}


/*
** ------------------
** UI
** ------------------
*/

function plaatscrum_import_form() {

	/* input */
	global $mid;
	global $sid;
	global $access;
	
	/* output */
	global $page;
	
	$page .= plaatscrum_filter();
	
	$page .= '<h1>'.t('IMPORT_TITLE').'</h1>';	
	
	$page .= '<p>';
	$page .= '<label>'.t('GENERAL_FILE').':</label>';
	$page .= '<input name="file" id="file" type="file" size="60"/> ';
	
	if ($access->story_import) {	
		$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_IMPORT, t('LINK_IMPORT'));
	}
	
	$page .= '<div class="note">';
	$page .= t('IMPORT_NOTE',ini_get('upload_max_filesize').'B');
	$page .= '</div>';
	
	$page .= '</p>';
			
}

/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_import_event_handler() {

	/* input */
	global $eid;

	/* Event handler */
	switch ($eid) {
				  		  		
		case EVENT_IMPORT: 
					plaatscrum_import_do();
					break;	
	}
}
					
/*
** ------------------
** THE END
** ------------------
*/

?>