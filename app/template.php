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

// define the locations of everything
$theme = $config['theme'];
$location = array(
	'includes'    => "sp-includes",
	'javascripts' => "sp-includes/js",
	'content'     => "sp-content",
	'themes'      => "sp-content/themes",
	'theme'       => "sp-content/themes/$theme",
	'images'      => "sp-content/themes/$theme/img",
	'styles'      => "sp-content/themes/$theme/css"
);

// set up everything
$page = new SPTemplate();
register_shutdown_function(array($page, 'finish'));

class SPTemplate
{
	private $title, $content, $stylesheets=array(), $javascripts=array(), $disabled=false, $location;
	
	function __construct()
	{
		global $location, $config;
		
		// Outside stuff
		$this->location = $location;
		
		// Default stuff to output to the header
		$this->title = 'spray';
		$this->addCSS($this->location['styles'] . '/screen.css');
		
		// Enqueue the bugspray JavaScript, which we always need
		$this->script_enqueue('spray', $this->location['javascripts'] . '/bugspray.js', array('jquery'));
		
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
					$this->javascripts['jquery'] = $this->location['javascripts'] . '/jquery-1.4.2.min.js';
					break;
				
				// Allow for HTML5 support in Internet Explorer
				case 'html5ie':
					$this->javascripts['html5'] = $this->location['javascripts'] . '/html5.js';
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
	
	public function get_menu()
	{
		// Grab the menu
		include($this->location['content'] . '/menu.php');
		
		// Cycle through the menu to see if we have an item matching our current page type
		foreach ($menu as &$menuitem)
		{
			$menuitem['selected'] = $menuitem['id'] == $this->type ? true : false;
		}
		
		// And we're done with this!
		return $menu;
	}
	
	public function get_head()
	{
		global $config;
		
		// Form the title
		$string = '<title>' . $this->title . ' &laquo; ' . $config['sitename'] . '</title>'."\n";
		
		// We'll just have it UTF-8 at all times for now
		$string .=  "\t".'<meta charset="UTF-8" />'."\n";
		
		// Output the inclusion of stylesheet files
		foreach ($this->stylesheets as $stylesheet)
		{
			$string .=  "\t".'<link rel="stylesheet" type="text/css" href="'.$stylesheet.'" />'."\n";
		}
		
		// Output the inclusion of JavaScript files
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
	
	public function include_template($page, $variables=array())
	{
		global $config, $users;
		
		// Grab the variables
		extract($variables, EXTR_SKIP);
		
		// Convenience variables for use by the included template
		$location = $this->location;
		
		// If the desired file doesn't exist, we include it from the default theme
		if (!file_exists($this->location['theme'] . '/template_' . $page . '.php'))
		{
			include($this->location['themes'] . '/default/template_' . $page . '.php');
		}
		else
		{
			include($this->location['theme'] . '/template_' . $page . '.php');
		}
		
		// This is to allow for nice spacing
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
				<p>
					This page was generated by <del>unicorn powers</del> spray ' . sp_get_version() . ' in ' . $totaltime . ' seconds
				</p>
			</section>
		';
		
		return "\n" . $debugout . "\n";
	}
	
	public function finish()
	{
		global $config, $client, $debug;
		
		// Stop capturing all that lovely content
		$this->content = ob_get_clean();
		
		// Don't want the overall stuff? All coo' :D
		if ($this->disabled)
		{
			echo $this->content;
			return true;
		}
		
		// Alright, the overall template can handle the content...
		ob_start();
		$this->include_template('overall');
		$out = ob_get_clean();
		
		// To ensure nothing at all is run (at least, most stuff) after debug info, we stick it at the end :O
		if ($debug)
		{
			$out = str_replace('</body>', $this->build_debug() . '</body>', $out);
		}
		
		// Do we want to strip whitespace used for making the source readable?
		if ($config['stripwhitespace'])
		{
			// Tabs are interpreted as spaces by browsers, so keep in mind usage of that
			$out = str_replace("\n\t", "\n ", $out);
			
			// We can remove a bit more
			$out = str_replace('> </', '></', $out);
			
			// And now we get rid of the rest
			$out = str_replace(array("\n","\r","\r\n","\t"), '', $out);
		}
		
		echo $out;
	}
}

?>