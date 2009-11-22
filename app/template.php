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
register_shutdown_function('template_bottom2'); // need a new name or have the code in a better place

function template_bottom2()
{
	global $page;
	$page->endBody();
	echo $page->render("thm/default/overall.php");
}

// http://ianburris.com/tutorials/oophp-template-engine/
class PageBuilder
{
	private $title, $content, $stylesheets=array(), $javascripts=array(), $bodypre, $disabled=false;
	
	function PageBuilder()
	{
		$this->title = 'bugspray';
		$this->addCSS('img/style.css');
		$this->addJS('js/jquery-1.3.2.min.js');
		$this->addJS('js/jquery.colorPicker.js');
		$this->addJS('js/jquery.amwnd.js');
		$this->addJS('js/html5.js');
		$this->addJS('js/bugspray.js');
	}
	
	function disableTemplate()
	{
		$this->disabled = true;
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
	
	function addCSS($path)
	{
		$this->stylesheets[] = $path;
	}
	
	function addJS($path)
	{
		$this->javascripts[] = $path;
	}
	
	function addBodyPre($content)
	{
		$this->bodypre .= $content;
	}
	
	function outputBodyPre()
	{
		echo $this->bodypre;
	}
	
	function getMenu()
	{
		include("menu.php");
		
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
	
	function outputHead()
	{
		foreach ($this->stylesheets as $stylesheet)
		{
			echo '<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />';
		}
		foreach ($this->javascripts as $javascript)
		{
			echo '<script type="text/javascript" src="'.$javascript.'"></script>';
		}
	}
	
	function outputContent()
	{
		echo $this->content;
	}
	
	function outputSidebar()
	{
		include("sidebar.php");
	}
	
	function render($path)
	{
		if ($this->disabled)
		{
			return $this->content;
		}
		else
		{
			ob_start();
			include($path);
			return ob_get_clean();
		}
	}
}

?>