<?php
$menu = array(
	array(
		'id' => 'issues',
		'name' => 'Issues',
		'url' => 'index.php'
	),
	array(
		'id' => 'projects',
		'name' => 'Projects'
	),
	array(
		'id' => 'activity',
		'name' => 'Activity',
		'url' => 'activity.php'
	),
	array(
		'id' => 'help',
		'name' => 'Help'
	),
	array(
		'id' => 'admin',
		'name' => 'Admin',
		'show' => isadmin(),
		'url' => 'admin.php'
	),
);
?>