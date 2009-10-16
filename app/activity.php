<?php
/*
 * bugspray
 * Copyright 2009 a2h - http://a2h.uni.cc/
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * http://a2h.github.com/bugspray/
 *
 */

include("functions.php");
template_top('log');

?>
<h2>Event log</h2>

<b>Unfortunately the logging itself hasn't been implemented, so this is somewhat static currently.</b><br /><br />

<?php
$result_projects = db_query("SELECT * FROM projects");
while ($project = mysql_fetch_array($result_projects))
{
	echo '<h3><a href="#">'.$project['name'].'</a> <a href="#"><img src="img/feed.png" alt="RSS feed" /></a></h3>';
	
	echo '<table class="watchlisttbl" cellspacing="0">';
		$i=0;
		$result_issues = db_query("SELECT * FROM issues WHERE project = '".$project['id']."'");
		while ($issue = mysql_fetch_array($result_issues))
		{				
			$result_log_issues = mysql_query("SELECT * FROM log_issues WHERE issue = '".$issue['id']."'");
			while ($log_issue = mysql_fetch_array($result_log_issues))
			{
				$log_entry[$i] = array(
					'when' => strtotime($log_issue['when']),
					'actiontype' => $log_issue['actiontype'],
					'userid' => $log_issue['userid'],
					'category' => $issue['category'],
					'name' => $issue['name']
				);
				$i+=1;
			}
		}
		
	usort($log_entry,'logwhencmp');

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
?>
<?php
template_bottom();
?>