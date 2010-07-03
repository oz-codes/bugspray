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

include("functions.php");
$page->setType('tickets');
$page->setTitle('Tickets');

// Installer completion screen
if (isset($_GET['installerdone']) && is_dir('install'))
{
	$page->addCSS('install/installer.css');
	include("install/index.php");
}

// Get the tickets and show them!
$tickets = ticket_list($_GET['status'], 'desc', true);
$page->include_template(
	'tickets.php',
	array(
		'tickets' => $tickets
	)
);
?>