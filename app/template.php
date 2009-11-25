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

// define the locations of everything
$theme = 'default';
$location['theme']  = "thm/$theme";
$location['images'] = "thm/$theme/img";
$location['styles'] = "thm/$theme/css";

// set up everything
$page = new PageBuilder($location);
register_shutdown_function(array($page,'outputAll'));

// http://ianburris.com/tutorials/oophp-template-engine/
class PageBuilder
{
	private $title, $content, $stylesheets=array(), $javascripts=array(), $bodypre, $disabled=false, $location;
	
	function PageBuilder($location)
	{
		// outside stuff
		$this->location = $location;
		
		// default stuff to output header
		$this->title = 'bugspray';
		$this->addCSS($this->location['styles'].'/screen.css');
		$this->addJS('js/jquery-1.3.2.min.js');
		$this->addJS('js/jquery.colorPicker.js');
		$this->addJS('js/jquery.amwnd.js');
		$this->addJS('js/html5.js');
		$this->addJS('js/bugspray.js');
		
		// start capturing the content
		ob_start();
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
		echo '<title>'.$this->title.'</title>'."\n";
		
		foreach ($this->stylesheets as $stylesheet)
		{
			echo "\t\t".'<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
		}
		foreach ($this->javascripts as $javascript)
		{
			echo "\t\t".'<script type="text/javascript" src="'.$javascript.'"></script>'."\n";
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
	
	function setPage($page,$variables=array())
	{
		extract($variables,EXTR_SKIP);
		$location = $this->location;
		include($this->location['theme'].'/'.$page);
		echo "\n";
	}
	
	function build()
	{
		if ($this->disabled)
		{
			return $this->content;
		}
		else
		{
			ob_start();
			$location = $this->location;
			include($this->location['theme'].'/overall.php');
			return ob_get_clean();
		}
	}
	
	function outputAll()
	{
		// stop capturing everything
		$this->content = ob_get_clean();
		
		// build the page
		echo $this->build();
	}
}

?>