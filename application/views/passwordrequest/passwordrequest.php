<div class="page-header"><h1>Passwort vergessen</h1></div><p></p><?=$error_msg?></p><?php    $attributes = array('class' => 'form-horizontal', 'role' => 'form');    echo form_open('passwordrequest/checkRequest',$attributes);?>  <input type="hidden" name="newpass" value="1"/>  <div class="form-group">    <label for="username" class="col-sm-2 control-label">Benutzername</label>    <div class="col-sm-10">      <input type="text" class="form-control" id="username" name="username" placeholder="Benutzername" value="<?php echo set_value('username'); ?>">    </div>  </div>  <div class="form-group">    <label for="email" class="col-sm-2 control-label">Email</label>    <div class="col-sm-10">      <input type="email" class="form-control" id="email" name="email" placeholder="name@domain.ch" value="<?php echo set_value('email'); ?>">    </div>  </div>  <div class="form-group">    <div class="col-sm-offset-2 col-sm-10">      <button type="submit" class="btn btn-default">Passwort anfordern</button>    </div>  </div></form>
