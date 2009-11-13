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
?>

<h3>Issues assigned to you</h3>
<?php
$result_issues_assigned = db_query("SELECT * FROM issues WHERE assign = '{$_SESSION['uid']}' ORDER BY when_opened ASC");
if (mysql_num_rows($result_issues_assigned) > 0)
{
	echo '<div style="font-size:12px;">';
	while ($issue_assigned = mysql_fetch_array($result_issues_assigned))
	{
		echo '
		<div class="fl" style="background:#ddd;font-weight:bold;padding:4px 6px;width:16px;text-align:center;margin-right:8px;">'.$issue_assigned['num_comments'].'</div>
		<div class="fl" style="margin-top:4px;"><a href="view_issue.php?id='.$issue_assigned['id'].'">'.$issue_assigned['name'].'</a></div>';
	}
	echo '<div class="fc"></div>
	</div>';
}
else
{
	echo 'Nothing is assigned to you...';
}
?>

<br />
<small>[TODO: Show only for those who can be assigned to proj's]</small>

<hr />

<h3>Issues you're watching</h3>
Not implemented...