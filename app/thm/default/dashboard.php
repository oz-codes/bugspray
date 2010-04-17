<?php if ($client['is_logged']) : ?>
<section id="dashboard-following">
	<div class="imgtitle imgtitle-32">
		<img class="image" src="<?php echo $location['images']; ?>/titles/following.png" alt="" />
		<div class="text">
			<h1>What you're following</h1>
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
						Ticket filtering doesn't work here right now
					</form>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($issues as $issue): ?>
		
		<tr class="ticket" data-id="<?php echo $issue['id'] ?>">
			<td class="status"><div style="background:<?php echo $issue['status_color'] ?>"></div></td>
			<td class="favorite"><a href="javascript:;"><img src="<?php echo $location['images']; ?>/star-<?php echo $issue['favorite'] ? 'on' : 'off' ?>.png" alt="<?php echo $issue['favorite'] ? '&#9733;' : '&#9734;' ?>" /></a></td>
			<td class="id"><?php echo $issue['id']; ?></td>
			<td class="summary">
				<a href="ticket.php?id=<?php echo $issue['id'] ?>"><?php echo $issue['name'] ?></a>
				<span class="tag"><?php echo gettagnm($issue['tags']) ?></span>
			</td>
			<td class="category"><a href="#"><?php echo getcatnm($issue['category']) ?></a></td>
			<td class="assigned<?php echo $issue['assign'] == $_SESSION['uid'] && $issue['status'] < 3 ? ' you' : '' ?>"><?php echo $issue['assign'] > 0 ? '<a href="profile.php?id=' . $issue['assign'] . '">' . getunm($issue['assign']) . '</a>' : '--' ?></td>
			<td class="last"><?php echo timeago($issue['when_updated'], false, true) ?></td>
		</tr>
		
		<?php endforeach; ?>
		</tbody>
	</table>
</section>
<?php endif; ?>

<section id="dashboard-new">
	<div class="imgtitle imgtitle-32">
		<img class="image" src="<?php echo $location['images']; ?>/titles/new.png" alt="" />
		<div class="text">
			<h1>What's been happening</h1>
		</div>
		<div class="clear"></div>
		
		<!-- maybe merge assigned and last columns here to show the last activity/activities that happened -->
	</div>
</section>