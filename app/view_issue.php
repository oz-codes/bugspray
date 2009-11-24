<?php
/*
 * bugspray issue tracking software
 * Copyright (c) 2009 a2h - http://a2h.uni.cc/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Under section 7b of the GNU General Public License you are
 * required to preserve this notice. Additional attribution may be
 * found in the NOTICES.txt file provided with the Program.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

include("functions.php");
$page->setType('issues');

$id = escape_smart($_GET['id']);

$result_issues = db_query("SELECT * FROM issues WHERE id = '$id' LIMIT 1");

if (mysql_num_rows($result_issues))
{
$issue = mysql_fetch_array($result_issues);
$page->setTitle($issue['name']);
?>

<article>

<h2>Viewing issue "<?php echo $issue['name']; ?>"</h2>

<table class="ibox_details">
	<tr>
		<td style="width:128px;">
			<b>Opened by</b>
		</td>
		<td>
			<?php echo getuinfo($issue['author']); ?>
		</td>
	</tr>
	<tr>
		<td>
			<b>Opened on</b>
		</td>
		<td>
			<?php echo timehtml5($issue['when_opened'],true); ?>
		</td>
	</tr>
	<tr>
		<td>
			<b>Categorised under</b>
		</td>
		<td>
			<?php echo getcattag($issue['category']); ?>
		</td>
	</tr>
	<tr>
		<td>
			<b>Project</b>
		</td>
		<td>
			<?php echo getprojnm($issue['project']); ?>
		</td>
	</tr>
	<tr>
		<td>
			<b>Status</b>
		</td>
		<td>
			<?php echo getstatusnm($issue['status']); ?>
		</td>
	</tr>
	<tr>
		<td>
			<b><?php echo ($issue['status'] == 3 ? 'Was assigned to' : 'Assigned to'); ?></b>
		</td>
		<td>
			<?php
				if ($issue['assign'] > 0)
				{
					echo getuinfo($issue['assign']);
				}
				else
				{
					if ($issue['status'] != 3)
						echo '<div class="fl" style="margin-right:4px;"><img src="img/alert/exclaim.png" alt="" /></div><div class="fl">unassigned</div>';
					else
						echo 'nobody';
				}
			?>
			<div class="fc"></div>
		</td>
	</tr>
	<?php
	if (isadmin())
	{
	?>
	<tr>
		<td>
			<b>Manage</b>
		</td>
		<td>
			<?php
				// get list of assignable users
				$assignsarr = array(array(-1,'nobody'),array(-1,'----------------------'));
				$result_userproject = db_query("SELECT * FROM assigns_userproject WHERE projectid = ".$issue['project']);
				while ($assign = mysql_fetch_array($result_userproject))
				{
					$enableme = $assign['userid'] == $issue['assign'] ? true : false;
					
					$assignsarr[] = array(
						$assign['userid'],
						str_replace('"',"\'",getunm($assign['userid'])),
						$enableme
					);
				}
				$assigns = str_replace('"',"'",json_encode($assignsarr));
				
				// change status
				echo '<button type="button" onclick="this.blur();changestatus('.$id.','.$issue['status'].','.$assigns.')"><img src="img/btn/change_status.png" />Change status</button> ';
				
				// lock/unlock discussion
				if (!$issue['discussion_closed'])
					echo '<button type="button" onclick="this.blur();confirmurl(\'Lock discussion\',\'manage_issue.php?id='.$id.'&amp;lock\',false)"><img src="img/btn/lock.png" />Lock discussion</button> ';
				else
					echo '<button type="button" onclick="this.blur();confirmurl(\'Unlock discussion\',\'manage_issue.php?id='.$id.'&amp;lock\',false)"><img src="img/btn/unlock.png" />Unlock discussion</button> ';
				
				// delete issue
				echo '<button type="button" onclick="this.blur();confirmurl(\'Delete issue\',\'manage_issue.php?id='.$id.'&amp;delete\',true);"><img src="img/btn/delete.png" />Delete issue</button>';
			?>
		</td>
	</tr>
	<?php
	}
	?>
</table><br />
<br />

<h3>Description</h3>
<div class="mono">
	<?php echo parsebbcode($issue['description']); ?>
</div>

<br />
<br />

<h3>Comments</h3>
<?php
$result_comments = db_query("SELECT * FROM comments WHERE issue = $id ORDER BY when_posted DESC");

$y=0;
while ($comment = mysql_fetch_array($result_comments))
{
	$y=1;
	
	echo '
	<article id="comment_'.$comment['id'].'" class="ibox_comment"' . ( $comment['type'] != '' ? ' style="background-image:url(img/bgcom/'.$comment['type'].'.png);"' : '' ) . '>
		<table>
			<tr>
				<td class="left">
					'.getuinfo($comment['author'],false).'
				</td>
				<td class="right">
					<div class="time">Posted on '.timehtml5($comment['when_posted'],true).'</div>
					<div class="mono">'.parsebbcode($comment['content']).'</div>
				</td>
			</tr>
		</table>
	</article>';
}

if ($y == 0)
{
	echo 'No comments exist for this... yet.<br /><br />';
}
?>

<h3>Add a comment</h3>
<?php
if (!$issue['discussion_closed'] || isadmin())
{
	if (isloggedin())
	{
	?>
	<form action="add_comment.php" method="post" class="ajax">
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
		<small>You can use BBcode here, like [b] and [url]. If you don't want something to be parsed, use [noparse]!</small><br />
		<textarea rows="4" style="width:600px;" name="cont" class="mono"></textarea>
		<br />
		<?php
			if ($issue['discussion_closed'])
			{
				echo '
				<div style="margin:4px 0px;">
					<div class="fl" style="margin-right:4px;"><img src="img/alert/exclaim.png" alt="" /></div>
					<div class="fl">Note that discussion has been <b>locked</b>; you are only able to add comments as you are a mod/admin.</div>
					<div class="fc"></div>
				</div>';
			}
		?>
		<input type="submit" value="Add" />
	</form>
	<?php
	}
	else
	{
		echo 'You need to be logged in to comment.';
	}
}
else
{
	echo 'Discussion has been closed.';
}
?>

</article>

<?php
}
else
{
	$page->setTitle('Error');
	echo 'That issue doesn\'t exist!';
}
?>