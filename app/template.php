<?php
/*
 * bugspray issue tracking software
 * Copyright (c) 2009 a2h - http://a2h.uni.cc/
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
 *
 */

$page = new PageBuilder;
$page->startBody();
register_shutdown_function('template_bottom2');

function template_top()
{
	/* */
}
function template_bottom()
{
	/* */
}

function template_bottom2()
{
	global $page;
	$page->endBody();
	echo $page->render("thm/default/overall.php");
}

// http://ianburris.com/tutorials/oophp-template-engine/
class PageBuilder
{
	private $title, $content;
	
	function PageBuilder()
	{
		$this->title = 'bugspray';
	}
	
	function setTitle($title)
	{
		$this->title = 'bugspray &bull; ' . $title;
	}
	
	function setType($type)
	{
		$this->type = $type;
	}
	
	function startBody()
	{
		ob_start();
	}
	
	function endBody()
	{
		$this->content = ob_get_clean();
	}
	
	function getMenu()
	{
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
		for ($i=0;$i<sizeof($menu);$i++)
		{
			if ($menu[$i]['id'] == $this->type)
			{
				$menu[$i]['selected'] = true;
			}
			else
			{
				$menu[$i]['selected'] = false;
			}
		}
		return $menu;
	}
	
	function showContent()
	{
		echo $this->content;
	}
	
	function showSidebar()
	{
		include("sidebar.php");
	}
	
	function render($path)
	{
		ob_start();
		include($path);
		return ob_get_clean();
	}
}

?>