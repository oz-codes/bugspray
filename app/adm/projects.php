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
?>

Just what it says on the tin! Projects can be anything from a piece of software to a website.

<br />
<br />

<?php
if (!isset($_GET['s']))
{	
	echo '
	<h3>Listing</h3>

	<button type="button" onclick="stringform(\'Add a project\',\''.$uri.'&amp;s=add\')"><img src="img/btn/add.png" alt="" />Add a project</button>';
	
	echo '<ul>';

	$result_projects = db_query("SELECT * FROM projects");

	while ($p = mysql_fetch_array($result_projects))
	{
		echo '
		<li>
			'.$p['name'].'
			<small>
				<a href="javascript:void(0)" onclick="stringform(\'Rename project \\\''.str_replace("'","\'",$p['name']).'\\\'\',\''.$uri.'&amp;s=rename&amp;id='.$p['id'].'\')">(rename)</a>
				<a href="javascript:void(0)" onclick="confirmurl(\'Delete project \\\''.str_replace("'","\'",$p['name']).'\\\'\',\''.$uri.'&amp;s=delete&amp;id='.$p['id'].'\',true);">(delete)</a>
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
			echo '<h3>Adding project</h3>';
			$n = escape_smart($_POST['str']);
			$query = db_query("INSERT INTO projects (name) VALUES ('$n')");
			if ($query) { echo 'Added succesfully!'; } else { mysql_error(); }
			break;
		case 'rename':
			echo '<h3>Renaming project</h3>';
			$n = escape_smart($_POST['str']);
			$i = escape_smart($_GET['id']);
			$query = db_query("UPDATE projects set name='$n' WHERE id='$i'");
			if ($query) { echo 'Renamed succesfully!'; } else { mysql_error(); }
			break;
		case 'delete':
			echo '<h3>Delete project</h3>';
			$i = escape_smart($_GET['id']);
			$query = db_query("DELETE FROM projects WHERE id='$i'");
			if ($query) { echo 'Deleted succesfully!'; } else { mysql_error(); }
			break;
		default:
			echo 'What?';
			break;
	}
	echo '<br /><br /><a href="'.$uri2.'">Go back</a>';
}
?>