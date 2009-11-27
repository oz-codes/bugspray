<?php
/*
 * bugspray issue tracking software
 * Copyright (c) 2009 a2h - http://a2h.uni.cc/
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
 *
 */

include("functions.php");
$page->setType('issues');
$page->setTitle('Issue list');

// installer completion screen
if (isset($_GET['installerdone']) && is_dir('install'))
{
	$page->addCSS('install/installer.css');
	include("install/index.php");
}

// current status
$curstatus = $_GET['status'];

// restrict the status
$whereclause = '';
if ($curstatus != 'all')
{
	if (!isset($_GET['status']))
		$curstatus = 'open';
	
	$wherests = array();
	foreach (getstatuses() as $status)
	{
		if ($status['type'] == $curstatus)
		{
			$wherests[] = $status['id'];
		}
	}
	if (count($wherests) > 0)
	{
		$whereclause = 'WHERE (';
		$i = 0;
		foreach ($wherests as $st)
		{
			if ($i > 0)
				$whereclause .= ' OR';
			$whereclause .= ' issues.status = '.$st;
			$i++;
		}
		$whereclause .= ')';
	}
}

// status tabs
$status_tabs = array(
	array(
		'name' => 'Open',
		'url' => 'index.php',
		'sel' => $curstatus == 'open' ? true : false
	),
	array(
		'name' => 'Assigned',
		'url' => 'index.php?status=assigned',
		'sel' => $curstatus == 'assigned' ? true : false
	),
	array(
		'name' => 'Resolved',
		'url' => 'index.php?status=resolved',
		'sel' => $curstatus == 'resolved' ? true : false
	),
	array(
		'name' => 'Declined',
		'url' => 'index.php?status=declined',
		'sel' => $curstatus == 'declined' ? true : false
	),
	array(
		'name' => 'All',
		'url' => 'index.php?status=all',
		'sel' => $curstatus == 'all' ? true : false
	)
);

// thanks to najmeddine for the mind-bursting sql http://stackoverflow.com/questions/1575673/mysql-limit-on-a-left-join
$result_issues = db_query_toarray("
   SELECT issues.*, 
		  comments.author AS commentauthor, 
		  comments.when_posted AS commentposted
	 FROM issues
LEFT JOIN ( SELECT c1.issue, c1.author, c1.when_posted
			  FROM comments c1
		   JOIN
		   (SELECT c2.issue, max(c2.when_posted) AS max_when_posted           
			  FROM comments c2
		  GROUP BY issue) c3
			on c1.issue = c3.issue and c1.when_posted = c3.max_when_posted
		  ) AS comments ON issues.id=comments.issue
	$whereclause
 ORDER BY COALESCE(commentposted, issues.when_opened) DESC
");

// extra variables
$count = count($result_issues);
for ($i=0;$i<$count;$i++)
{
	// last comment
	if ($result_issues[$i]['commentauthor'] > 0)
	{
		$result_issues[$i]['lastcomment'] = '
		'.timeago($result_issues[$i]['commentposted']).'
		<a href="profile.php?u='.$result_issues[$i]['commentauthor'].'">'.getunm($result_issues[$i]['commentauthor'],false).'</a>';
	}
	else
	{
		$result_issues[$i]['lastcomment'] = $issue['poster'].' N/A';
	}
	
	// age
	if ($result_issues[$i]['commentauthor'] > 0)
	{
		$age = $result_issues[$i]['commentposted'];
	}
	else
	{
		$age = $result_issues[$i]['when_opened'];
	}
	
	// determine the colour of the listing
	$result_issues[$i]['status_color'] = issuecol($result_issues[$i]['status'],$result_issues[$i]['num_comments'],$age);
}

$page->setPage(
	'issue_list.php',
	array(
		'status_tabs' => $status_tabs,
		'issues' => $result_issues
	)
);
?>