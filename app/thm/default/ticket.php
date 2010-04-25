<article>
	<div class="imgtitle imgtitle-32">
		<img class="image" src="<?php echo $location['images']; ?>/titles/tickets.png" alt="" />
		<div class="text">
			<h1 class="has-subtitle"><?php echo $issue['name']; ?></h1>
			<div class="subtitle">&laquo; <a href="#"><?php echo getcatnm($issue['category']); ?></a></div>
		</div>
		<div class="clear"></div>
	</div>
	
	<table class="ibox_details">
		<tr>
			<td>Tag(s)</td>
			<td>
			
			<?php
				$tags = explode(' ', $issue['tags']); // todo: use the separate table for tags instead of one long string
				foreach ($tags as $tag)
				{
					echo '<span class="tag">' . $tag . '</span>';
				}
			?>
			
			</td>
		</tr>
		<tr>
			<td>Status</td>
			<td><?php echo getstatusnm($issue['status']); ?></td>
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
		<?php if ($client['is_admin']): ?>
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
					$("#manage_delete").click(function(){
						this.blur();
						confirmurl(
							'Delete issue',
							'manage_issue.php?id=<?php echo $issue['id']; ?>&action=delete',
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
		<!-- TODO: Use the same template as below for this -->
		<article class="comment first">
			<table>
				<tr>
					<td>
						<div class="user"><?php echo getuinfo($issue['author'], false); ?></div>
						<div class="time">Posted on <?php echo timehtml5($issue['when_opened'], true); ?></div>
					</td>
				</tr>
				<tr>
					<td class="main">
						<div class="content"><?php echo parsebbcode($issue['description']); ?></div>
					</td>
				</tr>
			</table>
		</article>
		
		<?php foreach ($comments as $comment): ?>
		<article id="comment_<?php echo $comment['id']; ?>" class="comment<?php echo $comment['type'] != '' ? ' moderation' : ''; ?>">
			<table>
				<tr>
					<td colspan="2">
						<div class="user"><?php echo getuinfo($comment['author'], false); ?></div>
						<div class="time">Posted on <?php echo timehtml5($comment['when_posted'], true); ?></div>
					</td>
				</tr>
				<tr>
					<td class="main">
						<div class="content"><?php echo parsebbcode($comment['content']); ?></div>
					</td>
					<td class="actions">
						<?php if ($comment['type'] == ''): ?>
						<!--<img src="<?php echo $location['images']; ?>/btnmini/edit.png" alt="" />-->
						<img class="comment_quote" src="<?php echo $location['images']; ?>/btnmini/quote.png" alt="" />
						<!--<img src="<?php echo $location['images']; ?>/btnmini/report.png" alt="" />-->
						<?php endif; ?>
						<?php if ($comment['author'] == $_SESSION['uid'] || $client['is_admin']): ?>
						<img class="comment_delete" src="<?php echo $location['images']; ?>/btnmini/delete.png" alt="" />
						<?php endif; ?>
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
		<?php if ($client['is_logged']): ?>
		<form id="comment_form" class="ajax">
			<input type="hidden" name="id" value="<?php echo $issue['id']; ?>" />
			<small>You can use BBcode here, like [b] and [url]. If you don't want something to be parsed, use [noparse]!</small><br />
			<textarea rows="6" style="width:600px;" name="content" id="comment_form"></textarea>
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
	</section>
</article>