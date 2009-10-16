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
template_top('admin');

echo '<h2>Administration Panel</h2>';

if (isadmin())
{
	// redundant, merge with adm() or something
	switch ($_GET['p'])
	{
		case 'home': adm(); break;
		case 'projects': adm(); break;
		case 'categories': adm(); break;
		default: adm('home'); break;
	}
}
else
{
	echo 'You do not have sufficient privileges to access this page.';
}

template_bottom();

function adm($page='[nothingatall]')
{
	if ($page=='[nothingatall]') { $page = $_GET['p']; }
	
	echo '
	<div class="tabs">
		<a href="admin.php"'.($page=='home'?'class="sel"':'').'>Home</a>
		<a href="admin.php?p=projects"'.($page=='projects'?'class="sel"':'').'>Projects</a>
		<a href="admin.php?p=categories"'.($page=='categories'?'class="sel"':'').'>Categories</a>
		<a href="#" class="notyet">Commenting</a>
		<a href="#" class="notyet">Bans</a>
		<a href="#" class="notyet">Pages</a>
		<a href="#" class="notyet">Appearance</a>
		<div class="fc"></div>
	</div>';
	
	$uri = $_SERVER['REQUEST_URI'];
	$uri2 = $_SERVER['SCRIPT_NAME'].'?p='.$_GET['p'];
	
	include("adm/$page.php");
}
?>