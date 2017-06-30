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

$releasenotes = '

<h2>19-12-2016 PlaatScrum v1.4</h2>
<ul>
<li>Add new version available check.</li>
<li>Bugfix: Downchart day calculation improved.</li>
</ul>

<h2>21-10-2016 PlaatScrum v1.3</h2>
<ul>
<li>Admin user can now manage user events.</li>
<li>Admin user can now manage SQL backup files.</li>
<li>SQL backup file have now random filename. More secure!</li>
<li>Move event logging to database. More secure!</li>
<li>Filter settings are now managed per project and page.</li>
<li>Added multi select priority filter feature.</li>
<li>Added priority column to sprint  and product backlog.</li>
<li>Added calender sprint letter for each week that sprint takes.</li>
<li>Added story sorting (asc/desc) on taskboard.</li>
<li>Current day is highlighted on calender.</li>
<li>Redesign login page and page banner.</li>
<li>Bugfix: Velocity chart is now showing correct values.</li>
<li>Bugfix: Sprint locked column is now working again.</li>
<li>Bugfix: Default value of story ref_id is now correctly set.</li>
<li>Bugfix: Assign button on backlog form is now working again.</li>
<li>Bugfix: If session expired redirect to login page is now working correct.</li>
<li>Bugfix: Now all columns on taskboard layout have same size.</li>
<li>Bugfix: Burndown chart, both lines start now with same value.</li>
</ul>

<h2>15-10-2016 PlaatScrum v1.2</h2>
<ul>
<li>Added automatic database creation and patching</li>
<li>Make PHP code compliant with PHP version 7.x and higher.</li>
<li>Make SQL queries compliant with MySQL version 5.7 and higher.</li>
<li>Protect PHP source code for direct, not compiled access.</li>
<li>Protect all directory against direct URL access.</li>
<li>Improve layout of help pages.</li>
<li>Added Trivial and Blocker priority.</li>
<li>Improve password encryption algoritme.</li>
<li>If user is idle for 10 minutes the user is automatic logout.</li>
<li>Added icons for andriod and apple devices.</li>
<li>Improve a lot of other small things.</li>
<li>Bugfix: Fix several small issues.</li>
</ul>

<h2>01-01-2016 PlaatScrum v1.1</h2>
<ul>
<li>Update copyright footer.</li>
<li>Update copyright banner in source code.</li>
<li>Added phpdoc to project.</li>
<li>Move source code from Google code to GitHub.</li>
</ul>

<h2>28-11-2013 PlaatScrum v1.0</h2>
<ul>
<li>Improve default sorting of backlog</li>
<li>Improve backup cron. Now 25% smaller output and 50% faster.</li>
<li>Update jquery to 10.2.0.</li>
<li>Update jquery-ui to 10.3.0.</li>
<li>update jquery-css smooth to 10.3.0.</li>
<li>Added source code to google code.</li>
</ul>

<h2>16-08-2012 PlaatScrum v0.9</h2>
<ul>
<li>Improve home page. Now only assigned tasks, bugs and epics and showed default.</li>
<li>Improve taskboard sorting when no sprint is selected.</li>
<li>Improve resource view</li>
<li>Cost information is now only available for scrum master role.</li>
<li>Update some text labels.</li>
<li>Bug fix: Story owner is not lost during update with role "Team Member".</li>
<li>Bug fix: Logout action destroys now correct user session.</li>
</ul>

<h2>25-06-2012 PlaatScrum v0.8</h2>
<ul>
<li>Created new URL scrum.plaatsoft.nl</li>
<li>Move site to new VPS platform. Performance boost with factor 3!</li> 
<li>Improve intro page.</li>
<li>Added lock option sprint, to freeze sprint data set.</li>
<li>Added cost board page.</li>
<li>Added Basic Cost Rate to user project assignment page.</li>
<li>Added automatic database backup based on poormans cron job concept.</li>
<li>Bug fix: Login, register and recover page support now enter key</li>
</ul>

<h2>09-06-2012 PlaatScrum v0.7</h2>
<ul>
<li>Added email confirmation functionality.</li>
<li>Save / Assign / Drop popup message contain now a link back to the detail page.</li>
<li>Story points are now based on related task, bugs, epic work input.</li>
<li>New stories are now automatic assigned to Scrum Master.</li>
<li>Added optional (project setting) history information.</li>
<li>Added resource board page.</li>
<li>Added amount of queries needed to render to page.</li>
<li>Story, Task, Bug and Epic date is now validated against project working days.</li>
<li>Tasks, Bugs and Epics can now only be created with a story reference.</li>
<li>Added workdays to project.</li>
<li>Added xml and json export.</li>
</ul>

<h2>23-05-2012 PlaatScrum v0.6</h2>
<ul>
<li>Added more filter options to calender chart.</li>
<li>Make sql code compliant with mysql and postgres database engine.</li>
<li>Isolated mysql database api calls</li>
<li>Improve page navigation.</li>
<li>Added automatic story status update when related task, bug, epic status is updated.</li>
<li>Added link to related story at task, bug and epic page.</li>
<li>Bug fix: Sprint duration on project detail page is now correct!</li>
<li>Bug fix: Week number in date picker is now correct!</li>
</ul>

<h2>10-05-2012 PlaatScrum v0.5</h2>
<ul>
<li>Added php source encryption option.</li>
<li>Added audit trail to monitor user activities.</li>
<li>Added public access (read-only view ) for projects.</li>
<li>Improve role access rights model</li>
<li>Settings - Project menu now available for all users</li>
<li>Added review status.</li>
<li>Added multi language support.</li>
<li>Added backlog menu item.</li>
<li>Optimize story detail page.</li>
<li>Add task overview to story.</li>
<li>Bug fix: Charts are now no cache anymore client site.</li>
<li>Bug fix: Export is working again!</li>
</ul>

<h2>05-05-2012 PlaatScrum v0.4</h2>
<ul>
<li>Optimize filter code use</li>
<li>Added velocity chart.</li>
<li>Added jquery date picker and multi select control.</li>
<li>Improve role base access.</li>
<li>Improve import and export functionality.</li>
<li>Improve search box.</li>
<li>Improve menu location on screen.</li>
<li>Added meta search engine information</li>
<li>Improve directory structure</li>
<li>Added page titles.</li>
<li>Added basic calender page.</li>
<li>User can now change account settings.</li>
<li>Added status chart.</li>
<li>Added optimal line to burndown chart.</li>
<li>Improve GUI layout.</li>
<li>Bug fix: Solved several small bugs.</li>
</ul>

<h2>27-04-2012 PlaatScrum v0.3</h2>
<ul>
<li>Added basic burn down chart.</li>
<li>Added story status change in history table.</li>
<li>Added create unique story number when new story is created.</li>
<li>Added story number double number check.</li>
<li>Added guest role.</li>
<li>Added new css style sheet.</li>
<li>Added scrum master, product owner, team member role.</li>
<li>Added password recover page.</li>
<li>Added registration page.</li>
<li>Added dropdown feature to main menu.</li>
<li>Improve filter options.</li>
</ul>

<h2>20-04-2012 PlaatScrum v0.2</h2>
<ul>
<li>Added background image.</li>
<li>Encrypt user password in database.</li>
<li>Added logical user deletion.</li>
<li>Added assign / drop user story.</li>
<li>Added event en page defines.</li>
<li>Added banner.</li>
<li>Added login / logout page.</li>
<li>Added setting -> user story CSV import.</li>
<li>Added setting -> user story CSV export.</li>
<li>Added setting -> default project.</li>
<li>Added help -> release notes page.</li>
<li>Added help -> about page.</li>
<li>Added help -> donate page.</li>
<li>Added help -> credit page.</li>
</ul>

<h2>13-04-2012 PlaatScrum v0.1</h2>
<ul>
<li>Added session manager.</li>
<li>Encrypt hyperlinks.</li>
<li>Added basic stories page.</li>
<li>Added basic scrumboard view.</li>
<li>Added setting -> users page.</li>
<li>Added setting -> projects page.</li>
<li>Added css file.</li>
<li>Added login page.</li>
<li>Setup development framework.</li>
</ul>';

/*
** ------------------
**  FORMS
** ------------------
*/

function plaatscrum_release_notes_form() {

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
	
	$page .= '<div id="column">';
   $page .= '<img class="imgr" src="images/info.svg" width="256" height="256" alt="" />';
	$page .= '</div>';	
	
	$page .= '<br class="clear" />';
}

/*
** ------------------
** HANDLER
** ------------------
*/

function plaatscrum_release_notes() {

	/* input */
	global $pid;
		
	switch ($pid) {
					  
		case PAGE_RELEASE_NOTES: 
					plaatscrum_release_notes_form();
					break;
	}
}

/*
** ------------------
** The End
** ------------------
*/

?>