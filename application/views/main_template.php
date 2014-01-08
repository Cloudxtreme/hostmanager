<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=$_SESSION ['client']['client_name'].' - '.$homeTitle?></title>
    <link rel="shortcut icon" href="<?=base_url()?>/img/favicon.ico" type="image/x-ico; charset=binary" />
    <link rel="icon" href="<?=base_url()?>/img/<?=$_SESSION ['client']['client_url']?>_favicon.ico" type="image/x-ico; charset=binary" />
    <link href="<?=base_url()?>css/normalize.css" media="all" rel="stylesheet"/>
    <link href="<?=base_url()?>css/style.css" media="all" rel="stylesheet"/>
    <link href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" media="all" rel="stylesheet"/>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

	<script type="text/javascript" src="<?=base_url()?>js/functions.js"></script>
    <meta http-equiv='expires' content='-1' />
    <meta http-equiv= 'pragma' content='no-cache' />
    <meta name='robots' content='all' />
  </head>
  <body>
    <div id="container">
      <div id="header"><a href="<?=base_url()?>"><h1><?=$headerTitle?></h1></a></div>
      <div id="navigation"><?=$navigation?></div>
      <div id="subnavigation"><?=$subnavigation?></div>
      <div id="main"><?=$mainContent?></div>
      <div id="footer"><?=$footer?></div>
    </div>
  </body>
</html>