<?php
global $client;

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
		'id' => 'statistics',
		'name' => 'Statistics',
		'url' => 'statistics.php'
	),
	array(
		'id' => 'help',
		'name' => 'Help'
	),
	array(
		'id' => 'admin',
		'name' => 'Admin',
		'hide' => !$client['is_admin'],
		'url' => 'admin.php'
	),
);
?>