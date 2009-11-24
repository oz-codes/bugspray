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
$page->setType('activity');
$page->setTitle('Activity');
?>
<h2>Recent activity</h2>

<b>Logging has only been implemented for adding issues right now.</b><br /><br />

<?php
$result_projects = db_query("SELECT * FROM projects");
while ($project = mysql_fetch_array($result_projects))
{
	echo '<h3><a href="#">'.$project['name'].'</a> <a href="#"><img src="img/feed.png" alt="RSS feed" /></a></h3>';
	
	$i=0;
	$result_issues = db_query("SELECT * FROM issues WHERE project = '".$project['id']."'");
	while ($issue = mysql_fetch_array($result_issues))
	{				
		$result_log_issues = mysql_query("SELECT * FROM log_issues WHERE issue = '".$issue['id']."'");
		while ($log_issue = mysql_fetch_array($result_log_issues))
		{
			$log_entry[$i] = array(
				'when_occured' => strtotime($log_issue['when']),
				'actiontype' => $log_issue['actiontype'],
				'userid' => $log_issue['userid'],
				'category' => $issue['category'],
				'name' => $issue['name']
			);
			$i+=1;
		}
	}
	
	if (isset($log_entry))
	{
		usort($log_entry,'logwhencmp');
		echo '<table class="watchlisttbl" cellspacing="0">';
		for ($i=0;$i<sizeof($log_entry);$i++)
		{
			$u = getunm($log_entry[$i]['userid']);
			if ($u == $_SESSION['username'])
				$u = '<b>You</b>';
			else
				$u = '<a href="profile.php?u='.$log_entry[$i]['userid'].'">'.$u.'</a>';

			echo '
				<tr>
					<td>
						<img src="img/act/'.getactimg($log_entry[$i]['actiontype']).'" alt="" />
					</td>
					<td>
						<div class="avatar">
							<img src="'.getav($log_entry[$i]['userid']).'" alt="" />
						</div>
					</td>
					<td>
						'.$u.'
						
						<span style="background:#'.getactcol($log_entry[$i]['actiontype']).'">'.getactlogdsc($log_entry[$i]['actiontype']).'</span>
						
						categorised under
						
						<a href="#">'.getcatnm($log_entry[$i]['category']).'</a>,
						
						called <a href="#">'.$log_entry[$i]['name'].'</a>
					</td>
				</tr>';
		}
		echo '</table>';
	}
	else
	{
		echo 'Nothing...';
	}
}
?>