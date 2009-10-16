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

$a = escape_smart(getuid($_SESSION['username']));
$i = escape_smart($_GET['id']);

if (isadmin())
{
	if (isset($_GET['lock']))
	{		
		$query = db_query("UPDATE issues SET discussion_closed=1 WHERE id='$i'");
		if ($query) { echo 'Locked discussion succesfully!'; } else { mysql_error(); }
		
		echo '<br />';
		
		$query2 = db_query("INSERT INTO comments (author,issue,content,when_posted,type) VALUES ('$a','$i','*** Discussion has been locked ***',NOW(),'close')");
		$query3 = db_query("UPDATE issues SET num_comments=num_comments+1 WHERE id='$i'");
		if ($query2 && $query3) { echo 'Comment added succesfully!'; } else { mysql_error(); }
		
		echo '<br /><br /><a href="view_issue.php?id='.$i.'">Go back</a>';
	}
	elseif (isset($_GET['unlock']))
	{		
		$query = db_query("UPDATE issues SET discussion_closed=0 WHERE id='$i'");
		if ($query) { echo 'Unlocked discussion succesfully!'; } else { mysql_error(); }
		
		echo '<br />';
		
		$query2 = db_query("INSERT INTO comments (author,issue,content,when_posted,type) VALUES ('$a','$i','*** Discussion has been unlocked ***',NOW(),'reopen')");
		$query3 = db_query("UPDATE issues SET num_comments=num_comments+1 WHERE id='$i'");
		if ($query2 && $query3) { echo 'Comment added succesfully!'; } else { mysql_error(); }
		
		echo '<br /><br /><a href="view_issue.php?id='.$i.'">Go back</a>';
	}
	elseif (isset($_GET['delete']))
	{
		$query = db_query("DELETE FROM issues WHERE id='$i'");
		if ($query) { echo 'Deleted issue succesfully!'; } else { mysql_error(); }
		
		echo '<br />';
		
		$query2 = db_query("DELETE FROM comments WHERE issue='$i'");
		if ($query2) { echo 'Deleted associated comments succesfully!'; } else { mysql_error(); }
		
		echo '<br /><br /><a href="index.php">Go to issue index</a>';
	}
	/*elseif (isset($_GET['assign']))
	{
		$u = escape_smart($_GET['assign']);
		
		$query = db_query("UPDATE issues SET assign='$u' WHERE id='$i'");
		if ($query) { echo 'Assigned issue succesfully!'; } else { mysql_error(); }
		
		echo '<br />';
		
		if ($u == -1)
			$s = 0;
		else
			$s = 1;
		
		$query2 = db_query("UPDATE issues SET status=$s WHERE id='$i'");
		if ($query2) { echo 'Set status sucessfully!'; } else { mysql_error(); }
		
		echo '<br /><br /><a href="view_issue.php?id='.$i.'">Go back</a>';
	}*/
	elseif (isset($_GET['status']))
	{
		$s = escape_smart($_POST['st']);
		
		$query = db_query("UPDATE issues SET status='$s' WHERE id='$i'");
		if ($query) { echo 'Set status successfully!'; } else { mysql_error(); }
		
		echo '<br />';
		
		$query2 = db_query("INSERT INTO comments (author,issue,content,when_posted,type) VALUES ('$a','$i','*** Status changed to \'".getstatusnm($s)."\' ***',NOW(),'status')");
		$query3 = db_query("UPDATE issues SET num_comments=num_comments+1 WHERE id='$i'");
		if ($query2 && $query3) { echo 'Comment added succesfully!'; } else { mysql_error(); }
		
		echo '<br /><br /><a href="view_issue.php?id='.$i.'">Go back</a>';
	}
	else
	{
		echo 'What?';
	}
}
else
{
	echo 'You do not have sufficient privileges to access this page.';
}

template_bottom();
?>