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

$id = escape_smart($_GET['id']);
$a = escape_smart($_SESSION['uid']);

switch ($_GET['action'])
{	
	case 'delete':
		if ($users->client->is_admin)
		{
			ticket_delete($id);
			break;
		}
	
	case 'deletecomment':
		if ($users->client->is_admin)
		{
			$page->theme_disable(true); // eventually this should be at the top and all methods will use ajax
			header('Content-type: application/json');
			echo json_encode(ticket_comment_delete($id));
			break;
		}
	
	case 'notadmin':
		echo 'You need to be an administrator to perform that action.';
		break;
	
	case 'comment':
		$page->theme_disable(true); // eventually this should be at the top and all methods will use ajax
		header('Content-type: application/json');
		echo json_encode(ticket_comment_add(escape_smart($_POST['id']), escape_smart(htmlspecialchars($_POST['content'])), 'json'));
		break;
	
	case 'favorite':
		$page->theme_disable(true);
		header('Content-type: application/json');
		echo json_encode(ticket_favorite(escape_smart($_GET['id']), 'json'));
		break;
}



function ticket_delete($ticket)
{
	$query = db_query("DELETE FROM issues WHERE id='$ticket'");
	if ($query) { echo 'Deleted issue succesfully!'; } else { mysql_error(); }
	
	echo '<br />';
	
	$query2 = db_query("DELETE FROM comments WHERE issue='$ticket'");
	if ($query2) { echo 'Deleted associated comments succesfully!'; } else { mysql_error(); }
	
	echo '<br /><br /><a href="index.php">Go to issue index</a>';
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// okay this function is written to return text if it's UNsuccessful, so if you don't choose json then the error goes to another
// function like how mysql_query goes to mysql_error, except mysql_query returns something if it's successful... i really wish
// i knew of a better solution. so, TODO: FIND A BETTER SOLUTION!
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ticket_comment_add($issue, $content, $return, $type='')
{
	// blargh
	global $client;
	
	// safety first
	$content = escape_smart($content);
	$type = escape_smart($type);
	
	// tracking
	$success = true;
	$message = '';
	
	// we CAN post here, right?
	if ($client['is_logged'])
	{
		// yeah, like we're going to post a blank message...
		if (!hascharacters($content))
		{
			$success = false;
			$message = 'You didn\'t put in any content to post.';
		}
		else
		{
			// want to set a custom type?
			$typecolumn = '';
			$typevalue = '';
			if ($type != '')
			{
				$typecolumn = ",type";
				$typevalue = ",'$type'";
			}
			
			// and we're off!
			if (db_query("INSERT INTO comments (author,issue,content,when_posted$typecolumn) VALUES ({$_SESSION['uid']},'$issue','$content',NOW()$typevalue)"))
			{
				if (!db_query("UPDATE issues SET num_comments=num_comments+1, when_updated=NOW() WHERE id='$issue'"))
				{
					$success = false;
					$message = 'Comment inserted successfully, however the comment count could not be updated.';
				}
			}
			else
			{
				$success = false;
				$message = 'Could not insert the comment.';
			}
		}
	}
	// or not...
	else
	{
		$success = false;
		$message = 'You do not have sufficient privileges to post the comment.';
	}
	
	// and our work here is done!
	switch ($return)
	{
		case 'json':
			return array('success' => $success, 'message' => $message);
			break;
		
		case 'success':
		default:
			global $commenterr;
			$commenterr = $message;
			return $success;
			break;
	}
}

function ticket_comment_add_error()
{
	global $commenterr;
	return $commenterr ? $commenterr : false;
}

// this function has the same issue as with ticket_comment_add
function ticket_comment_delete($comment)
{
	$success = true;
	$error = '';
	
	if (is_numeric($comment))
	{
		// find the ticket the comment falls under
		$arr = db_query_single("SELECT issue FROM comments WHERE id='$comment'");
		$ticket = $arr[0];
		
		// delete the ticket
		db_query("DELETE FROM comments WHERE id='$comment'") or $error = 'The comment could\'nt be deleted... it\'s most likely it already has been.';
		
		// if the ticket's already been deleted we definitely do not want to mess with the ticket again
		if ($error == '')
		{
			// update the count
			db_query("UPDATE issues SET num_comments=num_comments-1 WHERE id='$ticket'") or $error = 'Couldn\'t update the ticket (comment count)... has the ticket been deleted?';
			
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
	return array(
		'success' => $success,
		'message' => $error
	);
}

// this function has the same issue as with ticket_comment_add
function ticket_favorite($ticket, $return)
{
	global $users;
	
	$success = true;
	$message = '';
	
	if ($users->client->is_logged)
	{
		if (!mysql_num_rows(db_query("SELECT * FROM favorites WHERE ticketid='$ticket' AND userid={$_SESSION['uid']}")))
		{
			if (!db_query("INSERT INTO favorites (ticketid, userid) VALUES ('$ticket', {$_SESSION['uid']})"))
			{
				$success = false;
				$message = 'Could not favourite this issue';
			}
		}
		else
		{
			if (!db_query("DELETE FROM favorites WHERE ticketid='$ticket' AND userid={$_SESSION['uid']}"))
			{
				$success = false;
				$message = 'Could not unfavourite this issue';
			}
		}
	}
	else
	{
		$success = false;
		$message = 'You need to be logged in to access this.';
	}
	
	// and our work here is done!
	switch ($return)
	{
		case 'json':
			return array('success' => $success, 'message' => $message);
			break;
		
		case 'success':
		default:
			return $success;
			break;
	}
}
?>