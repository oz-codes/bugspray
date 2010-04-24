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
	<dl class="form">
		<dt class="inline">
			<label for="title">Summary</label>
		</dt>
		<dd class="inline">
			<input id="title" name="title" type="text" size="64" maxlength="128" />
		</dd>
		
		<dt>
			<label for="description">Describe the problem</label>
		</dt>
		<dd>
			<textarea id="description" name="description" style="width: 75%; height: 192px;"></textarea>
		</dd>
		
		<dt>
			<label for="severity">Severity</label>
		</dt>
		<dd>
			<select id="severity" name="severity">
				<option value="0">None</option>
				<option value="1">Very Low</option>
				<option value="2">Low</option>
				<option value="3">Medium</option>
				<option value="4">Severe</option>
				<option value="5">Very Severe</option>
			</select>
		</dd>
		
		<dt>
			<label for="tag">Tag</label>
		</dt>
		<dd>
			<select id="tag" name="tag">
				<option value="-1">Select one...</option>
				<option value="-1">--------------------------------</option>
				<?php
					$tags = db_query("SELECT * FROM tags", 'Retrieving all tags from the database');
					while ($tag = mysql_fetch_array($tags))
					{
						echo '<option value="' . $tag['id'] . '">' . $tag['name'] . '</option>';
					}
				?>
			</select>
			<br />
			<small>Due to recent changes in the codebase, the tag system does not work like how tags would be expected to. This will be changed later on.</small>
		</dd>
		
		<dt>
			<label for="category">Category</label>
		</dt>
		<dd>
			<select id="category" name="category">
				<option>todo</option>
			</select>
		</dd>
	</dl>

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
		$d = escape_smart(htmlentities($_POST['description']));
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