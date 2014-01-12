<div class="page-header">
<h1>Benutzerverwaltung</h1>
</div>
<?php echo anchor('user/add/','<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Neuer Benutzer anlegen</button>',array('class'=>'add')); ?><br />
<br />
<?php echo $table; ?>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"></div>