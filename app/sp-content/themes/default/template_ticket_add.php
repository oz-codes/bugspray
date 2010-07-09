<div class="imgtitle imgtitle-32">
	<img class="image" src="<?php echo $location['images']; ?>/titles/tickets-add.png" alt="" />
	<div class="text">
		<h1>Add a ticket</h1>
	</div>
	<div class="clear"></div>
</div>

<form action="" method="post">
	
	<!-- title -->
	
	<?php echo output_errors($errors_title) ?>
	
	<dl class="form inline">
		<dt>
			<label for="title">Summary</label>
		</dt>
		<dd>
			<input id="title" name="title" type="text" maxlength="192" value="<?php echo $_POST['title'] ?>" />
			<div class="infobubble">
				<p>What's the problem? Remember to <b>K</b>eep <b>I</b>t <b>S</b>hort and <b>S</b>weet.</p>
				<p>You're limited to <b>192</b> characters here.</p>
			</div>
		</dd>
	</dl>
	
	<!-- tags -->
	
	<?php echo output_errors($errors_tags) ?>
	
	<dl class="form inline">
		<dt>
			<label for="tags">Tags</label>
		</dt>
		<dd>
			<input id="tags" name="tags" type="text" value="<?php echo $_POST['tags'] ?>" />			
			<div class="infobubble">
				<p>Tags are a multipurpose method of describing your ticket. Just separate them with spaces.</p>
				<p>You can provide up to <b>5</b> tags, and all the tags you provide can't be longer than <b>16</b> characters.</p>
				<p>For example, <code>windows-7 64-bit ui</code>.</p>
				<p class="small">[TODO: Suggested tags]</p>
			</div>
		</dd>
	</dl>
	
	<!-- severity -->
	
	<dl class="form inline">
		<dt>
			<label>Severity</label>
		</dt>
		<dd>
			<input id="severity" name="severity" type="hidden" value="0" />
			<div id="severity-slider" class="left" style="width: 422px; margin-top: 6px;"></div>
			<span id="severity-name" class="left small" style="margin-left: 4px; margin-top: 4px;">none</span>
			<script type="text/javascript">
				$("#severity-slider").slider({
					value: 0,
					min: 0,
					max: 5,
					slide: function(e, ui) {
						$("#severity").val(ui.value);
						
						var severityName = 'invalid';
						switch (ui.value)
						{
							case 0: severityName = 'none'; break;
							case 1: severityName = 'very low'; break;
							case 2: severityName = 'low'; break;
							case 3: severityName = 'medium'; break;
							case 4: severityName = 'severe'; break;
							case 5: severityName = 'very severe'; break;
						}
						$("#severity-name").html(severityName);
					}
				});
			</script>
		</dd>
	</dl>
	
	<div class="clear"></div>
	
	<hr class="invisible" />
	
	<!-- description -->
	
	<?php echo output_errors($errors_description) ?>
	
	<dl class="form">
		<dt>
			<label for="description">Describe the problem</label>
		</dt>
		<dd>
			<textarea id="description" name="description" style="height: 192px;"><?php echo $_POST['description'] ?></textarea>
		</dd>
	</dl>

	<input type="submit" name="submit" value="Post" />
</form>