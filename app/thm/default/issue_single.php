<article>
	<h2>Viewing issue "<?php echo $issue['name']; ?>"</h2>
	
	<table class="ibox_details">
		<tr>
			<td style="width:128px;">Opened by</td>
			<td><?php echo getuinfo($issue['author']); ?></td>
		</tr>
		<tr>
			<td>Opened on</td>
			<td><?php echo timehtml5($issue['when_opened'],true); ?></td>
		</tr>
		<tr>
			<td>Categorised under</td>
			<td><?php echo getcattag($issue['category']); ?></td>
		</tr>
		<tr>
			<td>Project</td>
			<td><?php echo getprojnm($issue['project']); ?></td>
		</tr>
		<tr>
			<td>Status</td>
			<td><?php echo getstatusnm($issue['status']); ?></td>
		</tr>
		<tr>
			<td><?php echo ($issue['status'] == 3 ? 'Was assigned to' : 'Assigned to'); ?></td>
			<td>
				<?php echo $issue['assignedto']; ?>
				<div class="fc"></div>
			</td>
		</tr>
		<?php if (isadmin()): ?>
		<tr>
			<td>Manage</td>
			<td>
                <?php
					$ac = count($issue['assigns']);
					for ($i=0;$i<$ac;$i++)
					{
						$issue['assigns'][$i] = str_replace('"',"\'",$issue['assigns'][$i]);
					}
					$assigns = json_encode($issue['assigns']);
				?>
				
				<button type="button" id="manage_status">
					<img src="<?php echo $location['images']; ?>/btn/change_status.png" alt="" />
					Change status
				</button>
				<button type="button" id="manage_lock">
					<img src="<?php echo $location['images']; ?>/btn/<?php echo !$issue['discussion_closed'] ? '' : 'un'; ?>lock.png" alt="" />
					<?php echo !$issue['discussion_closed'] ? 'Lock' : 'Unlock'; ?> discussion
				</button>
				<button type="button" id="manage_delete">
					<img src="<?php echo $location['images']; ?>/btn/delete.png" alt="" />
					Delete issue
				</button>
				
				<script type="text/javascript">
					$("#manage_status").click(function(){
						this.blur();
						changestatus(
							<?php echo $issue['id']; ?>,
							<?php echo $issue['status']; ?>,
							<?php echo $assigns; ?> 
						);
					});
					$("#manage_lock").click(function(){
						this.blur();
						confirmurl(
							'<?php echo !$issue['discussion_closed'] ? 'Lock' : 'Unlock'; ?> discussion',
							'manage_issue.php?id=<?php echo $issue['id']; ?>&<?php echo !$issue['discussion_closed'] ? '' : 'un'; ?>lock',
							false
						);
					});
					$("#manage_delete").click(function(){
						this.blur();
						confirmurl(
							'Delete issue',
							'manage_issue.php?id=<?php echo $issue['id']; ?>&delete',
							true
						);
					});
				</script>
			</td>
		</tr>
        <?php endif; ?>
	</table>
	
	<br />
	<br />
	
	<section>
		<h3>Description</h3>
		<div class="cont">
			<?php echo parsebbcode($issue['description']); ?>
		</div>
	</section>
	
	<br />
	<br />
	
	<section>
		<h3>Comments</h3>
		<?php foreach ($comments as $comment): ?>
		<article id="comment_<?php echo $comment['id']; ?>" class="comment<?php echo $comment['type'] != '' ? ' moderation' : ''; ?>">
			<table>
				<tr>
					<td class="left">
						<div class="username">
							<?php echo getuinfo($comment['author'],false); ?>
						</div>
						<div class="actions">
							<!--<img src="<?php echo $location['images']; ?>/btnmini/edit.png" alt="" />-->
							<img class="comment_quote" src="<?php echo $location['images']; ?>/btnmini/quote.png" alt="" />
							<!--<img src="<?php echo $location['images']; ?>/btnmini/report.png" alt="" />-->
						</div>
					</td>
					<td class="right">
						<div class="time">Posted on <?php echo timehtml5($comment['when_posted'],true); ?></div>
						<div class="cont"><?php echo parsebbcode($comment['content']); ?></div>
					</td>
				</tr>
			</table>
		</article>
		<?php endforeach; ?>
		<?php if (count($comments) == 0): ?>
		No comments exist for this issue... yet.<br /><br />
		<?php endif; ?>
	</section>
	
	<section>
		<h3>Add a comment</h3>
		<?php if (!$issue['discussion_closed'] || isadmin()): ?>
		<?php if (isloggedin()): ?>
		<form action="add_comment.php" method="post" class="ajax">
			<input type="hidden" name="id" value="<?php echo $issue['id']; ?>" />
			<small>You can use BBcode here, like [b] and [url]. If you don't want something to be parsed, use [noparse]!</small><br />
			<textarea rows="6" style="width:600px;" name="cont" id="comment_form"></textarea>
			<?php if ($issue['discussion_closed']): ?>
			<div style="margin:4px 0px;">
				<div class="fl" style="margin-right:4px;"><img src="<?php echo $location['images']; ?>/alert/exclaim.png" alt="" /></div>
				<div class="fl">Note that discussion has been <b>locked</b>; you are only able to add comments as you are a mod/admin.</div>
				<div class="fc"></div>
			</div>
			<?php endif; ?>
			<br />
			<input type="submit" value="Add" />
		</form>
		<?php else: ?>
		You need to be logged in to comment.
		<?php endif; ?>
		<?php else: ?>
		Discussion has been closed.
		<?php endif; ?>
	</section>
</article>