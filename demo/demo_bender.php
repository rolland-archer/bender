<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bender Demo</title>
<?php
require_once "../Bender/Bender.class.php";
$bender = new Bender(array(
    'root_dir'=>$_SERVER['DOCUMENT_ROOT'],
    'jsmin'=>'jshrink'
));
$bender->enqueue(array(
    'demo/assets/styles/ui/theme/jquery.ui.all.css',
    'demo/assets/styles/jquery.bxslider.css',
    'demo/assets/styles/jquery.fancybox.css',
    'demo/assets/styles/jquery.jscrollpane.css',
    'demo/assets/styles/jquery.jcrop.css',
    'demo/assets/styles/jquery.fancybox.css',
    'demo/assets/styles/jquery.rating.css',
    'demo/assets/styles/jCleverTemplate/default/css/jClever.css',
    'demo/assets/styles/iphone-style-checkboxes.css',
    'demo/assets/styles/knobKnob.css'
));
echo $bender->output_css('demo/cache','style');
?>
</head>

<body>

<div class="container">
    <div class="row">
        <div class="jumbotron">
            <div class="container">
                <h1>Hello, world!</h1>
                <p>This is a Bender demo</p>
                <p><a class="btn btn-primary btn-lg" href="http://www.esiteq.com/projects/bender/">Learn more &raquo;</a></p>
            </div>
        </div>
    </div>
</div>
<?
$bender->enqueue(array(
    'demo/assets/scripts/jquery-1.10.1.min.js',
    'demo/assets/scripts/jquery.mousewheel.js',
    'demo/assets/scripts/jquery-ui.custom.min.js',
    'demo/assets/scripts/jquery.datepicker.lang.js',
    'demo/assets/scripts/jquery.bxslider.js',
    
    'demo/assets/scripts/fileuploader.js',
    
));
echo $bender->output_js('demo/cache','script-1');
?>
<!--[if lt IE 9]>
    <script src="/demo/assets/scripts/slider-knob/html5.js"></script>
<![endif]-->

<?
$bender->enqueue(array(
    
    'demo/assets/scripts/fileuploader.js',
    'demo/assets/scripts/masonry.js',
    'demo/assets/scripts/jquery.jscrollpane.min.js',
    'demo/assets/scripts/history.min.js',
    'demo/assets/scripts/slider-knob/transform.js',
    'demo/assets/scripts/slider-knob/knobKnob.jquery.js',   
    'demo/assets/scripts/iphone-style-checkboxes.js',
    'demo/assets/scripts/jClever.min.js',
    'demo/assets/scripts/jquery.fancybox.pack.js',
    
    'demo/assets/scripts/preloadThis.js',
    
    'demo/assets/scripts/jquery.printElement.js',
    'demo/assets/scripts/jquery.rating.pack.js',
    'demo/assets/scripts/jquery.jcrop.min.js',
    
    'demo/assets/scripts/jquery.maskedinput.js',
    'demo/assets/scripts/shareSome.js',
    'demo/assets/scripts/jquery.confirm.js',
));
echo $bender->output_js('demo/cache','script-2');
?>
</body>
</html>