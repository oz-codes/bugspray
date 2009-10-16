<?php
/*
 * bugspray
 * Copyright 2009 a2h - http://a2h.uni.cc/
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * http://a2h.github.com/bugspray/
 *
 */
?>

<div class="ibox_error">NOT IMPLEMENTED YET</div><br />

Keep your issues under order! You can have, say, "bug-low", "bug-medium", and "bug-severe", then while you're at it have "suggestion".

<br />
<br />

<h3>Listing</h3>

To change the colour of a category, just click the colour. It's that simple.

<br />
<br />

<?php
$result_categories = db_query("SELECT * FROM categories");

$cidarr = array();

while ($category = mysql_fetch_array($result_categories))
{
	echo '
	<div class="colorlist">
		<input id="cat'.$category['id'].'" type="text" name="cat'.$category['id'].'" value="#'.$category['color'].'" />
		<label for="cat'.$category['id'].'">'.$category['name'].'</label>
		<div class="a"><a href="#">(list)</a> <a href="#">(rename)</a> <a href="#">(delete)</a></div>
		<div class="fc"></div>
	</div>';
	
	$cidarr[] = $category['id'];
}

echo '
<script type="text/javascript">
$.fn.colorPicker.defaultColors = [
	"FF3F3F","FF7F7F","FFBFBF",
	"FF3FFF","FF7FFF","FFBFFF",
	"FFB23F","FFCC7F","FFE5BF",
	"FFFF3F","FFFF7F","FFFFBF",
	"3FBFFF","7FD4FF","BFE9FF",
	"3FFFFF","7FFFFF","BFFFFF",
	"7FFF3F","AAFF7F","D4FFBF",
	"7F66FF","947FFF","C9BFFF"
];';

foreach ($cidarr as $cid)
{
	echo '
	$("#cat'.$cid.'").colorPicker();';
}

echo '
</script>';
?>