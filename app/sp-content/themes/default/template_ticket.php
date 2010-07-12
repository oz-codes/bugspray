<article>
	<div class="imgtitle imgtitle-32">
		<img class="image" src="<?php echo $location['images']; ?>/titles/tickets.png" alt="" />
		<div class="text">
			<h1 class="left"><?php echo $issue['name']; ?></h1>
		</div>
		
		<?php
			echo_tags($issue['tags']);
		?>
		
		<?php if ($users->client->is_admin): ?><div class="right">
			<a class="button drop-button" data-drop="drop-config" href="#"><img src="<?php echo $location['images']; ?>/btn/config.png" alt="" /></a>
			<div class="drop drop-right" id="drop-config">
				<ul>
					<li>
						<a href="#" onclick="confirmurl('Delete ticket', 'manage_issue.php?id=<?php echo $issue['id']; ?>&action=delete', true)">Delete this ticket</a>
					</li>
				</ul>
			</div>
		</div>
		<?php endif; ?>
		
		<div class="clear"></div>
	</div>
	
	<table class="details">
		<tr>
			<td>Status</td>
			<td><?php echo getstatusnm($issue['status']) ?></td>
		</tr>
		<tr>
			<td>Severity</td>
			<td><?php echo $issue['severity']; ?>/5 <small>[todo: show text not a number]</small></td>
		</tr>
		<tr>
			<td><?php echo ($issue['status'] == 3 ? 'Was assigned to' : 'Assigned to'); ?></td>
			<td>
				<?php echo $issue['assignedto']; ?>
				<div class="fc"></div>
			</td>
		</tr>
                <?php
                if($issue['misc'] != "") {
                   list($left, $right) = explode("=",$issue['misc'],2);
                 ?>
                <tr>
                    <td>
                     <?php echo $left;?>
                    </td>
                    <td>
                     <?php echo $right;?>
                    </td>
                </tr>
                <?php
                 }
                 ?>
	</table>
	
	<br />
	<br />
	
	<section>
		<!-- TODO: Use the same template as below for this -->
		<article class="comment first">
			<table>
				<tr>
					<td>
						<div class="user"><?php echo getuinfo($issue['author'], false); ?></div>
						<div class="time">Posted on <?php echo timehtml5($issue['when_opened'], true); ?></div>
                                                <div class="actions">
							<img class="comment_quote" src="<?php echo $location['images']; ?>/btnmini/quote.png" alt="" />
						</div>
					</td>
				</tr>
				<tr>
					<td class="content">
						<?php echo parsebbcode($issue['description']); ?>
					</td>
				</tr>
			</table>
		</article>
		
		<?php foreach ($comments as $comment): ?>
		<article id="comment_<?php echo $comment['id']; ?>" class="comment<?php echo $comment['type'] != '' ? ' moderation' : ''; ?>">
			<table>
				<tr>
					<td>
						<div class="user"><?php echo getuinfo($comment['author'], false); ?></div>
						<div class="time">Posted on <?php echo timehtml5($comment['when_posted'], true); ?></div>
						<div class="actions">
							<?php if ($comment['type'] == ''): ?>
							<!--<img src="<?php echo $location['images']; ?>/btnmini/edit.png" alt="" />-->
							<img class="comment_quote" src="<?php echo $location['images']; ?>/btnmini/quote.png" alt="" />
							<!--<img src="<?php echo $location['images']; ?>/btnmini/report.png" alt="" />-->
							<?php endif; ?>
							<?php if ($comment['author'] == $_SESSION['uid'] || $users->client->is_admin): ?>
							<img class="comment_delete" src="<?php echo $location['images']; ?>/btnmini/delete.png" alt="" />
							<?php endif; ?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="content">
						<?php echo parsebbcode($comment['content']); ?>
					</td>
				</tr>
			</table>
		</article>
		<?php endforeach; ?>
	</section>
	
	<section>
		<h3>Update this ticket</h3>
		<?php if ($users->client->is_logged): ?>
		<form id="comment_form" class="config" method="post">			
			<p class="form">
				<textarea name="comment" id="comment_form" class="unsel" rows="5" style="width:600px;">Enter a comment...</textarea>
			</p>
			
			<?php if ($users->client->is_admin): ?>
			
			<dl class="form col-3">
				<dt>
					<label for="status">Status</label>
				</dt>
				<dd>
					<select name="status" id="status">
						<option value="1">Open</option>
						<option value="2">Assigned</option>
						<option value="3">Resolved</option>
						<option value="4">Postponed</option>
						<option value="5">Declined</option>
                                                <option value="6">Duplicate</option>
					</select>
				</dd>
			</dl>
			<dl class="form col-3">
				<dt>
					<label for="assign">Assign to</label>
				</dt>
				<dd>
					<select name="assign" id="assign"><?php foreach ($issue['assigns'] as $assign) { echo '<option value="' . $assign[0] . '"' . ($assign[2] ? ' selected' : '') . '>' . $assign[1] . '</option>'; } ?></select>
				</dd>
			</dl>
			
			<div class="clear"></div>
			
			<?php endif ?>
			
			<input type="submit" name="submit" value="Add" disabled />
		</form>
		<?php else: ?>
		You need to be logged in to comment.
		<?php endif; ?>
	</section>
</article>
