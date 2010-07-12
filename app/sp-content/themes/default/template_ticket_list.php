<table class="tickets" data-type="<?php echo $type ?>">
	<thead>
		<tr>
			<th class="status"></th>
			<th class="star"></th>
			<th class="id"><a href="#">#</a></th>
			<th class="summary"><a href="#">Summary</a></th>
			<th class="replies"><a href="#">Replies</a></th>
			<th class="assigned"><a href="#">Assigned</a></th>
			<th class="last"><a href="#">Last</a></th>
                        <th class="status"><a href="#">Status</a></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="8">
				<form class="filter">
					Showing
					<select name="status">
						<?php foreach ($statuses as $status): ?>
						<option value="<?php echo $status['type']; ?>"<?php echo $status['type'] == $_COOKIE['current'] ? ' selected' : ''; ?>><?php echo strtolower($status['name']); ?></option>
						<?php endforeach; ?>
					</select>
					tickets
				</form>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php foreach ($tickets as $ticket): ?>
	
	<tr class="<?php echo $ticket['classes'] ?>" data-id="<?php echo $ticket['id'] ?>">
		<td class="status"><div class="status-color"></div></td>
		<td class="favorite">
			<a href="#">
				<img src="<?php echo $location['images']; ?>/star-<?php echo $ticket['favorite'] ? 'on' : 'off' ?>.png" alt="<?php echo $ticket['favorite'] ? '&#9733;' : '&#9734;' ?>" />
				<?php echo $ticket['favoritecount'] ? '<div class="favorite-count">' . $ticket['favoritecount'] . '</div>' : '' ?>
			</a>
		</td>
		<td class="id"><?php echo $ticket['id']; ?></td>
		<td class="summary">
			<a href="ticket.php?id=<?php echo $ticket['id'] ?>"><?php echo $ticket['name'] ?></a>
			
			<?php
			echo_tags($issue['tags']);
			?>
			
		</td>
		<td class="replies"><?php echo $ticket['num_comments'] ?></td>
		<td class="assigned<?php echo $ticket['assign'] == $_SESSION['uid'] && $ticket['status'] < 3 ? ' you' : '' ?>"><?php echo $ticket['assign'] > 0 ? '<a href="profile.php?id=' . $ticket['assign'] . '">' . getunm($ticket['assign']) . '</a>' : '--' ?></td>
		<td class="last"><?php echo timeago($ticket['when_updated'], false, true) ?></td>
                <td class="status"><?php echo getstatusnm($ticket["status"]); ?></td>
	</tr>
	
	<?php endforeach; ?>
	</tbody>
</table>
