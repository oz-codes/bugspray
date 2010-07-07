<?php
global $client, $users;

$menu = array(
	array(
		'id' => 'tickets',
		'name' => 'Tickets',
		'url' => 'index.php'
	),
	array(
		'id' => 'statistics',
		'name' => 'Statistics',
		//'url' => 'statistics.php'
	),
	array(
		'id' => 'admin',
		'name' => 'Admin',
		'hide' => !$users->client->is_admin,
		'url' => 'admin.php'
	),
);
?>