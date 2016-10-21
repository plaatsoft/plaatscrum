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
** -----------------
** GENERAL
** ----------------- 
*/

/**
 * connect to database
 * @param $dbhost database hostname
 * @param $dbuser database username
 * @param $dbpass database password
 * @param $dbname database name
 * @return connect result (true = successfull connected | false = connection failed)
 */
function plaatscrum_db_connect($dbhost, $dbuser, $dbpass, $dbname) {

	global $db;

   $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);	
	if (mysqli_connect_errno()) {
		plaatscrum_db_error();
		return false;		
	}
	return true;
}

/**
 * Disconnect from database  
 * @return disconnect result
 */
function plaatscrum_db_close() {

	global $db;

	mysqli_close($db);

	return true;
}

/**
 * Show SQL error 
 * @return HTML formatted SQL error
 */
function plaatscrum_db_error() {

	if (DEBUG == 1) {
		echo mysqli_connect_error(). "<br/>\n\r";
	}
}

/**
 * Count queries 
 * @return queries count
 */
$query_count=0;
function plaatscrum_db_count() {

	global $query_count;
	return $query_count;
}

/**
 * Execute database multi query
 */
function plaatscrum_db_multi_query($queries) {

	$tokens = @preg_split("/;/", $queries);
	foreach ($tokens as $token) {
	
		$token=trim($token);
		if (strlen($token)>3) {
			plaatscrum_db_query($token);		
		}
	}
}

/**
 * Execute database query
 * @param $query SQL query with will be executed.
 * @return Database result
 */
function plaatscrum_db_query($query) {
			
	global $query_count;
	global $db;
	
	$query_count++;

	if (DEBUG == 1) {
		echo $query."<br/>\r\n";
	}

	@$result = mysqli_query($db, $query);

	if (!$result) {
		plaatscrum_db_error();		
	}
	
	return $result;
}

/**
 * escap database string
 * @param $data  input.
 * @return $data escaped
 */
function plaatscrum_db_escape($data) {

	global $db;
	
	return mysqli_real_escape_string($db, $data);
}

/**
 * Fetch query result 
 * @return mysql data set if any
 */
function plaatscrum_db_fetch_object($result) {
	
	$row="";
	
	if (isset($result)) {
		$row = $result->fetch_object();
	}
	return $row;
}

/**
 * Return number of rows
 * @return number of row in dataset
 */
function plaatscrum_db_num_rows($result) {
	
	return mysqli_num_rows($result);
}

/*
** ------------------------
** CREATED / PATCH DATABASE
** ------------------------
*/

function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
}

/**
 * Execute SQL script
 * @param $version Version of sql patch file
 */
function plaatscrum_db_execute_sql_file($version) {

    $filename = 'database/patch-'.$version.'.sql';

    $commands = file_get_contents($filename);

    //delete comments
    $lines = explode("\n",$commands);
    $commands = '';
    foreach($lines as $line){
        $line = trim($line);
        if( $line && !startsWith($line,'--') ){
            $commands .= $line . "\n";
        }
    }

    //convert to array
    $commands = explode(";\n", $commands);

    //run commands
    $total = $success = 0;
    foreach($commands as $command){
        if(trim($command)){
	         $success += (@plaatscrum_db_query($command)==false ? 0 : 1);
            $total += 1;
        }
    }

    //return number of successful queries and total number of queries found
    return array(
        "success" => $success,
        "total" => $total
    );
}

/**
 * Check db version and upgrade if needed!
 */
function plaatscrum_db_check_version() {

   // Create database if needed	
   $sql = "select 1 FROM config limit 1" ;
   $result = plaatscrum_db_query($sql);
   if (!$result) {
		$version="1.1";
      plaatscrum_db_execute_sql_file($version);
   }

   // Path database if needed	
	$query = "SHOW TABLES LIKE 'config'";
	$result = plaatscrum_db_query($query);
   if (plaatscrum_db_num_rows($result)==0) {
		$version="1.2";
      plaatscrum_db_execute_sql_file($version);		
   }
	
	$version = plaatscrum_db_config_get("database_version");
	
	// Path database if needed	
   if ($version=="1.2") {
		$version="1.3";
      plaatscrum_db_execute_sql_file($version);
   }
}


/*
** -----------------
** CONFIG
** -----------------
*/

function plaatscrum_db_config($token) {
	
	$query  = 'select id, token, category, value, options, last_update, readonly from config where token="'.$token.'"';	
		
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	return $data;	
}

function plaatscrum_db_config_get($token) {
	
	$query  = 'select value from config where token="'.$token.'"';	
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	$value = "";
	if (isset($data->value)) {
		$value = $data->value;	
	}
	return $value;
}

function plaatscrum_db_config_update($data) {
		
	$query  = 'update config set '; 
	$query .= 'value="'.$data->value.'", ';
	$query .= 'last_update="'.date("Y-m-d H:i:s").'" ';
	$query .= 'where id='.$data->id; 
	
	plaatscrum_db_query($query);
}

/*
** -----------------
** STORY
** -----------------
*/

function plaatscrum_db_story_count($project_id, $type) {

	$query = 'select count(story_id) as total from story where deleted=0 and project_id='.$project_id.' and type='.$type;
	
	$result = plaatscrum_db_query($query);
		
	$total=0;
	if ($data = plaatscrum_db_fetch_object($result)) {
		
		$total = $data->total;
	}	
	return $total;
}

function plaatscrum_db_story_ref_count($story_id, $status) {

	$query  = 'select count(story_id) as total from story where deleted=0 and story_story_id='.$story_id.' ';
	if ($status!=STATUS_NONE) {
		$query .= 'and status='.$status;
	}
	
	$result = plaatscrum_db_query($query);
	
	$total=0;
	if ($data = plaatscrum_db_fetch_object($result)) {
		$total = $data->total;
	}	
	return $total;
}

function plaatscrum_db_story_points_count($story_id) {

	$query  = 'select ifnull(sum(points),0) as total from story where deleted=0 and story_story_id='.$story_id;
	
	$result = plaatscrum_db_query($query);
	
	$value=0;
	if ($data = plaatscrum_db_fetch_object($result)) {
		$value = $data->total;
	}	
	
	return $value;
}

function plaatscrum_db_story_unique($project_id) {
		
	$query  = 'select number from story where project_id='.$project_id.' order by number desc';
	
	$number=1;
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	if (isset ($data->number)) {	
		$number = ++$data->number;
	}	
	
	return $number;
}

function plaatscrum_db_story_number_check($number, $project_id) {
	
	$id=0;
	
	if (strlen($number)>0) {
		$query  = 'select story_id, type from story where number='.$number.' and project_id='.$project_id;
			
		$result = plaatscrum_db_query($query);
		$data = plaatscrum_db_fetch_object($result);
		
		if (isset($data->story_id)) {	
			$id = $data->story_id;
		}	
	}
	
	return $id;
}

function plaatscrum_db_story_check($number, $project_id) {
	
	$id=0;
	
	if (strlen($number)>0) {
		$query  = 'select story_id, type from story where number='.$number.' and project_id='.$project_id;
		
		$result = plaatscrum_db_query($query);
		$data = plaatscrum_db_fetch_object($result);
		if (isset($data->story_id) && ($data->type==TYPE_STORY)) {	
			$id = $data->story_id;
		}	
	}
	
	return $id;
}

function plaatscrum_db_story_delete($story_id) {
		
	$query  = 'update story set deleted=1 where story_id='.$story_id;
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_story_insert($number, $summary, $description, $status, $points, $reference, $sprint_id, $project_id, $user_id, $date, $prio, $type, $story_story_id) {

	$query  = 'insert into story (number, summary, description, status, points, reference, sprint_id, project_id, user_id, date, deleted, prio, type, story_story_id) ';
	$query .= 'values ('.$number.',"'.plaatscrum_db_escape($summary).'","'.plaatscrum_db_escape($description);
	$query .= '",'.$status.','.$points.',"'.plaatscrum_db_escape($reference).'",'.$sprint_id.','.$project_id.','.$user_id.',';
	$query .= '"'.$date.'",0 ,'.$prio.','.$type.','.$story_story_id.')';
		
	plaatscrum_db_query($query);
		
	$id = plaatscrum_db_story_number_check($number, $project_id);
	
	return $id;
}

function plaatscrum_db_story_update($data) {
		
	$query  = 'update story set '; 
	$query .= 'number='.$data->number.', ';
	$query .= 'summary="'.plaatscrum_db_escape($data->summary).'", ';
	$query .= 'description="'.plaatscrum_db_escape($data->description).'", ';
	$query .= 'status='.$data->status.', ';
	$query .= 'points='.$data->points.', ';	
	$query .= 'reference="'.plaatscrum_db_escape($data->reference).'", ';
	$query .= 'deleted='.$data->deleted.', ';
	$query .= 'date="'.$data->date.'", ';
	$query .= 'sprint_id='.$data->sprint_id.', ';
	$query .= 'user_id='.$data->user_id.', ';
	$query .= 'project_id='.$data->project_id.', ';
	$query .= 'prio='.$data->prio.', ';
	$query .= 'type='.$data->type.', ';
	$query .= 'story_story_id='.$data->story_story_id.' ';
	$query .= 'where story_id='.$data->story_id; 
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_story($story_id) {
	
	$query  = 'select story_id, number, summary, description, status, points, reference, deleted, sprint_id, project_id, ';
	$query .= 'user_id, date, prio, type, story_story_id ';
	$query .= 'from story where story_id='.$story_id;	
	
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	return $data;
}

/*
** -----------------
** HISTORY
** -----------------
*/

function plaatscrum_db_history_insert($story_id, $user_id, $status_old, $status_new) {

	$query  = 'insert into history (story_id, user_id, status_old, status_new, date) ';
	$query .= 'values ('.$story_id.','.$user_id.','.$status_old.','.$status_new.',"'.date("Y-m-d H:i:s", time()).'")';

	if ($story_id>0) {
			
		plaatscrum_db_query($query);			
	} 
}

function plaatscrum_db_history($history_id) {
	
	$query  = 'select story_id, history_id, user_id, status_old, status_new, date from history where history_id='.$history_id;	
	
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	return $data;
}

/*
** -----------------
** SPRINT
** -----------------
*/

function plaatscrum_db_sprint_check($number, $project_id) {
	
	$query  = 'select sprint_id from sprint where number="'.$number.'" and project_id='.$project_id;	
		
	$result = plaatscrum_db_query($query);
	
	$sprint_id=0;
	if ($data = plaatscrum_db_fetch_object($result)) {
		$sprint_id=$data->sprint_id;
	}
	
	return $sprint_id;
}

function plaatscrum_db_sprint_first($project_id) {
		
	$query  = 'select sprint_id from sprint where project_id='.$project_id.' ';
	$query .= 'order by sprint_id limit 1';
	
	$result = plaatscrum_db_query($query);
	
	$id = 0;
	if ($data = plaatscrum_db_fetch_object($result)) {
		$id = $data->sprint_id;
	}
	
	return $id;
}

function plaatscrum_db_sprint_delete($sprint_id) {
		
	$query = 'update sprint set deleted=1 where sprint_id='.$sprint_id;
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_sprint_insert($number, $description, $start_date, $end_date, $project_id, $release_id, $locked) {

	$query  = 'insert into sprint (number, description, start_date, end_date, project_id, release_id, locked, deleted) ';
	$query .= 'values ('.$number.',"'.plaatscrum_db_escape($description).'","'.$start_date.'","'.$end_date;
	$query .= '",'.$project_id.','.$release_id.','.$locked.',0)';
			
	plaatscrum_db_query($query);
}
		
function plaatscrum_db_sprint_update($data) {
		
	$query  = 'update sprint set '; 
	$query .= 'number='.$data->number.', ';
	$query .= 'description="'.plaatscrum_db_escape($data->description).'", ';	
	$query .= 'start_date="'.$data->start_date.'", ';
	$query .= 'end_date="'.$data->end_date.'", ';
	$query .= 'project_id='.$data->project_id.', ';
	$query .= 'release_id='.$data->release_id.', ';
	$query .= 'locked='.$data->locked.' ';
	$query .= 'where sprint_id='.$data->sprint_id; 
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_sprint($sprint_id) {
	
	$query  = 'select sprint_id, number, description, start_date, end_date, project_id, release_id, ';
	$query .= 'deleted, locked ';
	$query .= 'from sprint where sprint_id='.$sprint_id;	
		
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	return $data;
}

/*
** -----------------
** PROJECT
** -----------------
*/

function plaatscrum_db_project_unique($name) {
		
	$query  = 'select project_id from project where deleted=0 and name="'.$name.'"';
	
	$project_id=0;
	
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	if (isset ($data->project_id)) {	
		$project_id = $data->project_id;
	}	
	
	return $project_id;
}
	
function plaatscrum_db_project_delete($project_id) {
		
	$query = 'update project set deleted=1 where project_id='.$project_id;
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_project_insert($name, $public, $days, $history) {

	$query  = 'insert into project (name, public, days, history, deleted) ';
	$query .= 'values ("'.plaatscrum_db_escape($name).'",'.$public.',"'.plaatscrum_db_escape($days).'",'.$history.',0)';
	
	plaatscrum_db_query($query);
	
	$project_id = plaatscrum_db_project_unique($name);
	return $project_id;
}

function plaatscrum_db_project_update($data) {
		
	$query  = 'update project set '; 
	$query .= 'name="'.plaatscrum_db_escape($data->name).'", ';
	$query .= 'public='.$data->public.', ';
	$query .= 'days="'.plaatscrum_db_escape($data->days).'", ';
	$query .= 'history='.$data->history.' ';
	$query .= 'where project_id='.$data->project_id; 
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_project($project_id) {
	
	$query  = 'select project_id, name, public, days, history, deleted ';
	$query .= 'from project where deleted=0 and project_id='.$project_id;
	
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	return $data;
}

/*
** -----------------
** RELEASE
** -----------------
*/

function plaatscrum_db_release_unique_name($name) {
	
	/* input */
	global $user;
	
	$query  = 'select release_id from released where deleted=0 and name="'.$name.'" and project_id='.$user->project_id;	
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	$release_id=0;
	if (isset($data->release_id)) {
		$release_id = $data->release_id;
	}
	return $release_id;
}

function plaatscrum_db_release_delete($release_id) {
		
	$query = 'update released set deleted=1 where release_id='.$release_id;
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_release_insert($name, $note, $project_id) {

	$query  = 'insert into released (project_id, name, note, deleted) values (';
	$query .= $project_id.',"'.plaatscrum_db_escape($name).'","'.plaatscrum_db_escape($note).'",0)';
			
	plaatscrum_db_query($query);
}

function plaatscrum_db_release_update($data) {
		
	$query  = 'update released set '; 
	$query .= 'name="'.plaatscrum_db_escape($data->name).'", ';
	$query .= 'note="'.plaatscrum_db_escape($data->note).'", ';
	$query .= 'project_id='.$data->project_id.' ';
	$query .= 'where release_id='.$data->release_id; 
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_release($release_id) {
	
	$query  = 'select release_id, project_id, name, note, deleted from released where deleted=0 and release_id='.$release_id;
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
		
	return $data;
}

/*
** -----------------
** MEMBER
** -----------------
*/

function plaatscrum_db_member_id($username, $password) {

	$member_id=0;

	$query  = 'select member_id, password from member where username="'.$username.'"' ;	
		
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	if (isset($data->member_id)) {	
		
		if (plaatscrum_password_verify($password, $data->password)) {
			$member_id = $data->member_id;
			
		}
	}	
	
	return $member_id;
}

function plaatscrum_db_member_insert($username, $password) {

	$query  = 'insert into member (username, password, user_id, last_login, last_activity, requests, deleted) ';
	$query .= 'values ("'.plaatscrum_db_escape($username).'","'.plaatscrum_password_hash($password).'", 0, sysdate(), sysdate(), 0, 0)';
	plaatscrum_db_query($query);
		
	/* user_id = member_id */
	$member_id = plaatscrum_db_member_id($username, $password);	

	$query  = 'update member set user_id='.$member_id.' where member_id='.$member_id; 
	plaatscrum_db_query($query);
				
	return $member_id;
}

function plaatscrum_db_member_update2($username, $password, $member_id) {
		
	$query  = 'update member set '; 
	$query .= 'username="'.plaatscrum_db_escape($username).'" ';
	
	if (strlen($password)>0) {
		$query .= ', password="'.plaatscrum_password_hash($password).'" ';
	}

	$query .= 'where member_id='.$member_id; 
	
	plaatscrum_db_query($query);
}
	
function plaatscrum_db_member_update($data) {
		
	$query  = 'update member set '; 
	$query .= 'deleted='.$data->deleted.', ';
	$query .= 'requests='.$data->requests.', ';	
	$query .= 'last_activity="'.$data->last_activity.'", ';
	$query .= 'last_login="'.$data->last_login.'" ';
	$query .= 'where member_id='.$data->member_id; 
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_member_username($username) {
	
	$query  = 'select member_id from member where username="'.$username.'"';	
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	$member_id=0;
	if (isset($data->member_id)) {
		$member_id = $data->member_id;
	}
	return $member_id;
}

function plaatscrum_db_member($member_id) {
	
	$query  = 'select member_id, user_id, username, requests, last_login, last_activity, deleted ';
	$query .= 'from member where member_id='.$member_id;	
		
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	return $data;
}

/*
** -----------------
** USER
** -----------------
*/

function plaatscrum_db_user_check($name) {

	$query  = 'select user_id from tuser where name="'.$name.'"';	
		
	$result = plaatscrum_db_query($query);
	
	$user_id=0;
	if ($data = plaatscrum_db_fetch_object($result)) {
		$user_id=$data->user_id;
	}
	
	return $user_id;	
}

function plaatscrum_db_user_insert($user_id, $name, $email, $role_id) {

	$query  = 'insert into tuser (user_id, name, email, valid, role_id, project_id, sprint_id, menu_id, page_id, language) ';
	$query .= 'values ('.$user_id.',"'.$name.'","'.plaatscrum_db_escape($email).'",0,'.$role_id.',0,0,0,0,0)';
			
	plaatscrum_db_query($query);	
}
	
function plaatscrum_db_user_update($user) {
		
	$query  = 'update tuser set '; 
	$query .= 'name="'.plaatscrum_db_escape($user->name).'", ';
	$query .= 'email="'.plaatscrum_db_escape($user->email).'", ';		
	$query .= 'valid='.$user->valid.', ';		
	$query .= 'role_id='.$user->role_id.', ';
	$query .= 'project_id='.$user->project_id.', ';
	$query .= 'sprint_id='.$user->sprint_id.', ';
	$query .= 'menu_id='.$user->menu_id.', ';
	$query .= 'page_id='.$user->page_id.', ';
	$query .= 'language='.$user->language.' ';
	$query .= 'where user_id='.$user->user_id; 
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_user_email($email) {
	
	$query  = 'select user_id from tuser where email="'.$email.'"';	
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	$user_id=0;
	if (isset($data->user_id)) {
		$user_id = $data->user_id;
	}
	return $user_id;
}

function plaatscrum_db_user($user_id) {
	
	$query  = 'select user_id, role_id, name, email, valid, project_id, sprint_id, menu_id, page_id, ';
	$query .= 'language from tuser where user_id='.$user_id;	
		
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	return $data;
}

/*
** ---------------------
** SESSION
** ---------------------
*/

function plaatscrum_db_session_add($member_id) {
		
	/* First delete all old session */
	$query  = 'delete from session where member_id='.$member_id;	
	plaatscrum_db_query($query);  
		
	/* Create new session */
	$query  = 'insert into session (member_id, session, date) values ('.$member_id.',"","'.date("Y-m-d H:i:s").'")';	
	plaatscrum_db_query($query);
	
	/* Return new session entry */
	$query  = 'select session_id from session where member_id='.$member_id;
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	/* created unique session id */
	$tmp = md5($data->session_id);
	
	/* Update session state */
	$query  = 'update session set session = "'.$tmp.'" where session_id='.$data->session_id; 
	plaatscrum_db_query($query);
	
	return $tmp;
}

function plaatscrum_db_session_valid( $session ) {
	
	/* Session expires after 10 minutes */
	$expired = 600;
	
	if (strlen($session)==0) {
		return 0;
	}
	
	$query  = 'select session_id, member_id, session, date from session where session="'.$session.'"';
	$result = plaatscrum_db_query($query);
	
	if ($data=plaatscrum_db_fetch_object($result)) {
		
		if (((time()-strtotime($data->date))>$expired)) {
				
			plaatscrum_db_session_delete($data->session);

		} else {
	
			/* Update session state */
			$query  = 'update session set date = "'.date("Y-m-d H:i:s").'" where session="'.$session.'"'; 
			plaatscrum_db_query($query);
			
			return $data->member_id;
		}
	}
	return 0;
}

function plaatscrum_db_session_delete($session) {
	
	$query = 'delete from session where session="'.$session.'"';
	
	plaatscrum_db_query($query); 
}

/**
 * hack a player session for debug reasons (Admin functionality)
 */
function plaatscrum_db_session_hack($member_id) {

	$query  = 'select session from session where member_id='.$member_id;
	$result = plaatscrum_db_query($query);
	
	if ($data = plaatscrum_db_fetch_object($result)) {
		return $data->session;
	} else {
		return plaatscrum_db_session_add($member_id);
	}
}

/*
** -----------------
** PROJECT_USER
** -----------------
*/

function plaatscrum_db_project_user_delete($project_id, $user_id) {
		
	$query = 'delete from project_user where project_id='.$project_id.' and user_id='.$user_id;
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_project_user_update($data) {
		
	$query  = 'update project_user set '; 
	$query .= 'role_id='.$data->role_id.', ';
	$query .= 'bcr='.$data->bcr.' ';
	$query .= 'where project_id='.$data->project_id.' and user_id='.$data->user_id;
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_project_user_insert($project_id, $user_id, $role_id, $bcr) {

	$query  = 'insert into project_user (project_id, user_id, role_id, bcr) values ';
	$query .= '('.$project_id.','.$user_id.','.$role_id.','.$bcr.')';
			
	plaatscrum_db_query($query);
}

function plaatscrum_db_project_user($project_id, $user_id) {
	
	$query  = 'select user_id, project_id, role_id, bcr from project_user ';
	$query .= 'where project_id='.$project_id.' and user_id='.$user_id;	
	
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	return $data;
}

/*
** -----------------
** ROLE
** -----------------
*/

function plaatscrum_db_role($role_id) {
	
	$query  = 'select role_id, project_edit, story_add, story_edit, story_delete, story_import, story_export ';
	$query .= 'from role where role_id='.$role_id;	
		
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	return $data;
}

/*
** ---------------------
** CRON
** ---------------------
*/

function plaatscrum_db_cron_update($id) {
		
	/* Update member */
	$query  = "update cron set "; 
	$query .= 'last_run = "'.date("Y-m-d H:i:s").'" ';
	$query .= "where cron_id=".$id; 
	
	plaatscrum_db_query($query);
}

/*
** ---------------------
** FILTER
** ---------------------
*/	
	
function plaatscrum_db_filter_update($data) {
		
	$query  = 'update filter set '; 
	$query .= 'user_id='.$data->user_id.', ';		
	$query .= 'project_id='.$data->project_id.', ';
	$query .= 'page_id='.$data->page_id.', ';		
	$query .= 'status="'.$data->status.'", ';		
	$query .= 'prio="'.$data->prio.'", ';
	$query .= 'type="'.$data->type.'", ';
	$query .= 'owner='.$data->owner.' ';	
	$query .= 'where filter_id='.$data->filter_id; 
	
	plaatscrum_db_query($query);
}

function plaatscrum_db_filter($user_id, $project_id, $page_id) {
	
	$query  = 'select filter_id, project_id, user_id, page_id, status, prio, type, owner from filter ';
	$query .= 'where user_id='.$user_id.' and project_id='.$project_id.' and page_id='.$page_id;	
		
	$result = plaatscrum_db_query($query);
	$data = plaatscrum_db_fetch_object($result);
	
	return $data;
}

function plaatscrum_db_filter_insert($user_id, $project_id, $page_id, $status, $prio, $type, $owner ) {

	$query  = 'insert into filter ( user_id, project_id, page_id, status, prio, type, owner) values (';
	$query .= $user_id.','.$project_id.','.$page_id.',"'.$status.'","'.$prio.'","'.$type.'",'.$owner.')';
	
	plaatscrum_db_query($query);
}

/*
** ---------------------
** LOG
** ---------------------
*/	

function plaatscrum_db_log_insert($user_id, $category, $description ) {

	$query  = 'insert into log ( timestamp, user_id, category, address, description) values (';
	$query .= '"'.date("Y-m-d H:i:s").'",'.$user_id.','.$category.',"'.$_SERVER["REMOTE_ADDR"];
	$query .= '","'.plaatscrum_db_escape($description).'")';
	
	plaatscrum_db_query($query);
}

/*
** ---------------------
** THE END
** ---------------------
*/

?>
