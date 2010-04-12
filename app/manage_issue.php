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
	elseif (isset($_GET['deletecomment']))
	{
		$page->disableTemplate(); // eventually this should be at the top and all methods will use ajax
		header('Content-type: application/json');
		
		$success = true;
		$error = '';
		
		if (is_numeric($i))
		{
			// find the ticket the comment falls under
			$arr = db_query_single("SELECT issue FROM comments WHERE id='$i'");
			$ticket = $arr[0];
			
			// delete the ticket
			db_query("DELETE FROM comments WHERE id='$i'") or $error = 'The comment could\'nt be deleted... it\'s most likely it already has been.';
			
			// if the ticket's already been deleted we definitely do not want to mess with the ticket again
			if ($error == '')
			{
				// update the count
				db_query("UPDATE issues SET num_comments=num_comments-1") or $error = 'Couldn\'t update the ticket (comment count)... has the ticket been deleted?';
				
				if ($error == '')
				{
					// get the newest ticket that hasn't been killed off
					$newquery = db_query("SELECT when_posted FROM comments WHERE issue='$ticket' ORDER BY when_posted DESC LIMIT 1");
					
					// is there one?
					if (mysql_num_rows($newquery))
					{
						$newqueryarr = mysql_fetch_array($newquery);
						$newtime = $newqueryarr[0];
					}
					// otherwise, we grab the time that the ticket was opened
					else
					{
						$newquery2 = db_query("SELECT when_opened FROM issues WHERE id='$ticket'") or $error = 'Couldn\'t update the ticket (non-existent)... has the ticket been deleted?';
						$newquery2arr = mysql_fetch_array($newquery2);
						$newtime = $newquery2arr[0];
					}
					
					// and finally, update the ticket!
					if ($error == '')
					{
						db_query("UPDATE issues SET when_updated='$newtime' WHERE id='$ticket'") or $error = 'Couldn\'t update the ticket (last update time)... has the ticket been deleted?';
					}
				}
			}
		}
		else
		{
			$success = false;
		}
		
		if ($error != '')
		{
			$success = false;
		}
		
		// return
		echo json_encode(array(
			'success' => $success,
			'message' => $error
		));
	}
	elseif (isset($_GET['status']))
	{
		$page->disableTemplate(); // eventually this should be at the top and all methods will use ajax
		header('Content-type: application/json');
		
		$success = true;
		
		// general vars
		$s = escape_smart($_POST['st']);
		
		// first, the status
		$query = db_query("UPDATE issues SET status='$s' WHERE id='$i'") or $success = false;
		
		// custom stuff, whoooooo
		$custom = '';
		
		$assign = escape_smart($_POST['st2a']) == $_POST['st2a'] ? $_POST['st2a'] : false;
		if ($assign)
		{
			if ($assign == -1)
			{
				$uassigned = 'nobody';
			}
			else
			{
				$uassigned = 'user id '.$assign; // need to find a way to get the display name w/o becoming incorrect when it changes
			}
			
			$queryassign = db_query("UPDATE issues SET assign='$assign' WHERE id='$i'");
			if ($queryassign)
			{
				$custom .= ', assigned to '.$uassigned;
			}
			else
			{
				$success = false;
			}
		}
		
		$custom = escape_smart($custom);
		
		// and finally the comment
		$query2 = db_query("INSERT INTO comments (author,issue,content,when_posted,type) VALUES ('$a','$i','*** Status changed to \'".getstatusnm($s)."\'$custom ***',NOW(),'status')") or $success = false;
		$query3 = db_query("UPDATE issues SET num_comments=num_comments+1 WHERE id='$i'") or $success = false;
		
		// return
		if (!$success) { $message = mysql_error(); }
		echo json_encode(array(
			'success' => $success,
			'message' => $message
		));
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
?>