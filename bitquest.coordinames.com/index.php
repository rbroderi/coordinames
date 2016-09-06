<?php
header('Content-Type: text/html; charset=utf-8');
include "includes/coordinames.php";
$adj= isset($_GET["adj"])?htmlspecialchars($_GET["adj"]):null;
$verb= isset($_GET["verb"])?htmlspecialchars($_GET["verb"]):null;
$noun= isset($_GET["noun"])?htmlspecialchars($_GET["noun"]):null;
$xcoord= isset($_GET["xcoord"])?htmlspecialchars($_GET["xcoord"]):null;
$ycoord= isset($_GET["ycoord"])?htmlspecialchars($_GET["ycoord"]):null;
$scalenumber =isset($_GET["qty"])?htmlspecialchars($_GET["qty"]):null;

const BLOCKSPERCHUNK=16.0;
setFactor(BLOCKSPERCHUNK);
if((isset($adj) && isset($verb) && isset($noun)) && ($adj!=="" && $verb!=="" && $noun!=="")){
$coordNumeric =lookupFromAVN($scalenumber,$adj,$verb,$noun);
}
else if((isset($xcoord) && isset($ycoord)) && ($xcoord!=="" && $ycoord!=="" ))
{
$coordAVN =lookupFromXY($xcoord,$ycoord);
$scalenumber=$coordAVN[0];
$adj=$coordAVN[1];
$verb=$coordAVN[2];
$noun=$coordAVN[3];
}
?>
<!DOCTYPE html>
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Coordinames:Minecraft</title>
<style>

</style>
<link href="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet"/>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>

</head>
<body>
	<div id="wrapper">
		<div id="header">
<a href="index.php">
		<img id='logo' src='logo.png' alt="cordinames logo" height="166" width ="585"/>
</a>
		<span id='subtitle'>Minecraft</span>
			<div id='row'></div>
			<div id='row2'></div>
			<div id='form'><form action="">
 <label>
  Coordiname: 
<input type="text" name="qty" size="4" placeholder="<?php $v=isset($coordAVN)? $coordAVN[0]:"Quantity";echo $v?>" value="<?php $v=isset($coordAVN)? $coordAVN[0]:"";echo $v?>" />
  <input type="text" name="adj" size="11" placeholder="<?php $v=isset($coordAVN)? $coordAVN[1]:"Adjective";echo $v?>" value="<?php $v=isset($coordAVN)? $coordAVN[1]:"";echo $v?>"/>
<input type="text" name="verb" size="11" placeholder="<?php $v=isset($coordAVN)? $coordAVN[2]:"Verb";echo $v?>" value="<?php $v=isset($coordAVN)? $coordAVN[2]:"";echo $v?>"/>
<input type="text" name="noun" size="12" placeholder="<?php $v=isset($coordAVN)? $coordAVN[3]:"Noun";echo $v?>" value="<?php $v=isset($coordAVN)? $coordAVN[3]:"";echo $v?>"/>
</label>
<label>
Numeric Coordinate: 
<input type="text" name="xcoord" size="12" placeholder="<?php $v=isset($coordNumeric)? $coordNumeric[0]:"X Coordinate";echo $v?>" value="<?php $v=isset($coordNumeric)? $coordNumeric[0]:"";echo $v?>"/>
<input type="text" name="ycoord" size="12" placeholder="<?php $v=isset($coordNumeric)? $coordNumeric[1]:"Z Coordinate";echo $v?>" value="<?php $v=isset($coordNumeric)? $coordNumeric[1]:"";echo $v?>"/>
</label>
<label>
  <input class='submit' type="submit" value="Lookup">
</label>
</form></div>
<div id='link'>
<?php
$mx=isset($coordNumeric)?$coordNumeric[0]:$xcoord;
$my=isset($coordNumeric)?$coordNumeric[1]:$ycoord;

?>
<a style="<?php $v= (isset($mx) && isset($my)) ? "":"visibility: hidden;"; echo $v?>" href='map/<?php echo $mx."/".$my?>'>Bitquest User Map Link</a>
<button class="btn" id="btn" data-clipboard-demo data-clipboard-text="http://bitquest.coordinames.com/map/<?php echo $mx."/".$my?>"> Copy link to clipboard </button>
</div>
<div id='menu'>
    <ul class="menu-list">
        <li class="menu-item"><a href="#about" class="menu-link">About</a></li>
        <li class="menu-item"><a href="#locations" class="menu-link">Notable Locations</a></li>
        <li class="menu-item"><a href="#faq" class="menu-link">FAQ</a></li>
        <li class="menu-item"><a href="../" class="menu-link">Coordinames Main Page &#8617;</a></li>
    </ul>
</div>
		</div>
<a name="about"></a> 
		<div id="content">
<div id="contentpad">
<h1>About</h1>
<p>
<?php echo file_get_contents("http://coordinames.com/about.html");?>
</p>
<hr/>
<h1 id='faq'>Notable Locations</h1>
<?php include 'locations.php'?>
<hr/>
<h1 id='faq'>FAQ</h1>
<?php include 'faq.html'?>
<hr/>
</div>
</div>
		<div id="footer">This website is not affiliated with, maintained, authorized, endorsed or sponsored by the Mojang, Minecraft or any of its affiliates. <small><a title="Minecraft Grass" href="https://flickr.com/photos/filterforge/10134745566">Flickr photo</a> shared by <a href="https://flickr.com/people/filterforge">Filter Forge</a> under a <a href="https://creativecommons.org/licenses/by/2.0/">Creative Commons ( BY ) license</a> </small></div>
	</div>
<script>
var btn = document.getElementById('btn');
    var clipboard = new Clipboard(btn);

// Tooltip

$('button').tooltip({
  trigger: 'click',
  placement: 'bottom'
});

function setTooltip(btn, message) {
  $(btn).tooltip('hide')
    .attr('data-original-title', message)
    .tooltip('show');
}

function hideTooltip(btn) {
  setTimeout(function() {
    $(btn).tooltip('hide');
  }, 1000);
}

// Clipboard

var clipboard = new Clipboard('button');

clipboard.on('success', function(e) {
  setTooltip(e.trigger, 'Copied!');
  hideTooltip(e.trigger);
});

clipboard.on('error', function(e) {
  setTooltip(e.trigger, 'Failed!');
  hideTooltip(e.trigger);
});
    </script>
<script src="tooltips.js"></script>
</body>
</html>