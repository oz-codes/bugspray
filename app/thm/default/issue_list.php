<div class="imgtitle imgtitle-32">
	<img class="image" src="<?php echo $location['images']; ?>/titles/tickets.png" alt="" />
	<div class="text">
		<h1>Tickets</h1>
	</div>
	<div class="right">
		<button type="button" onclick="location.href='add_issue.php'"><img src="<?php echo $location['images']; ?>/btn/add.png" alt="" />Add an issue</button>
	</div>
	<div class="clear"></div>
</div>

<table class="tickets">
	<thead>
		<tr>
			<th class="status"></th>
			<th class="star"></th>
			<th class="id"><a href="#">#</a></th>
			<th class="summary"><a href="#">Summary</a></th>
			<th class="category"><a href="#">Category</a></th>
			<th class="assigned"><a href="#">Assigned</a></th>
			<th class="last"><a href="#">Last</a></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="7">
				<form id="tickets-filter">
					Showing
					<select name="status" onchange="location.href=this.options[selectedIndex].value">
						<?php foreach ($status_tabs as $tab): ?>
						<option value="<?php echo $tab['url']; ?>"<?php echo $tab['sel'] ? ' selected' : ''; ?>><?php echo strtolower($tab['name']); ?></a>
						<?php endforeach; ?>
					</select>
					tickets
				</form>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php foreach ($issues as $issue): ?>
	
	<tr class="ticket" data-id="<?php echo $issue['id'] ?>">
		<td class="status"><div style="background:<?php echo $issue['status_color'] ?>"></div></td>
		<td class="star"><img src="<?php echo $location['images']; ?>/star-<?php echo $issue['favorite'] ? 'on' : 'off' ?>.png" alt="<?php echo $issue['favorite'] ? '&#9733;' : '&#9734;' ?>" /></td>
		<td class="id"><?php echo $issue['id']; ?></td>
		<td class="summary">
			<a href="view_issue.php?id=<?php echo $issue['id'] ?>"><?php echo $issue['name'] ?></a>
			<span class="tag"><?php echo gettagnm($issue['tags']) ?></span>
		</td>
		<td class="category"><a href="#"><?php echo getcatnm($issue['category']) ?></a></td>
		<td class="assigned<?php echo $issue['assign'] && $issue['status'] < 3 == $_SESSION['uid'] ? ' you' : '' ?>"><?php echo $issue['assign'] > 0 ? '<a href="profile.php?id=' . $issue['assign'] . '">' . getunm($issue['assign']) . '</a>' : '--' ?></td>
		<td class="last"><?php echo timeago($issue['when_updated'], false, true) ?></td>
	</tr>
	
    <?php endforeach; ?>
	</tbody>
	
</table>