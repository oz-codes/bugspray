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

include("functions.php");
template_top('issues');

if (!((!$issue['discussion_closed'] || isadmin()) && isloggedin())) // ridiculously complicated if statement derived from view_issue.php
{
	echo 'You do not have sufficient privileges to access this page.';
}
else
{
	if (!isset($_POST['sub']))
	{
		echo 'What?';
	}
	else
	{
		$a = escape_smart(getuid($_SESSION['username']));
		$i = escape_smart($_POST['id']);
		$c = escape_smart(htmlentities($_POST['cont'])); 
		
		$query = db_query("INSERT INTO comments (author,issue,content,when_posted) VALUES ('$a','$i','$c',NOW())");
		$query = db_query("UPDATE issues SET num_comments=num_comments+1 WHERE id='$i'");
		
		if ($query) { echo 'Done!<br /><br /><a href="view_issue.php?id='.$i.'">Go back</a>'; } else { mysql_error(); }
	}
}

template_bottom();
?>