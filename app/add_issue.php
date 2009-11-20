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

// if possible make this more like the reg/login forms or at least introduce similarities to this or them

include("functions.php");
$page->setType('issues');
$page->setTitle('Add an issue');
?>

<h2>Add an issue</h2>

<?php
if (!isloggedin())
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
				Title
			</td>
			<td>
				<input name="title" type="text" /><br />
				<small>Be succint! You are limited to 128 characters.</small>
			</td>
		</tr>
		<tr>
			<td>
				Project
			</td>
			<td>
				<select name="proj">
					<option>todo</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				Category
			</td>
			<td>
				<select name="cat">
					<option value="-1">Select one...</option>
					<option value="-1">--------------------------------</option>
					<?php
						$result_categories = db_query("SELECT * FROM categories");
						while ($category = mysql_fetch_array($result_categories))
						{
							echo '<option value="'.$category['id'].'" style="background:#'.$category['color'].'">'.$category['name'].'</option>';
						}
					?>
				</select><br />
				<small>What category does this issue fit under?</small>
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

	<input type="submit" name="sub" value="Add" />
</form>

<?php
}
else
{
	$c = escape_smart($_POST['cat']);
	
	$cate = db_query_single("SELECT id from categories WHERE id='$c'");
	
	if ($cate[0])
	{
		$t = escape_smart(htmlentities($_POST['title']));
		$a = escape_smart($_SESSION['uid']);
		$d = escape_smart(htmlentities($_POST['desc']));
		
		$query2 = db_query("INSERT INTO issues (name,author,description,project,when_opened,category) VALUES ('$t','$a','$d','1',NOW(),'$c')");
		if ($query2) { echo 'Added issue successfully!'; } else { mysql_error(); }
		
		$query2_id = mysql_insert_id();
		
		echo '<br />';
		
		$query3 = db_query("INSERT INTO log_issues (when_occured,userid,actiontype,issue) VALUES (NOW(),'$a',1,$query2_id)");
		if ($query3) { echo 'Logged successfully!'; } else { mysql_error(); }
		
		echo '<br /><br /><a href="view_issue.php?id='.$query3[0].'">Go to issue</a>';
	}
	else
	{
		echo 'You did not select a proper category.';
	}
}
}
?>