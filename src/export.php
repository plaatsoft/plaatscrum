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

$export_sort = plaatscrum_post("export_sort", 0);
$export_type = plaatscrum_post("export_type", 0);

/*
** ------------------
** ACTIONS
** ------------------
*/

function plaatscrum_export_json_do($result) {

	$posts = array();
	
	while($post = plaatscrum_db_fetch_object($result)) {
		$posts[] = array('story'=>$post);
	}

	/* output in necessary format */
	header('Content-type: application/json');
	echo json_encode(array('stories'=>$posts));
}

function plaatscrum_export_xml_do($result) {

	$posts = array();
	
	while($post = plaatscrum_db_fetch_object($result)) {
		$posts[] = array('story'=>$post);
	}

	header('Content-type: text/xml');
	
	echo '<stories>';
	foreach($posts as $index => $post) {
		if(is_array($post)) {
		foreach($post as $key => $value) {
			echo '<',$key,'>';
			if(is_array($value)) {
				foreach($value as $tag => $val) {
					
					echo '<',$tag,'>';
				
					if ($tag=="status") {				
						echo t('STATUS_'.$val);
					} else if ($tag=="prio") {				
						echo t('PRIO_'.$val);
					} else if ($tag=="type") {				
						echo t('TYPE_'.$val);
					} else {
						echo htmlentities($val);
					} 
					echo '</',$tag,'>';					
				}
			}
			echo '</',$key,'>';
		}
		}
	}
	echo '</stories>';
}

function plaatscrum_export_csv_do($result) {
  			
	$page  = t('GENERAL_NUMBER');
	$page .= ';';		
	$page .= t('GENERAL_STATUS');
	$page .= ';';
	$page	.= t('GENERAL_SPRINT');
	$page .= ';';
	$page .= t('GENERAL_SUMMARY');
	$page .= ';';	
	$page .= t('GENERAL_DESCRIPTION');
	$page .= ';';	
	$page .= t('GENERAL_OWNER');
	$page .= ';';	
	$page	.= t('GENERAL_POINTS');
	$page .= ';';	
	$page	.= t('GENERAL_REFERENCE');
	$page .= ';';	
	$page	.= t('GENERAL_DATE');
	$page .= ';';	
	$page	.= t('GENERAL_PRIO');
	$page .= ';';	
	$page	.= t('GENERAL_TYPE');
	
	$page .= "\r\n";
	
	while ($data=plaatscrum_db_fetch_object($result)) {	
	
		$page .= $data->number;
		$page .= ';';	
		$page .= '"'.t('STATUS_'.$data->status).'"';
		$page .= ';';	
		$page .= $data->sprint_number;
		$page .= ';';	
		$page .= '"'.$data->summary.'"';
		$page .= ';';	
		$page .= '"'.$data->description.'"';
		$page .= ';';	
		$page .= '"'.$data->name.'"';
		$page .= ';';	
		$page .= $data->points;
		$page .= ';';	
		$page .= '"'.$data->reference.'"';
		$page .= ';';	
		$page .= '"'.convert_date_php($data->date).'"';
		$page .= ';';	
		$page .= t('PRIO_'.$data->prio);
		$page .= ';';	
		$page .= t('TYPE_'.$data->type);
		$page .= "\r\n";
	}

	header('HTTP/1.1 200 OK');
	header('Date: ' . date("D M j G:i:s T Y"));
	header('Last-Modified: ' . date("D M j G:i:s T Y"));
	header("Content-Type: application/force-download"); 
	header("Content-Lenght: " . (string)(strlen($page)));
	header("Content-Transfer-Encoding: Binary"); 
	header('Content-Disposition: attachment; filename="backlog.csv"' );

	echo $page;	
}


function plaatscrum_export_do() {

	/* input */
	global $user;
	global $export_type;	
	global $export_sort;
	
	$query  = 'select a.number, a.status, a.sprint_id, a.summary, a.description, b.name, a.points, a.reference,';
	$query .= 'a.date, a.prio, a.type, c.number as sprint_number ';
	$query .= 'from story a left join tuser b on a.user_id=b.user_id left join sprint c on a.sprint_id=c.sprint_id ';
	$query .= 'where a.project_id='.$user->project_id.' and a.deleted=0 ';	
	
	switch ($export_sort) {
	
		default: 
			$query .= 'order by a.number';
			break;
		
		case 1: 
			$query .= 'order by a.status, a.number';
			break;
			
		case 2: 
			$query .= 'order by a.points, a.number';
			break;
			
		case 3: 
			$query .= 'order by a.prio, a.number';
			break;
			
		case 4: 
			$query .= 'order by a.type, a.number';
			break;
		
		case 5: 
			$query .= 'order by sprint_number, a.number';
			break;
	}
	
	$result = plaatscrum_db_query($query);
		
	switch ($export_type) {
		
		case 0: 
				plaatscrum_export_csv_do($result);
				plaatscrum_info($user->name.' ['.$user->user_id.'] csv export project ['.$user->project_id.']');
				break;
				
		case 1: 
				plaatscrum_export_xml_do($result);
				plaatscrum_info($user->name.' ['.$user->user_id.'] xml export project ['.$user->project_id.']');
				break;		
				
		case 2: 
				plaatscrum_export_json_do($result);
				plaatscrum_info($user->name.' ['.$user->user_id.'] json export project ['.$user->project_id.']');
				break;
	}
}
					
/*
** ------------------
** UI
** ------------------
*/

function plaatscrum_ui_sort($tag) {
			
	$values = array( 0 => t('GENERAL_NUMBER'), 1 => t('GENERAL_STATUS'), 2 => t('GENERAL_POINTS'), 
					     3 => t('GENERAL_PRIO'), 4 => t('GENERAL_TYPE'), 5 => t('GENERAL_SPRINT'));

	$page ='<select id="'.$tag.'" name="'.$tag.'" ';
	
	$page .= '>'; 
	
	foreach ($values as $key => $value) {
	
		$page.='<option value="'.$key.'">'.$value.'</option>';
	}
		
	$page .='</select>';
	
	return $page;
}
	

function plaatscrum_ui_export_type($tag) {
			
	$values = array( 0 => t('GENERAL_CSV'), 1 => t('GENERAL_XML'), 2 => t('GENERAL_JSON'));

	$page ='<select id="'.$tag.'" name="'.$tag.'" ';
	
	$page .= '>'; 
	
	foreach ($values as $key => $value) {
	
		$page.='<option value="'.$key.'">'.$value.'</option>';
	}
		
	$page .='</select>';
	
	return $page;
}

function plaatscrum_export_form() {

	/* input */
	global $mid;
	global $sid;
	global $access;
	
	/* output */
	global $page;
	
	$page .= plaatscrum_filter();
		
	$page .= '<h1>'.t('EXPORT_TITLE').'</h1>';	
	
	$page .= '<p>';
	$page .= '<label>'; 
	$page .= t('GENERAL_SORT').':';
	$page .= '</label>'; 
	$page .= '<br/>';
	$page .= plaatscrum_ui_sort("export_sort");
	$page .= '</p>';
	
	$page .= '<p>';
	$page .= '<label>'; 
	$page .= t('GENERAL_TYPE').':';
	$page .= '</label>'; 
	$page .= '<br/>';
	$page .= plaatscrum_ui_export_type("export_type");
	$page .= '</p>';
		
	$page .= '<p>';
	
	if ($access->story_export) {
		$page .= plaatscrum_link('mid='.$mid.'&sid='.$sid.'&eid='.EVENT_EXPORT, t('LINK_EXPORT'));
	}
	$page .= '</p>';
	
}

/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_export_event_handler() {

	/* input */
	global $eid;

	/* Event handler */
	switch ($eid) {
				  		  		
		case EVENT_EXPORT: 
					plaatscrum_export_do();	
					break;
	}
}

/*
** ------------------
** THE END
** ------------------
*/

?>
