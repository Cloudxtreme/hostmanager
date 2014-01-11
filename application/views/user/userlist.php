<div class="page-header">
<h1>Benutzerverwaltung</h1>
</div>
<?php echo anchor('user/add/','<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Neuer Benutzer anlegen</button>',array('class'=>'add')); ?><br />
<br />
<div class="paging"><?php echo $pagination; ?></div>
<div class="data"><?php echo $table; ?></div>
<br />
