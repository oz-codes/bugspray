<h1>Dashboard</h1>

<section id="dashboard-following">
	<div class="imgtitle imgtitle-32">
		<img src="<?php echo $location['images']; ?>/titles/following.png" alt="" />
		<div class="text">
			<h1>What you're following</h1>
			<div class="subtitle">
				Filter:
			</div>
		</div>
		<div class="clear"></div>
	</div>
	
	<table class="tickets">
		<thead>
			<tr>
				<th class="status" style="width: 4px;"></th>
				<th style="width: 16px;"></th>
				<th style="width: 8px;"><a href="#">#</a></th>
				<th><a href="#">Summary</a></th>
				<th style="width: 128px;"><a href="#">Category</a></th>
				<th style="width: 96px;"><a href="#">Assigned</a></th>
				<th style="width: 48px;"><a href="#">Last</a></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="status"><div style="background:#ffae00"></div></td>
				<td><img src="<?php echo $location['images']; ?>/star.png" alt="*" /></td>
				<td>5</td>
				<td>Quoting doesn't work</td>
				<td class="lesser">Example Category</td>
				<td class="lesser">--</td>
				<td class="lesser">2d ago</td>
			</tr>
			<tr>
				<td class="status"><div style="background:#ffde00"></div></td>
				<td><img src="<?php echo $location['images']; ?>/star-off.png" alt="*" /></td>
				<td>12</td>
				<td>Default theme doesn't work in all WebKit-based browsers</td>
				<td class="lesser">Example Category</td>
				<td class="lesser">a2h</td>
				<td class="lesser">1h ago</td>
			</tr>
		</tbody>
	</table>
</section>

<section id="dashboard-new">
	<div class="imgtitle imgtitle-32">
		<img src="<?php echo $location['images']; ?>/titles/new.png" alt="" />
		<div class="text">
			<h1>What's been happening</h1>
			<div class="subtitle">
				Filter:
			</div>
		</div>
		<div class="clear"></div>
	</div>
</section>