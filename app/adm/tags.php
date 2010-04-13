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
?>

<p>Tags are a great multipurpose way to get your tickets in order.</p>

<?php
if (!isset($_GET['s']))
{
	echo '
	<h3>Listing</h3>
	
	<button type="button" onclick="stringform(\'Add a tag\',\''.$uri.'&amp;s=add\',{col:true})"><img src="img/btn/add.png" alt="" />Add a tag</button>';

	echo '<ul>';
	
	$result_tags = db_query("SELECT * FROM tags", 'Retrieving all tags');

	while ($tag = mysql_fetch_array($result_tags))
	{
		echo '
		<li>
			<div class="left" style="width:128px;">' . $tag['name'] . '</div>			
			<small class="left">
				<a href="#">(<s>list</s>)</a>
				<a href="javascript:void(0)" class="rnm">(<s>rename</s>)</a>
				<a href="javascript:void(0)" class="del">(<s>delete</s>)</a>
				<br />
				linked categories: all <a href="#">(<s>change</s>)</a>
			</small>
			<div class="clear"></div>
		</li>';
	}
	
	echo '</ul>';
}
else
{
	switch ($_GET['s'])
	{
		case 'add':
			echo '<h3>Adding tag</h3>';
			$n = escape_smart($_POST['str']);
			$c = escape_smart(str_replace('#','',$_POST['col']));
			$query = db_query("INSERT INTO tags (name,color) VALUES ('$n','$c')");
			if ($query) { echo 'Added succesfully!'; } else { mysql_error(); }
			break;
		case 'rename':
			echo '<h3>Renaming tag</h3>';
			$n = escape_smart($_POST['str']);
			$i = escape_smart($_GET['id']);
			$query = db_query("UPDATE tags set name='$n' WHERE id='$i'");
			if ($query) { echo 'Renamed succesfully!'; } else { mysql_error(); }
			break;
		case 'delete':
			echo '<h3>Delete tag</h3>';
			$i = escape_smart($_GET['id']);
			$query = db_query("DELETE FROM tags WHERE id='$i'");
			if ($query) { echo 'Deleted succesfully!'; } else { mysql_error(); }
			break;
		default:
			echo 'What?';
			break;
	}
	echo '<br /><br /><a href="'.$uri2.'">Go back</a>';
}
?>