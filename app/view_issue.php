<?php
/**
 * bugspray issue tracking software
 * Copyright (c) 2009-2010 a2h
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Under section 7b of the GNU General Public License you are
 * required to preserve this notice. Additional attribution may be
 * found in the NOTICES.txt file provided with the Program.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

include("functions.php");
$page->setType('issues');

$id = escape_smart($_GET['id']);

$result_issues = db_query("SELECT * FROM issues WHERE id = '$id' LIMIT 1", "Retrieving info for issue $id from database");

if (mysql_num_rows($result_issues))
{
	$issue = mysql_fetch_array($result_issues);
	$page->setTitle($issue['name']);
	
	// add to views
	db_query("UPDATE issues SET num_views = num_views + 1 WHERE id = '$id'");
	
	// who is the issue assigned to?
	if ($issue['assign'] > 0)
	{
		$issue['assignedto'] = getuinfo($issue['assign']);
	}
	else
	{
		$issue['assignedto'] = 'nobody';
	}

	// get list of assignable users
	$assignsarr = array(array(-1,'nobody'),array(-1,'----------------------'));
	$result_userproject = db_query("SELECT * FROM assigns_userproject WHERE projectid = " . $issue['project'], "Retrieving assigned users for issue $id from database");
	while ($assign = mysql_fetch_array($result_userproject))
	{
		$enableme = $assign['userid'] == $issue['assign'] ? true : false;
		
		$assignsarr[] = array(
			$assign['userid'],
			getunm($assign['userid']),
			$enableme
		);
	}
	$issue['assigns'] = $assignsarr;
	
	// get the comments
	$result_comments = db_query_toarray("SELECT * FROM comments WHERE issue = $id ORDER BY when_posted DESC", "Retrieving comments for issue $id from database");
	
	// output the page
	$page->setPage(
		'issue_single.php',
		array(
			'issue' => $issue,
			'comments' => $result_comments
		)
	);
}
else
{
	$page->setTitle('Error');
	echo 'That issue doesn\'t exist!';
}
?>