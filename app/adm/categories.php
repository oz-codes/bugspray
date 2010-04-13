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

<p>Categories are defined by you - they could be products, modules of one, versions of one, whatever you like!</p>

<?php
if (!isset($_GET['s']))
{	
	echo '
	<h3>Listing</h3>

	<button type="button" onclick="stringform(\'Add a category\',\''.$uri.'&amp;s=add\')"><img src="img/btn/add.png" alt="" />Add a category</button>';
	
	echo '<ul>';

	$result_categories = db_query("SELECT * FROM categories", 'Retrieving all categories');

	while ($p = mysql_fetch_array($result_categories))
	{
		echo '
		<li>
			'.$p['name'].'
			<small>
				<a href="javascript:void(0)" onclick="stringform(\'Rename category \\\''.str_replace("'","\'",$p['name']).'\\\'\',\''.$uri.'&amp;s=rename&amp;id='.$p['id'].'\')">(rename)</a>
				<a href="javascript:void(0)" onclick="confirmurl(\'Delete category \\\''.str_replace("'","\'",$p['name']).'\\\'\',\''.$uri.'&amp;s=delete&amp;id='.$p['id'].'\',true);">(delete)</a>
			</small>
		</li>';
	}

	echo '</ul>';
}
else
{	
	switch ($_GET['s'])
	{
		case 'add':
			echo '<h3>Adding category</h3>';
			$n = escape_smart($_POST['str']);
			$query = db_query("INSERT INTO categories (name) VALUES ('$n')");
			if ($query) { echo 'Added succesfully!'; } else { mysql_error(); }
			break;
		case 'rename':
			echo '<h3>Renaming category</h3>';
			$n = escape_smart($_POST['str']);
			$i = escape_smart($_GET['id']);
			$query = db_query("UPDATE categories set name='$n' WHERE id='$i'");
			if ($query) { echo 'Renamed succesfully!'; } else { mysql_error(); }
			break;
		case 'delete':
			echo '<h3>Delete category</h3>';
			$i = escape_smart($_GET['id']);
			$query = db_query("DELETE FROM categories WHERE id='$i'");
			if ($query) { echo 'Deleted succesfully!'; } else { mysql_error(); }
			break;
		default:
			echo 'What?';
			break;
	}
	echo '<br /><br /><a href="'.$uri2.'">Go back</a>';
}
?>