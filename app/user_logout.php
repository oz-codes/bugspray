<?php
/**
 * spray issue tracking software
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

include('sp-core.php');

$page->setType('account');
$page->setTitle('Logout');

if (!$users->client->is_logged)
{
	echo 'Congratulations, you have just created a paradox. A black hole is currently being formed behind you.';
}
else
{	
	if ($users->logout())
	{
		// congraulations, you have helped destroy ze vorld!
		echo 'You have been logged out.<br /><br /><small>[todo maybe: an ajax version]</small>';
	}
}
?>