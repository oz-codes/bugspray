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

// if possible make this more like the reg/login forms or at least introduce similarities to this or them

include("functions.php");
$page->setType('tickets');
$page->setTitle('Add a ticket');
?>

<div class="imgtitle imgtitle-32">
	<img class="image" src="<?php echo $location['images']; ?>/titles/tickets-add.png" alt="" />
	<div class="text">
		<h1>Add a ticket</h1>
	</div>
	<div class="clear"></div>
</div>

<p><b>[todo: redo this form]</b></p>

<?php
if (!$client['is_logged'])
{
	echo 'You are not logged in.';
}
else
{
if (!isset($_POST['sub']))
{
?>

<form action="" method="post">	
	<table class="frmtbl">
		<tr>
			<td style="width:128px;">
				Summary
			</td>
			<td>
				<input name="title" type="text" /><br />
				<small>Be succinct! You are limited to 128 characters.</small>
			</td>
		</tr>
		<tr>
			<td>
				Severity
			</td>
			<td>
				<input type="radio" name="severity" id="sv-0" value="0" /> <label for="sv-0">None</label>
				<input type="radio" name="severity" id="sv-1" value="1" /> <label for="sv-1">Very Low</label>
				<input type="radio" name="severity" id="sv-2" value="2" /> <label for="sv-2">Low</label>
				<input type="radio" name="severity" id="sv-3" value="3" /> <label for="sv-3">Medium</label>
				<input type="radio" name="severity" id="sv-4" value="4" /> <label for="sv-4">Severe</label>
				<input type="radio" name="severity" id="sv-5" value="5" /> <label for="sv-5">Very Severe</label>
			</td>
		</tr>
		<tr>
			<td>
				Category
			</td>
			<td>
				<select name="category">
					<option>todo</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				Tags
			</td>
			<td>
				<select name="tag">
					<option value="-1">Select one...</option>
					<option value="-1">--------------------------------</option>
					<?php
						$result_tags = db_query("SELECT * FROM tags");
						while ($tag = mysql_fetch_array($result_tags))
						{
							echo '<option value="' . $tag['id'] . '">' . $tag['name'] . '</option>';
						}
					?>
				</select><br />
				<small>You can only select one tag for now, as changes are happening with the codebase</small>
			</td>
		</tr>
		<tr>
			<td>
				Description
			</td>
			<td>
				<textarea name="desc" style="width:50%;height:192px;"></textarea><br />
				<small>This is where you describe the issue in full. Be sure to be descriptive!</small>
			</td>
		</tr>
	</table>

	<input type="submit" name="sub" value="Post" />
</form>

<?php
}
else
{
	$c = escape_smart($_POST['tag']);
	
	$cate = db_query_single("SELECT id from tags WHERE id='$c'");
	
	if ($cate[0])
	{
		$t = escape_smart(htmlentities($_POST['title']));
		$a = escape_smart($_SESSION['uid']);
		$d = escape_smart(htmlentities($_POST['desc']));
		$s = escape_smart($_POST['severity']);
		
		$query2 = db_query("INSERT INTO issues (name,author,description,category,when_opened,when_updated,tags,severity) VALUES ('$t','$a','$d','1',NOW(),NOW(),'$c','$s')");
		if ($query2) { echo 'Added issue successfully!'; } else { echo mysql_error(); }
		
		$query2_id = mysql_insert_id();
		
		echo '<br />';
		
		$query3 = db_query("INSERT INTO log_issues (when_occured,userid,actiontype,issue) VALUES (NOW(),'$a',1,$query2_id)");
		if ($query3) { echo 'Logged successfully!'; } else { mysql_error(); }
		
		echo '<br /><br /><a href="view_issue.php?id=' . $query2_id . '">Go to issue</a>';
	}
	else
	{
		echo 'You did not select a proper tag.';
	}
}
}
?>