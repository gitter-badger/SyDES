<?php if (!$form['hide_name']){ ?>
	<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-form-<?=$form['id'];?>" data-size="<?=$modal_size;?>"><?=$form['name'];?></button>
<?php } ?>

<div class="modal fade" id="modal-form-<?=$form['id'];?>" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?=$form['name'];?></h4>
			</div>
			<div class="modal-body">
				<form method="post" enctype="multipart/form-data" action="/constructors/form/send" <?=H::attr($form_attr);?>>
					<?php if (!empty($form['description'])){ ?><p><?=$form['description'];?></p><?php } ?>

					<?=implode(PHP_EOL, $fields);?>

					<?=$system_fields;?>
					<button type="submit" class="btn btn-primary"><?=$form['submit_button'];?></button> 
				</form>
			</div>
		</div>
	</div>
</div>