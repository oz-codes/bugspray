<?php
/**
 * bugspray issue tracking software
 * Copyright (c) 2009-2010 a2h - http://a2h.uni.cc/
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
$page->setType('tickets');
$page->setTitle('Tickets');

// current status
$curstatus = $_GET['status'];

// restrict the status
$whereclause = '';
if ($curstatus != 'all')
{
	if (!isset($_GET['status']))
		$curstatus = 'open';
	
	switch ($curstatus)
	{
		case 'open': $whereclause = 'WHERE issues.status = 1 OR issues.status = 2'; break;
		case 'unassigned': $whereclause = 'WHERE issues.status = 1'; break;
		case 'resolved': $whereclause = 'WHERE issues.status = 3'; break;
		//case 'postponed': $whereclause = 'WHERE issues.status = 4'; break;
		case 'declined': $whereclause = 'WHERE issues.status = 5'; break;
		case 'all': $whereclause = ''; break;
	}
}

// status tabs
$status_tabs = array(
	array(
		'name' => 'Open',
		'url' => 'tickets.php',
		'sel' => $curstatus == 'open' ? true : false
	),
	array(
		'name' => 'Unassigned',
		'url' => 'tickets.php?status=unassigned',
		'sel' => $curstatus == 'unassigned' ? true : false
	),
	array(
		'name' => 'Assigned',
		'url' => 'tickets.php?status=assigned',
		'sel' => $curstatus == 'assigned' ? true : false
	),
	array(
		'name' => 'Resolved',
		'url' => 'tickets.php?status=resolved',
		'sel' => $curstatus == 'resolved' ? true : false
	),
	array(
		'name' => 'Declined',
		'url' => 'tickets.php?status=declined',
		'sel' => $curstatus == 'declined' ? true : false
	),
	array(
		'name' => 'All',
		'url' => 'tickets.php?status=all',
		'sel' => $curstatus == 'all' ? true : false
	)
);

$result_issues = db_query_toarray("
	SELECT issues.*, comments.author AS commentauthor FROM issues LEFT JOIN comments ON comments.issue = issues.id AND comments.when_posted = issues.when_updated $whereclause ORDER BY issues.when_updated DESC
", false, 'Retrieving a list of issues');

// extra variables
$count = count($result_issues);
for ($i=0;$i<$count;$i++)
{
	// is the issue favoUrited? (db uses "favorite" because everyone favoUrs the americans)
	$result_issues[$i]['favorite'] = $client['is_logged'] ? in_array($result_issues[$i]['id'], getufavs($_SESSION['uid'])) : false;
	
	// determine the colour of the listing (!!!!!!!!!!!!!!!!!!!move into template?)
	$result_issues[$i]['status_color'] = issuecol($result_issues[$i]['status'], $result_issues[$i]['severity']);
}

$page->setPage(
	'issue_list.php',
	array(
		'status_tabs' => $status_tabs,
		'issues' => $result_issues
	)
);
?>