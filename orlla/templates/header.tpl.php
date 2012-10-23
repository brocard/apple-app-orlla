<!DOCTYPE html>
<html> 
<head>
<meta charset="utf-8"> 
<title><?=$this->title ? $this->title . " - " : ''?>tomtalk</title>
<meta name="viewport" content="width=device-width; initial-scale=1.0">

<?php foreach ($this->styles as $tmp_style):?>
<link rel="stylesheet" type="text/css" href="<?=$STATIC_PATH . 'styles/' . $tmp_style?>?v<?=$VERSION?>" />
<?php endforeach;?>

<?php if (!empty($this->extra)): ?>
<?php foreach ($this->extra as $line):?>
<?=$line?>
<?php endforeach;?>
<?php endif; ?>

<link rel="icon" type="image/ico" href="/favicon.ico" />
<link rel="shortcut icon" type="image/ico" href="/favicon.ico" />

<!-- Le styles -->
    <style type="text/css">
    body {
        padding-top: 60px;
        padding-bottom: 40px; 
    } 
    
    img {
        max-width:100%;
        height:auto;
    }
    </style>
    <link href="<?=$STATIC_PATH . 'styles/'?>bootstrap.css?v<?=$VERSION?>" rel="stylesheet">
    <link href="<?=$STATIC_PATH . 'styles/'?>bootstrap-responsive.css?v<?=$VERSION?>" rel="stylesheet">

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon-precomposed" href="<?=$STATIC_PATH.'img/'?>apple-touch-icon-144-precomposed.png" sizes="144x144" >
    <link rel="apple-touch-icon-precomposed" href="<?=$STATIC_PATH.'img/'?>apple-touch-icon-114-precomposed.png" sizes="114x114" >
    <link rel="apple-touch-icon-precomposed" href="<?=$STATIC_PATH.'img/'?>apple-touch-icon-72-precomposed.png" sizes="72x72"   >
    <link rel="apple-touch-icon-precomposed" href="<?=$STATIC_PATH.'img/'?>apple-touch-icon-57-precomposed.png" >

</head>
<body>
