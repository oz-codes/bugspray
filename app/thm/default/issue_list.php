<h1 class="left">Tickets</h2>
<div class="right">
	<button type="button" onclick="location.href='add_issue.php'"><img src="<?php echo $location['images']; ?>/btn/add.png" alt="" />Add an issue</button>
</div>
<div class="clear"></div>

<div class="tabs">
	<?php foreach ($status_tabs as $tab): ?>
	<a href="<?php echo $tab['url']; ?>"<?php echo $tab['sel'] ? ' class="sel"' : ''; ?>><?php echo $tab['name']; ?></a>
    <?php endforeach; ?>
	<div class="clear"></div>
</div>

<table class="tickets">
	<thead>
		<tr>
			<!-- perhaps do classes for all these columns -->
			<th class="status" style="width: 4px;"></th>
			<th style="width: 16px;"></th>
			<th style="width: 8px;"><a href="#">#</a></th>
			<th><a href="#">Summary</a></th>
			<th style="width: 128px;"><a href="#">Category</a></th>
			<th style="width: 96px;"><a href="#">Assigned</a></th>
			<th style="width: 64px;"><a href="#">Last</a></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($issues as $issue): ?>
	<tr>
		<td class="status"><div style="background:<?php echo $issue['status_color']; ?>"></div></td>
		<!--<td class="comments">
			<div><?php echo $issue['num_comments']; ?></div>
			<div>comment<?php echo $issue['num_comments'] == 1 ? '' : 's'; ?></div>
		</td>
		<td class="views">
			<div><?php echo $issue['num_views']; ?></div>
			<div>view<?php echo $issue['num_views'] == 1 ? '' : 's'; ?></div>
		</td>-->
		<td><!--<img src="<?php echo $location['images']; ?>/star.png" alt="*" />--></td>
		<td><?php echo $issue['id']; ?></td>
		<td>
			<a href="view_issue.php?id=<?php echo $issue['id']; ?>"><?php echo $issue['name']; ?></a>
			<span class="tag"><?php echo getcatnm($issue['category']); ?></span>
		</td>
		<td class="lesser"><a href="#"><?php echo getprojnm($issue['project']); ?></a></td>
		<td class="lesser">?</td>
		<td class="lesser"><?php echo timeago($issue['when_updated'], false, true); ?></td>
	</tr>
    <?php endforeach; ?>
	</tbody>
</table>