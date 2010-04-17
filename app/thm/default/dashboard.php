<?php if ($client['is_logged']) : ?>
<section id="dashboard-following">
	<div class="imgtitle imgtitle-32">
		<img class="image" src="<?php echo $location['images']; ?>/titles/following.png" alt="" />
		<div class="text">
			<h1>What you're following</h1>
		</div>
		<div class="clear"></div>
	</div>
	
	<?php echo $tickets_following ?>
</section>
<?php endif; ?>

<section id="dashboard-new">
	<div class="imgtitle imgtitle-32">
		<img class="image" src="<?php echo $location['images']; ?>/titles/new.png" alt="" />
		<div class="text">
			<h1>What's been happening</h1>
		</div>
		<div class="clear"></div>
		
		<p>To be implemented...</p>
	</div>
</section>