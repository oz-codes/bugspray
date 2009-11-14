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
?>

Keep your issues under order! You can have, say, "bug-low", "bug-medium", and "bug-severe", then while you're at it have "suggestion".

<br />
<br />

<?php
if (!isset($_GET['s']))
{
	echo '
	<h3>Listing</h3>
	
	<button type="button" onclick="stringform(\'Add a category\',\''.$uri.'&amp;s=add\',{col:true})"><img src="img/btn/add.png" alt="" />Add a category</button>
	
	<br />
	<br />

	To change the colour of a category, just click the colour. <b>Not implemented</b>

	<br />
	<br />';

	$result_categories = db_query("SELECT * FROM categories");

	while ($category = mysql_fetch_array($result_categories))
	{
		echo '
		<div class="colorlist">
			<input id="cat'.$category['id'].'" class="catcol" type="text" name="cat'.$category['id'].'" value="#'.$category['color'].'" />
			<label for="cat'.$category['id'].'">'.$category['name'].'</label>
			<div class="a">
				<a href="#">(<s>list</s>)</a>
				<a href="javascript:void(0)" class="rnm">(rename)</a>
				<a href="javascript:void(0)" class="del">(delete)</a>
				<br />
				linked projects: all <a href="#">(<s>change</s>)</a>
			</div>
			<div class="fc"></div>
		</div>';
	}

	// some javascript for managing all this stuff...
	?>
	<script type="text/javascript">
		// the colour picker
		$.fn.colorPicker.defaultColors = [
			"FF3F3F","FF7F7F","FFBFBF",
			"FF3FFF","FF7FFF","FFBFFF",
			"FFB23F","FFCC7F","FFE5BF",
			"FFFF3F","FFFF7F","FFFFBF",
			"3FBFFF","7FD4FF","BFE9FF",
			"3FFFFF","7FFFFF","BFFFFF",
			"7FFF3F","AAFF7F","D4FFBF",
			"7F66FF","947FFF","C9BFFF"
		];
		$(".catcol").colorPicker();

		// category management buttons
		$(".colorlist .rnm").bind('click',function(e) {
			target = e.currentTarget;
			while (!$(target).hasClass('colorlist'))
			{
				target = target.parentNode;
			}
			
			stringform(
				'Rename category "' + $(target).find("label").text() + '"',
				'<?php echo $uri; ?>&s=rename&id=' + $(target).find("label").attr("for").replace('cat','')
			);
		});
		
		$(".colorlist .del").bind('click',function(e) {
			target = e.currentTarget;
			while (!$(target).hasClass('colorlist'))
			{
				target = target.parentNode;
			}
			
			confirmurl(
				'Delete category "' + $(target).find("label").text() + '"',
				'<?php echo $uri; ?>&s=delete&id=' + $(target).find("label").attr("for").replace('cat',''),
				true
			);
		});
	
	</script>
	<?php
}
else
{
	switch ($_GET['s'])
	{
		case 'add':
			echo '<h3>Adding category</h3>';
			$n = escape_smart($_POST['str']);
			$c = escape_smart(str_replace('#','',$_POST['col']));
			$query = db_query("INSERT INTO categories (name,color) VALUES ('$n','$c')");
			if ($query) { echo 'Added succesfully!'; } else { mysql_error(); }
			break;
		case 'rename':
			echo '<h3>Renaming category</h3>';
			$n = escape_smart($_POST['str']);
			$i = escape_smart($_GET['id']);
			$query = db_query("UPDATE categories set name='$n' WHERE id='$i'");
			if ($query) { echo 'Renamed succesfully!'; } else { mysql_error(); }
			break;
		case 'delete':
			echo '<h3>Delete category</h3>';
			$i = escape_smart($_GET['id']);
			$query = db_query("DELETE FROM categories WHERE id='$i'");
			if ($query) { echo 'Deleted succesfully!'; } else { mysql_error(); }
			break;
		default:
			echo 'What?';
			break;
	}
	echo '<br /><br /><a href="'.$uri2.'">Go back</a>';
}
?>