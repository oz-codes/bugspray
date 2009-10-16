<?php
/*
 * bugspray issue tracking software
 * Copyright (c) 2009 a2h - http://a2h.uni.cc/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * Under section 7b of the GNU Affero General Public License you are
 * required to preserve this notice. Additional attribution may be
 * found in the NOTICES.txt file provided with the Program.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

include("functions.php");
template_top('issues');

if (!((!$issue['discussion_closed'] || isadmin()) && isloggedin())) // ridiculously complicated if statement derived from view_issue.php
{
	echo 'You do not have sufficient privileges to access this page.';
}
else
{
	if (!isset($_POST['sub']))
	{
		echo 'What?';
	}
	else
	{
		$a = escape_smart(getuid($_SESSION['username']));
		$i = escape_smart($_POST['id']);
		$c = escape_smart(htmlentities($_POST['cont'])); 
		
		$query = db_query("INSERT INTO comments (author,issue,content,when_posted) VALUES ('$a','$i','$c',NOW())");
		$query = db_query("UPDATE issues SET num_comments=num_comments+1 WHERE id='$i'");
		
		if ($query) { echo 'Done!<br /><br /><a href="view_issue.php?id='.$i.'">Go back</a>'; } else { mysql_error(); }
	}
}

template_bottom();
?>