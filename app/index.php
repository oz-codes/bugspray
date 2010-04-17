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
$page->setType('dashboard');
$page->setTitle('Dashboard');

// installer completion screen
if (isset($_GET['installerdone']) && is_dir('install'))
{
	$page->addCSS('install/installer.css');
	include("install/index.php");
}

if ($client['is_logged'])
{
	$result_issues = db_query_toarray("
		SELECT issues.*, comments.author AS commentauthor, favorites.userid AS favorited FROM issues
		LEFT JOIN comments ON comments.issue = issues.id AND comments.when_posted = issues.when_updated
		LEFT JOIN favorites ON favorites.ticketid = issues.id
		WHERE issues.status < 3 AND (favorites.userid = {$_SESSION['uid']} OR issues.assign = {$_SESSION['uid']})
		ORDER BY issues.when_updated DESC
	", false, 'Retrieving a list of issues');

	// extra variables
	$count = count($result_issues);
	for ($i=0;$i<$count;$i++)
	{
		// is the issue favoUrited? (db uses "favorite" because everyone favoUrs the americans)
		$result_issues[$i]['favorite'] = $result_issues[$i]['favorited'] ? true : false;
		
		// determine the colour of the listing (!!!!!!!!!!!!!!!!!!!move into template?)
		$result_issues[$i]['status_color'] = issuecol($result_issues[$i]['status'], $result_issues[$i]['severity']);
	}
}

$page->setPage(
	'dashboard.php',
	array(
		'issues' => $result_issues
	)
);
?>