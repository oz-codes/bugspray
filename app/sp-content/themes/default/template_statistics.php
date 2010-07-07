<div class="imgtitle imgtitle-32">
	<img class="image" src="<?php echo $location['images']; ?>/titles/statistics.png" alt="" />
	<div class="text">
		<h1>Statistics</h1>
	</div>
	<div class="clear"></div>
</div>

<b>Logging has only been implemented for adding issues right now.</b><br /><br />

<?php
foreach ($projects as $project)
{
	echo '
	<section class="watchlist">
		<h3><a href="#">'.$project['name'].'</a></h3>
		<nav><a href="#"><img src="'.$location['images'].'/feed.png" alt="(RSS)" title="Subscribe to this feed" /></a></nav>
		<div class="fc"></div>
		';
		
	foreach ($project['logs'] as $log)
	{
		echo '
		<article>
			<div class="type">
				<img src="'.$location['images'].'/act/'.getactimg($log['actiontype']).'" alt="" />
			</div>
			<div class="user">
				'.getuinfo($log['userid'],false).'
			</div>
			<div class="details" style="margin:2px 0px 0px 4px;">
				<span style="background:#'.getactcol($log['actiontype']).'">'.getactlogdsc($log['actiontype']).'</span>
				categorised under <a href="#">'.getcatnm($log['category']).'</a>,
				called <a href="#">'.$log['name'].'</a>
			</div>
			<div class="fc"></div>
		</article>';
	}
		
	echo '
	</section>';
}

if (count($projects) == 0)
{
	echo '
	<section>
		Looks like there\'s no projects around here...
	</section>';
}
?>