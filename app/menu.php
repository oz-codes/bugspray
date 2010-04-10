<?php
$menu = array(
	array(
		'id' => 'dashboard',
		'name' => 'Dashboard',
		'url' => 'index.php'
	),
	array(
		'id' => 'tickets',
		'name' => 'Tickets',
		'url' => 'tickets.php'
	),
	array(
		'id' => 'milestones',
		'name' => 'Milestones'
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
		'hide' => !isadmin(),
		'url' => 'admin.php'
	),
);
?>