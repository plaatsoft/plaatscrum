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

/* Prevent images caching client site */

if( isset( $_GET['img'] ) && is_file( 'images/'.$_GET['img'] ) ) {

  $f = fopen( 'images/'.$_GET['img'], "r" );
  $img = fread($f, 100000);
  fclose($f);

  header("Expires: Sat, 01 Jul 2011 00:00:00 GMT"); 
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
  header("Cache-Control: no-store, no-cache, must-revalidate"); 
  header("Cache-Control: post-check=0, pre-check=0", false); 
  header("Pragma: no-cache"); 

  header('Accept-Ranges: bytes');
  header('Content-Length: '.strlen( $img )); 
  header('Content-Type: image/png'); 

  echo $img;
  exit();
}

?>
