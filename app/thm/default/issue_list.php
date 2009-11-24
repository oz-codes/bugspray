<h2 class="fl">Issue list</h2>
<div class="fr">
	<button type="button" onclick="location.href='add_issue.php'"><img src="<?php echo $location['images']; ?>/btn/add.png" alt="" />Add an issue</button>
</div>
<div class="fc"></div>

<div class="tabs">
	<?php foreach ($status_tabs as $tab): ?>
	<a href="<?php echo $tab['url']; ?>"<?php echo $tab['sel'] ? ' class="sel"' : ''; ?>><?php echo $tab['name']; ?></a>
    <?php endforeach; ?>
	<div class="fc"></div>
</div>

<table class="issuelist_large">
	<?php foreach ($issues as $issue): ?>
	<tr<?php echo $issue['closed'] ? ' class="closed"' : ''; ?>>
		<td class="col">
			<div style="background:<?php echo $issue['status_color']; ?>"></div>
		</td>
		<td class="comments">
			<div><?php echo $issue['num_comments']; ?></div>
			<div>comment<?php echo $issue['num_comments'] == 1 ? '' : 's'; ?></div>
		</td>
		<td class="views">
			<div>?</div>
			<div>views</div>
		</td>
		<td class="main">
			<div class="upper">
				<div class="left">
					<a href="view_issue.php?id=<?php echo $issue['id']; ?>"><?php echo $issue['name']; ?></a>
					in <a href="#"><?php echo getprojnm($issue['project']); ?></a>
				</div>
				<div class="right">
					<b>tagged as</b> <?php echo getcattag($issue['category']); ?>
				</div>
				<div class="fc"></div>
			</div>
			
			<div class="lower">
				<div class="left">
					<?php echo getuinfo($issue['author']); ?>
				</div>
				<div class="right">
					<b>last</b> <?php echo $issue['lastcomment']; ?>
				</div>
			</div>
		</td>
	</tr>
    <?php endforeach; ?>
</table>