<?php
/**
 * spray issue tracking software
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

include('sp-core.php');

$page->setType('statistics');
$page->setTitle('Statistics');

/*$projects = db_query_toarray("SELECT id, name FROM categories");

$i = 0;
foreach ($projects as $project)
{
	$result_logs = db_query("
		SELECT issues.tags as issuecat, issues.name as issuename, log_issues.when_occured as logwhen, log_issues.userid, log_issues.actiontype
		FROM issues
		RIGHT JOIN log_issues ON log_issues.issue = issues.id
		WHERE category = '{$project['id']}'
		ORDER BY log_issues.when_occured DESC
	");
	$j=0;
	while ($issue = mysql_fetch_array($result_logs))
	{				
		$projects[$i]['logs'][$j] = array(
			'when_occured' => strtotime($issue['logwhen']),
			'actiontype' => $issue['actiontype'],
			'userid' => $issue['userid'],
			'category' => $issue['issuecat'],
			'name' => $issue['issuename']
		);
		$j+=1;
	}
	$i+=1;
}

$page->include_template(
	'statistics.php',
	array(
		'projects' => $projects
	)
);*/

echo 'todo';
?>