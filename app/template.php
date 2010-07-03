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
$theme = $config['theme'];
$location = array(
	'theme'  => "themes/$theme",
	'images' => "themes/$theme/img",
	'styles' => "themes/$theme/css"
);

// set up everything
$page = new MTTemplate();
register_shutdown_function(array($page,'outputAll'));

class MTTemplate
{
	public $sitename;
	private $title, $content, $stylesheets=array(), $javascripts=array(), $bodypre, $disabled=false, $location;
	
	function __construct()
	{
		global $sitename, $location, $config;
		
		// Outside stuff
		$this->sitename = $config['sitename'];
		$this->location = $location;
		
		// Default stuff to output to the header
		$this->title = 'spray';
		$this->addCSS($this->location['styles'] . '/screen.css');
		
		// Enqueue the bugspray JavaScript, which we always need
		$this->script_enqueue('spray', 'js/bugspray.js', array('jquery'));
		
		// Alrighty, let's start capturing content!
		ob_start();
	}
	
	public function theme_disable($do)
	{
		$this->disabled = $do;
	}
	
	public function script_enqueue($id, $path='', $depends=array())
	{
		// Don't reinclude things, of course
		if (!$this->javascripts[$id])
		{
			// Predetermined stuff and... stuff
			switch ($id)
			{
				// jQuery, of course
				case 'jquery':
					$this->javascripts['jquery'] = 'js/jquery-1.4.2.min.js';
					break;
				
				// Allow for HTML5 support in Internet Explorer
				case 'html5ie':
					$this->javascripts['html5'] = 'js/html5.js';
					break;
				
				// Something we don't know about?
				default:
					// If there's no path, ignore the request (and of course log it)
					if (!$path)
					{
						global $debug, $debug_log;
						$debug_log[] = array(
							'text' => 'Something tried to enqueue a script with an unrecognised id "' . $id . '", but there was no path supplied!'
						);
					}
					else
					{
						// Do we have dependencies?
						if (!empty($depends))
						{
							foreach ($depends as $depend)
							{
								$this->script_enqueue($depend);
							}
						}
						
						// Alright, let's enqueue our actual script!
						$this->javascripts[$id] = $path;
						break;
					}
			}
		}
	}
	
	public function setTitle($title)
	{
		global $sitename;
		$this->title = $title;
	}
	
	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function addCSS($path)
	{
		$this->stylesheets[] = $path;
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
	
	public function get_head()
	{		
		$string = '<title>' . $this->title . ' &laquo; ' . $this->sitename . '</title>'."\n";
		
		$string .=  "\t".'<meta charset="UTF-8">'."\n";
		
		foreach ($this->stylesheets as $stylesheet)
		{
			$string .=  "\t".'<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
		}
		foreach ($this->javascripts as $javascript)
		{
			$string .=  "\t".'<script type="text/javascript" src="'.$javascript.'"></script>'."\n";
		}
		
		return $string;
	}
	
	public function output_head()
	{
		echo $this->get_head();
	}
	
	public function outputContent()
	{		
		echo $this->content;
	}
	
	public function setPage($page,$variables=array())
	{
		global $debug_log, $client, $users;
		
		extract($variables,EXTR_SKIP);
		$location = $this->location;
		if (!file_exists($this->location['theme'].'/'.$page))
		{
			include('themes/default/'.$page);
		}
		else
		{
			include($this->location['theme'].'/'.$page);
		}
		echo "\n";
	}
	
	private function build_debug()
	{
		global $debug, $debug_log;
		
		$tqueries = 0;
		
		// First, we output the top
		$debugout = '
			<section id="debug">
				<h1>Debug output</h1>
				<hr />
				<table>
					<thead>
						<tr>
							<th style="width:80px;">Type</th>
							<th>Description</th>
						</tr>
					</thead>
					<tbody>
		';
		
		// Output each of the log stuff
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
		
		// How long'd it take to generate this page?
		global $starttime;
		$mtime = explode(' ', microtime());
		$totaltime = sprintf('%.5f', $mtime[0] + $mtime[1] - $starttime);
		
		// And now output the bottom, along with the generation time
		$debugout .= '
					</tbody>
				</table>
				<hr />
				<p>
					This page was generated by <del>unicorn powers</del> spray 0.3-dev in ' . $totaltime . ' seconds
				</p>
			</section>
		';
		
		return "\n" . str_replace(array("\n","\r","\r\n","\t"), '', $debugout) . "\n";
	}
	
	private function build_page()
	{
		global $debug, $client;
		
		// Don't want the overall stuff? All coo' :D
		if ($this->disabled)
		{
			return $this->content;
		}
		// Well, we actually do then!
		else
		{
			// Alright, the overall stuff can handle the content
			ob_start();
			$location = $this->location;
			include($this->location['theme'] . '/overall.php');
			$content = ob_get_clean();
			
			// To ensure nothing at all is run (at least, most stuff) after debug info, we stick it at the end :O
			if ($debug)
			{
				$content = str_replace('</body>', $this->build_debug() . '</body>', $content);
			}
			
			return $content;
		}
	}
	
	public function outputAll()
	{
		// Stop capturing all that lovely content
		$this->content = ob_get_clean();
		
		// Build the page
		echo $this->build_page();
	}
}

?>