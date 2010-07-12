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

$page->setType('tickets');

$id = $_GET['id'];

if (!is_numeric($id))
{
	// todo: move this into a dedicated error page. this message is in account.php and ticket.php
	$page->setTitle('Error');
	$page->setType('error');
	?>
	<div class="imgtitle imgtitle-32">
		<img class="image" src="<?php echo $location['images']; ?>/titles/error.png" alt="" />
		<div class="text">
			<h1>Error!</h1>
		</div>
		<div class="clear"></div>
	</div>
	<p>Oh dear! It looks like you either don't have permission to access this page, or it doesn't exist!</p>
	<p>If perhaps your problem is due to permissions, have a look-see if you're logged in. If you are, perhaps
	this is a restricted page.</p>
	<p>Perhaps it's not permissions? If you typed out the address to this page manually, be sure to check if your spllnig is correct!
	Otherwise, pop around to the <a href="./">home page</a> and try and see if you can find what you were looking for.</p>
	<?php
}
else
{
	$result_issues = db_query("SELECT * FROM issues WHERE id = '$id' LIMIT 1", "Retrieving info for issue $id from database");
	
	if (!mysql_num_rows($result_issues))
	{
		$page->setTitle('Error');
		echo 'That ticket doesn\'t exist! If you accessed this page through a link, please contact the person who posted it and inform them that the link is invalid.';
	}
	else
	{		
		$issue = mysql_fetch_array($result_issues);
		$page->setTitle($issue['name']);
		
		// do we have a comment to post?
		if (isset($_POST['submit']))
		{
			$error = false;
			$errors = array();
			
			// safety first
			$comment = escape_smart($_POST['comment']);
			$status = escape_smart($_POST['status']);
			$assign = escape_smart($_POST['assign']);
                        $data = isset($_POST["misc"])?$_POST["misc"]:"";
                        $bn = basename(dirname($_SERVER[PHP_SELF]));
                        if($status == 6) { $misc = escape_smart("Duplicate Of=<a href='$bn/ticket.php?id=$data'>$data</a>"); }
			
			// TODO: make a method of adding these messages that don't involve permanently storing a name...
			
			// has the status been changed?
			if (is_numeric($status) && $status != $issue['status'])
			{
				if (!db_query("UPDATE issues SET status = $status, misc='$misc' WHERE id = $id "))
				{
					$error = true;
					$errors[] = 'The status of the ticket could not be changed. There may be a server error.';
				}
				else
				{
					$comment .= "\n\n" . '[b]*** Status changed to ' . getstatusnm($status) . ' ***[/b]';
                                        $comment .= ($misc != "")?"\n\n[b]*** Received extra data: $data ***[/b]":"";
				}
			}
			
			// has the assigned user been changed?
			if (is_numeric($assign) && $assign != $issue['assign'])
			{
				if (!db_query("UPDATE issues SET assign = $assign WHERE id = $id"))
				{
					$error = true;
					$errors[] = 'The assigned user of the ticket could not be changed. There may be a server error.';
				}
				else
				{
					$comment .= "\n\n" . '[b]*** Assigned user changed to ' . $users->id($assign)->info['name'] . ' ***[/b]';
				}
			}
			
			// now let's insert the comment!
			if (db_query("INSERT INTO comments (author, issue, content, when_posted) VALUES ({$_SESSION['uid']}, $id, '$comment', NOW())"))
			{
				if (!db_query("UPDATE issues SET num_comments = num_comments+1, when_updated = NOW() WHERE id = $id"))
				{
					$errors[] = 'Your comment was inserted successfully, however, the comment count could not be updated.';
				}
			}
			else
			{
				$error = true;
				$errors[] = 'Your comment could not be inserted. There may be a server error.';
			}
			
			// and our work here is done!
			if ($error)
			{
				output_errors($errors);
			}
			else
			{
				header("Location: {$_SERVER['REQUEST_URI']}");
				exit();
			}
		}
		
		// add to views
		db_query("UPDATE issues SET num_views = num_views + 1 WHERE id = '$id'");
		
		// who is the issue assigned to?
		if ($issue['assign'] > 0)
		{
			$issue['assignedto'] = getuinfo($issue['assign']);
		}
		else
		{
			$issue['assignedto'] = 'nobody';
		}

		// Get assignable users (for now, just admins)
		$assignsarr = array(array(0,'nobody'),array(0,'----------------------'));
		foreach ($users->from_admins() as $uid)
		{
			$assignsarr[] = array(
				$uid,
				getunm($uid),
				$uid == $issue['assign'] ? true : false
			);
		}
		$issue['assigns'] = $assignsarr;
		
		// get the comments
		$result_comments = db_query_toarray("SELECT * FROM comments WHERE issue = $id ORDER BY when_posted ASC", "Retrieving comments for issue $id from database");
		
		// output the page
		$page->include_template(
			'ticket',
			array(
				'issue' => $issue,
				'comments' => $result_comments
			)
		);
	}
}
?>
