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

echo "<b>Release PlaatScrum</b><br/>";

/* Enable php source code encryption */
$encrypt=1;

$encrypt_files = array( 
	"backlog.inc", 
	"board.inc", 
	"calender.inc", 
	"chart.inc", 
	"chart.php", 
	"cron.inc", 
	"config.inc",
	"database.inc", 
	"english.inc",
	"export.inc",
	"filter.inc",
	"general.inc", 
	"graph.php",
	"help.inc", 
	"home.inc",
	"import.inc", 
	"index.php",
	"login.inc",
	"menu.inc", 
	"nederlands.inc",
	"project.inc", 
	"release.inc", 
	"releasenotes.inc",	
	"story.inc",	
	"settings.inc",	
	"sprint.inc",
	"story.inc",
	"user.inc"	
	);

$copy_files = array( 
	"css/general.css", 
	"css/jquery.css", 
	
	"js/link.js", 
	"js/jquery-ui.js", 
	"js/jquery-multi.js", 
	"js/jquery.js"
	);

foreach ($copy_files as $file) {

	echo 'Copy '.$file.'<br/>';
	
	$in = fopen($file, 'r');
	$content = fread($in, filesize($file));
	fclose($in);
	
	$out = fopen('encrypt/'.$file, 'w'); 
	fwrite($out, $content);
	fclose($out);
}

foreach ($encrypt_files as $file) {

	echo 'Encrypt '.$file.'<br/>';
	
	$in = fopen($file, 'r');
	$content = fread($in, filesize($file));
	fclose($in);

	/* Remove php header but not xml header */
	
	$pos = strpos($content, "<?xml");
	$content = str_replace("<?php","", $content);
	if ($pos==0) {
		$content = str_replace("?>","", $content);
	}

	/* Rename variables */
	$content = trim($content);
	$content = str_replace('= ', '=', $content); 
	$content = str_replace(' =' , '=', $content); 
	$content = str_replace(' .=' , '.=', $content);
			
	/* Rename all function names */
	$content = str_replace('plaatscrum_', '_', $content); 
		
	/* Remove all enter and line feed characters. */
	$content = str_replace("\r\n", "", $content); 
	$content = str_replace("\n", "", $content); 
		
	/* Remove tabs characters */
	$content = str_replace(array("\t"), '', $content); 
	
	/* Remove source code comment lines */
	$content = preg_replace('!/\*.*?\*/!s', '', $content);
	$content = preg_replace('/\n\s*\n/', '', $content);

	/* Encrypt source code */
	if ($encrypt==1) {
		$content = base64_encode(gzdeflate($content));
		$content = 'eval(gzinflate(base64_decode("'.$content.'")));';
	
		/* Break lines every 165 characters */
		$content = preg_replace('/(.{120})/', "$1\r\n", $content);
	}

	/* Write encrypted file */
	$out = fopen('encrypt/'.$file, 'w'); 	
	$tmp  ="<?php\r\n";
	$tmp .= $content."\r\n";
	$tmp .= '?>';
	
	fwrite($out, $tmp);
	fclose($out);
}

echo '<b>Ready</b><br/>';

/*
** ---------------------------------------------------------------- 
** THE END
** ----------------------------------------------------------------
*/

?>