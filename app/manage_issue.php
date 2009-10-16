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
		if ($query2) { echo 'Comment added succesfully!'; } else { mysql_error(); }
		
		echo '<br /><br /><a href="view_issue.php?id='.$i.'">Go back</a>';
	}
	elseif (isset($_GET['unlock']))
	{		
		$query = db_query("UPDATE issues SET discussion_closed=0 WHERE id='$i'");
		if ($query) { echo 'Unlocked discussion succesfully!'; } else { mysql_error(); }
		
		echo '<br />';
		
		$query2 = db_query("INSERT INTO comments (author,issue,content,when_posted,type) VALUES ('$a','$i','*** Discussion has been unlocked ***',NOW(),'reopen')");
		if ($query2) { echo 'Comment added succesfully!'; } else { mysql_error(); }
		
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
	elseif (isset($_GET['assign']))
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
	}
	elseif (isset($_GET['status']))
	{
		$s = escape_smart($_POST['st']);
		
		$query = db_query("UPDATE issues SET status='$s' WHERE id='$i'");
		if ($query) { echo 'Set status successfully!'; } else { mysql_error(); }
		
		echo '<br />';
		
		$query2 = db_query("INSERT INTO comments (author,issue,content,when_posted,type) VALUES ('$a','$i','*** Status changed to \'".getstatusnm($s)."\' ***',NOW(),'status')");
		if ($query2) { echo 'Comment added succesfully!'; } else { mysql_error(); }
		
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