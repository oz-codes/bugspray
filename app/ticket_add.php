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

// if possible make this more like the reg/login forms or at least introduce similarities to this or them

$page->setType('tickets');
$page->setTitle('Add a ticket');

// We need to be logged in for this, of course
if (!$users->client->is_logged)
{
	echo 'You are not logged in.';
}
else
{
	// Have we got some input? Looks like we should handle it!
	if (isset($_POST['submit']))
	{
		$title = escape_smart(htmlentities($_POST['title']));
		$description = escape_smart(htmlentities($_POST['description']));
		$severity = escape_smart($_POST['severity']);
		
		// Error arrays
		$error = false;
		$errors_title = array();
		$errors_tags = array();
		$errors_description = array();
		
		// Sanitise the tags string [TODO: use the separate table for tags instead of one long string]
		$tags = escape_smart(htmlentities($_POST['tags']));
		
		// Remove excessive whitespace, first on the sides, and then double/triple/quadruple/etc spaces
		$tags = trim($tags);
		while (strstr($tags, '  '))
		{
			$tags = str_replace('  ', ' ', $tags);
		}
		
		// Split the tags into a nice array and get a proper count of how many tags there are
		$tagsarr = explode(' ', $tags);
		sort($tagsarr);
		$tagsc = empty($tags) ? 0 : count($tagsarr); // We need to explicitly set to 0 if there's no items because of how explode works
		
		// Have we gone over the tag limit?
		if ($tagsc > 5)
		{
			$error = true;
			$errors_tags[] = 'You may only provide up to 5 tags.';
		}
		
		// We still want to run this even if there's more than 5 tags to check for other errors
		if ($tagsc > 0)
		{
			$tags = '';
			for ($i=0; $i<$tagsc; $i++)
			{
				if (strstr($tags, $tagsarr[$i]))
				{
					$error = true;
					$errors_tags[] = 'The tag you entered \'' . $tagsarr[$i] . '\' has been entered more than once.';
				}
				elseif (strlen($tagsarr[$i]) > 16) // TODO: client side check for this
				{
					$error = true;
					$errors_tags[] = 'The tag you entered \'' . $tagsarr[$i] . '\' exceeds the maximum length of tags of 16 characters.';
				}
				else
				{
					$tags .= ($i > 0 ? ' ' : '') . $tagsarr[$i];
				}
			}
		}
		
		if (!hascharacters($title))
		{
			$error = true;
			$errors_title[] = 'The summary you provided for your ticket is blank.';
		}
		if (!hascharacters($description))
		{
			$error = true;
			$errors_description[] = 'The description you provided for your ticket is blank.';
		}
		
		if (!$error)
		{
			$query2 = db_query("
				INSERT INTO issues (name, author, description, when_opened, when_updated, tags, severity)
				VALUES ('$title', {$_SESSION['uid']}, '$description', NOW(), NOW(), '$tags', '$severity')
			");
			
			if ($query2) { echo '<p><b>Info:</b> Added issue successfully!</p>'; } else { echo mysql_error(); }
			
			$query2_id = mysql_insert_id();
			
			echo '<br />';
			
			$query3 = db_query("INSERT INTO log_issues (when_occured,userid,actiontype,issue) VALUES (NOW(), {$_SESSION['uid']}, 1, $query2_id)");
			if ($query3) { echo '<p><b>Info:</b> Logged successfully!</p>'; } else { mysql_error(); }
			
			echo '<p><a href="ticket.php?id=' . $query2_id . '">Go to issue</a></p>';
		}
	}
	
	// If we've just started, or we've got errors, show the form!
	if (!isset($error) || $error)
	{
		$page->include_template(
			'ticket_add',
			array(
				'errors_title' => $errors_title,
				'errors_tags' => $errors_tags,
				'errors_description' => $errors_description
			)
		);
	}
}
?>
