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

// define the locations of everything
$theme = 'default';
$location = array(
	'theme'  => "thm/$theme",
	'images' => "thm/$theme/img",
	'styles' => "thm/$theme/css"
);

// set up everything
$page = new MTTemplate();
register_shutdown_function(array($page,'outputAll'));

class MTTemplate
{
	private $title, $content, $stylesheets=array(), $javascripts=array(), $bodypre, $disabled=false, $sitename, $location;
	
	function __construct()
	{
		global $sitename, $location;
		
		// outside stuff
		$this->sitename = $sitename;
		$this->location = $location;
		
		// default stuff to output header
		$this->title = 'bugspray';
		$this->addCSS($this->location['styles'].'/screen.css');
		$this->addJS('js/jquery-1.4.2.min.js');
		$this->addJS('js/html5.js');
		$this->addJS('js/bugspray.js');
		
		// start capturing the content
		ob_start();
	}
	
	public function disableTemplate()
	{
		$this->disabled = true;
	}
	
	public function setTitle($title)
	{
		global $sitename;
		$this->title = $title . ' | ' . $this->sitename;
	}
	
	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function addCSS($path)
	{
		$this->stylesheets[] = $path;
	}
	
	public function addJS($path)
	{
		$this->javascripts[] = $path;
	}
	
	public function addBodyPre($content)
	{
		$this->bodypre .= $content;
	}
	
	public function outputBodyPre()
	{
		echo $this->bodypre;
	}
	
	public function getMenu()
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
	
	public function outputHead()
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
	
	public function outputContent()
	{		
		echo $this->content;
		
		global $debug, $debug_log;
		if ($debug)
		{
			$tqueries = 0;
			
			$debugout = '
				<table id="debug">
					<thead>
						<tr>
							<th style="width:80px;">Type</th>
							<th>Description</th>
						</tr>
					</thead>
					<tbody>
			';
			
			foreach ($debug_log as $log)
			{
				switch ($log['type'])
				{
					case 'query':
						$tqueries++;
						$debugout .= '<tr><td>Query #' . $tqueries . '</td><td>' . $log['text'] . '</td>';
						break;
					default:
						$debugout .= '<tr><td>Unknown</td><td>' . $log['text'] . '</td>';
						break;
				}
			}
			
			$debugout .= '
					</tbody>
				</table>
			';
			
			echo "\n" . str_replace(array("\n","\r","\r\n","\t"), '', $debugout) . "\n";
		}
	}
	
	public function setPage($page,$variables=array())
	{
		global $debug_log, $client, $users;
		
		extract($variables,EXTR_SKIP);
		$location = $this->location;
		if (!file_exists($this->location['theme'].'/'.$page))
		{
			include('thm/default/'.$page);
		}
		else
		{
			include($this->location['theme'].'/'.$page);
		}
		echo "\n";
	}
	
	private function build()
	{
		global $debug_log, $client;
		
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
	
	public function outputAll()
	{
		// stop capturing everything
		$this->content = ob_get_clean();
		
		// build the page
		echo $this->build();
	}
}

?>