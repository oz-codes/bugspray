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
?>

<h2 class="fl">Issue list</h2>
<div class="fr">
	<button type="button" onclick="location.href='add_issue.php'"><img src="img/btn/add.png" alt="" />Add an issue</button>
</div>
<div class="fc"></div>

<div class="ibox_alert">About the tabs... the "All" tab will be removed later on when the other tabs are implemented if there aren't some good reasons for it staying.</div>
<br />

<div class="tabs">
	<a href="#" class="sel">All</a>
	<a href="#" class="notyet">Open</a>
	<a href="#" class="notyet">Assigned</a>
	<a href="#" class="notyet">Resolved</a>
	<a href="#" class="notyet">Closed(?)</a>
	<div class="fc"></div>
</div>

<table class="issuelist_large">
<?php
// thanks to najmeddine for the mind-bursting sql http://stackoverflow.com/questions/1575673/mysql-limit-on-a-left-join
$result_issues = db_query("
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
 ORDER BY COALESCE(commentposted, issues.when_opened) DESC
");
while ($issue = mysql_fetch_array($result_issues))
{
	// last comment
	if ($issue['commentauthor'] > 0)
	{
		$lastcomment = '
		'.timeago($issue['commentposted']).'
		<a href="profile.php?u='.$issue['commentauthor'].'">'.getunm($issue['commentauthor'],false).'</a>';
	}
	else
	{
		$lastcomment = $issue['poster'].' N/A';
	}
	
	// the entry
	echo '
	<tr'.(issueclosed($issue['status']) ? ' class="closed"':'').'>
		<td class="comments">
			<div>'.$issue['num_comments'].'</div>
			<div>comment'.($issue['num_comments']==1?'':'s').'</div>
		</td>
		<td class="views">
			<div>?</div>
			<div>views</div>
		</td>
		<td class="main">
			<div class="upper">
				<div class="left">
					<a href="view_issue.php?id='.$issue['id'].'">'.$issue['name'].'</a>
					in <a href="#">'.getprojnm($issue['project']).'</a>
				</div>
				<div class="right">
					<b>tagged as</b> '.getcattag($issue['category']).'
				</div>
				<div class="fc"></div>
			</div>
			
			<div class="lower">
				<div class="left">
					'.getuinfo($issue['author']).'
				</div>
				<div class="right">
					<b>last</b> '.$lastcomment.'
				</div>
			</div>
		</td>
	</tr>';
}
?>
</table>