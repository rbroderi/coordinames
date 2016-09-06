<?php
$adj= isset($_GET["adj"])?htmlspecialchars($_GET["adj"]):null;
$verb= isset($_GET["verb"])?htmlspecialchars($_GET["verb"]):null;
$noun= isset($_GET["noun"])?htmlspecialchars($_GET["noun"]):null;
$xcoord= isset($_GET["xcoord"])?htmlspecialchars($_GET["xcoord"]):null;
$ycoord= isset($_GET["ycoord"])?htmlspecialchars($_GET["ycoord"]):null;
include "../bce.php";
//$time_start = microtime(true);

$a = file('http://localhost/coordinames/adj.txt',FILE_IGNORE_NEW_LINES);
$b = file('http://localhost/coordinames/gerund.txt',FILE_IGNORE_NEW_LINES);
$c;
$totalcomb;
const BLOCKSPERCHUNK=16.0;
$RADIUS;
//large num compliant
function setQuadrantNumeric($x,$y){
global $c,$totalcomb,$RADIUS,$a,$b;
	if(bccomp($x,0)!=-1){ //$x>=0
		if(bccomp($y,0)!=-1){ //$y>=0
		$c=file('http://localhost/coordinames/animals_px_py.txt',FILE_IGNORE_NEW_LINES);
		}
		else{
		$c=file('http://localhost/coordinames/natural_px_ny.txt',FILE_IGNORE_NEW_LINES);
		}
	}
	else{
		if($y>=0){
		$c=file('http://localhost/coordinames/plants_nx_py.txt',FILE_IGNORE_NEW_LINES);
		}
		else{
		$c=file('http://localhost/coordinames/noun_nx_ny.txt',FILE_IGNORE_NEW_LINES);
		}
	}
$totalcomb=bcmul(bcmul(count($a),count($b)),count($c)); //count($a)*count($b)*count($c)
$RADIUS=bcfloor(bcsqrt($totalcomb)); //floor(sqrt($totalcomb))
}
function setQuadrantName($test_noun){
global $c,$totalcomb,$RADIUS,$a,$b;
$q=0;
$search = $test_noun;
$lines = file('http://localhost/coordinames/animals_px_py.txt',FILE_IGNORE_NEW_LINES);
// Store true when the text is found
$found = false;
foreach($lines as $line)
{
  if(strpos($line, $search) !== false)
  {
    $found = true;
	$q=1;
      goto end;
  }
}

$lines = file('http://localhost/coordinames/natural_px_ny.txt',FILE_IGNORE_NEW_LINES);
// Store true when the text is found
$found = false;
foreach($lines as $line)
{
  if(strpos($line, $search) !== false)
  {
    $found = true;
	$q=4;
      goto end;
  }
}

$lines = file('http://localhost/coordinames/plants_nx_py.txt',FILE_IGNORE_NEW_LINES);
// Store true when the text is found
$found = false;
foreach($lines as $line)
{
  if(strpos($line, $search) !== false)
  {
    $found = true;
	$q=2;
      goto end;
  }
}

$lines = file('http://localhost/coordinames/noun_nx_ny.txt',FILE_IGNORE_NEW_LINES);
// Store true when the text is found
$found = false;
foreach($lines as $line)
{
  if(strpos($line, $search) !== false)
  {
    $found = true;
	$q=3;
      goto end;
  }
}

// If the text was not found, show a message
if(!$found)
{
  echo 'No match found';
	return;
}
end:
$c=$lines;
$totalcomb=bcmul(bcmul(count($a),count($b)),count($c)); //count($a)*count($b)*count($c)
$RADIUS=bcfloor(bcsqrt($totalcomb)); //floor(sqrt($totalcomb))
return $q;
}

//large num compliant
function getChunk($x,$y){
    return array (bcdiv($x,BLOCKSPERCHUNK,0),bcdiv($y,BLOCKSPERCHUNK,0)); // ((int)($x/BLOCKSPERCHUNK),(int)($y/BLOCKSPERCHUNK)); 
}
//large num compliant
function encode($x,$y){
	global $RADIUS;
    $chunkxy=getChunk($x,$y);
    return bcadd(bcmul($RADIUS,$chunkxy[1]),$chunkxy[0]); //$RADIUS*$chunkxy[1]+$chunkxy[0];
}
//large num compliant
function decode($encodeval){
	global $RADIUS;
	//$encodeval = (int)$encodeval;
    return array (bcmod($encodeval,$RADIUS),bcdiv($encodeval,$RADIUS),0); //($encodeval % $RADIUS,(int)($encodeval/$RADIUS))
}
//large num compliant
function toAVN($encodeval){
    global $totalcomb,$a,$b,$c;
	$inrange=bcsub($encodeval,$totalcomb,0);
	$encodeval=bcmod($encodeval,$totalcomb);
    $firstpart = $totalcomb/count($a);
    $secondpart = $firstpart/count($b);
	return array($a[bcdiv($encodeval,$firstpart)],
				$b[bcmod(bcdiv($encodeval,$secondpart),count($b))],
				$c[bcmod($encodeval,count($c))],
				$inrange); //($a[$encodeval/$firstpart],$b[($encodeval / $secondpart) % count($b)],$c[$encodeval % count($c)])
}
function fromAVNNumeric($AVNstr){
   global $totalcomb,$a,$b,$c;
	return array (array_search($AVNstr[0],$a),array_search($AVNstr[1],$b),array_search($AVNstr[2],$c));
}
//large num compliant
function fromAVN($AVNstr){
    global $totalcomb,$a,$b,$c;
  $firstpart = $totalcomb/count($a);
    $secondpart = $firstpart/count($b);
	$numeric = fromAVNNumeric($AVNstr);
	return bcadd(bcadd(bcmul($firstpart,$numeric[0]),bcmul($secondpart,$numeric[1])),$numeric[2]); //$firstpart*$numeric[0]+$secondpart*$numeric[1]+$numeric[2]
}
//echo getChunk(16,1)[0]; CHUNK 1 , 0

/*$tx=$xcoord;
$ty=$ycoord;
setQuadrant($tx,$ty);
$tx=bcabs($tx);
$ty=bcabs($ty);
echo "total combinations possible =".$totalcomb;
echo "<br/>";
echo "x=".$tx.","."y=$ty";
echo "<br/>";
echo "in chunk at:".getChunk($tx,$ty)[0].",".getChunk($tx,$ty)[1];
echo "<br/>";
echo "combo number:".encode($tx,$ty);
echo "<br/>";
echo decode(encode($tx,$ty))[0].",".decode(encode($tx,$ty))[1];
echo "<br/>";
echo toAVN(encode($tx,$ty))[0].",". toAVN(encode($tx,$ty))[1].",". toAVN(encode($tx,$ty))[2];
if(toAVN(encode($tx,$ty))[3]>0){
echo ";".toAVN(encode($tx,$ty)/$totalcomb)[0].",". toAVN(encode($tx,$ty)/$totalcomb)[1].",". toAVN(encode($tx,$ty)/$totalcomb)[2];
}
echo "<br/>";
echo "combo number:".fromAVN(toAVN(encode($tx,$ty)));
echo "<br/>";
echo decode(fromAVN(toAVN(encode($tx,$ty))))[0].",".decode(fromAVN(toAVN(encode($tx,$ty))))[1];
*/
 if(isset($noun) && $noun!=""){
$str=array($adj,$verb,$noun);
$q = setQuadrantName($noun);
$coordNumeric= decode(fromAVN($str));
$coordNumeric[0]*=BLOCKSPERCHUNK;
$coordNumeric[1]*=BLOCKSPERCHUNK;
if($q==2){
$coordNumeric[0]*=-1;
}
else if($q==4){
$coordNumeric[1]*=-1;
}
else if($q==3){
$coordNumeric[0]*=-1;
$coordNumeric[1]*=-1;
}
}
else if(isset($xcoord) && $xcoord!=""){
setQuadrantNumeric($xcoord,$ycoord);
$xcoordabs=bcabs($xcoord);
$ycoordabs=bcabs($ycoord);
$coordAVN= toAVN(encode($xcoordabs,$ycoordabs));
}

/*
$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Script Runtime = $time seconds\n";
*/

?>
<!DOCTYPE html>
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Coordinames:Minecraft</title>
<style>
/**
 * CSSReset.com - How To Keep Footer At Bottom of Page with CSS
 * 
 * Original Tutorial: http://www.cssreset.com/2010/css-tutorials/how-to-keep-footer-at-bottom-of-page-with-css/
 * License: Free - do whatever you like with it! Credit and linkbacks much appreciated.
 *
 * NB: Make sure the value for 'padding-bottom' on #content is equal to or greater than the height of #footer.
 */
html,
body {
	margin:0;
	padding:0;
	height:100%;
min-width: 600px;
}
#wrapper {
	min-height:100%;
	position:relative;
}
#header {
  text-align: center;
	background:#ededed;
	padding:10px;
left:-10px;
position: fixed;
width:102%;
  background-image: url("grass_top.png");
    background-repeat: repeat;
 background-size: 128px 128px;
  image-rendering:optimizeSpeed;             /* Legal fallback */
  image-rendering:-moz-crisp-edges;          /* Firefox        */
  image-rendering:-o-crisp-edges;            /* Opera          */
  image-rendering:-webkit-optimize-contrast; /* Safari         */
  image-rendering:optimize-contrast;         /* CSS3 Proposed  */
  image-rendering:crisp-edges;               /* CSS4 Proposed  */
  image-rendering:pixelated;                 /* CSS4 Proposed  */
  -ms-interpolation-mode:nearest-neighbor;   /* IE8+           */
}
#content {
padding-top:400px;
	padding-bottom:100px; /* Height of the footer element */
background-color:#BBA

}
#menu{
height:25px;
width:102%;
background-color:#efefef;
margin-bottom:-10px;
margin-top:-62px;
padding-top:.5em;
}
#footer {
	width:100%;
	height:1ex;
	position:absolute;
	bottom:0;
	left:0;
  padding: 1rem;
  background-color: #efefef;
  text-align: center;
font-size:75%;
}
#row{
left:-10px;
  position: relative;
top:100px;
height:128px;
width:102%;
    background-image: url("grass_block.png");
    background-repeat: repeat-x;
}
#row2{
  position: relative;
left:-10px;
top:100px;
height:128px;
width:102%;
    background-image: url("dirt.png");
    background-repeat: repeat;
  background-position: 60px 128px; 
}
body {
  font-family: "Helvetica Neue", Arial, sans-serif;
    overflow-x: hidden;
}
ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}
li {
    display: inline;
padding-left:1em;
}
#logo{
  position: relative;
top:-40px;
left:-150px;
margin-bottom:-150px;
    margin-left: 250px;
    margin-right: auto;

}
#subtitle{
  position: absolute;
left:40%;
top:80px;
	font-size:400%;
   font-weight: bold;
 z-index: 1;
}
#contentpad{
padding-top:4em;
padding-left:5em;
padding-right:5em;

}
#link{
 position: relative;
 margin: auto;
    width: 45%;
font-size:150%;
top:-70px;
  border-radius: 25px;
background: rgba(255, 255, 255, 0.75);
left:-10px;
min-width: 600px;
}
#form{
 position: relative;
 margin: auto;
    width: 45%;
top:-120px;
height:128px;
z-index:1;
left:-10px;
  min-width: 600px;
}
form {
  display: table;
 border-spacing: 10px;
    border-radius: 25px;
background: rgba(255, 255, 255, 0.75)
}
label {
  display: table-row;
font-size:140%;
   font-weight: bold;
}
input {
  display: table-cell;
}
.submit{
font-size:100%;
   font-weight: bold;
}
::-webkit-input-placeholder { /* Chrome */
  color: #555;
}
:-ms-input-placeholder { /* IE 10+ */
  color: #555;
}
::-moz-placeholder { /* Firefox 19+ */
  color:#555;
  opacity: 1;
}
:-moz-placeholder { /* Firefox 4 - 18 */
  color:#555;
  opacity: 1;
}
h2{
font-size:200% !important;
}
</style>
<script src="https://cdn.jsdelivr.net/clipboard.js/1.5.12/clipboard.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
<link href="https://netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>
	<div id="wrapper">
		<div id="header">
<a href="index.php">
		<img id='logo' src='logo.png' alt="cordinames logo"/>
</a>
		<span id='subtitle'>Minecraft</span>
			<div id='row'></div>
			<div id='row2'></div>
			<div id='form'><form action="">
 <label>
  Coordiname: 
<input type="text" name="qty" size="4" placeholder="<?php $v=isset($coordAVN)? $coordAVN[0]:"Quantity";echo $v?>" />
  <input type="text" name="adj" size="11" placeholder="<?php $v=isset($coordAVN)? $coordAVN[1]:"Adjective";echo $v?>" />
<input type="text" name="verb" size="11" placeholder="<?php $v=isset($coordAVN)? $coordAVN[2]:"Verb";echo $v?>" />
<input type="text" name="noun" size="12" placeholder="<?php $v=isset($coordAVN)? $coordAVN[3]:"Noun";echo $v?>"/>
</label>
<label>
Numeric Coordinate: 
<input type="text" name="xcoord" size="12" placeholder="<?php $v=isset($coordNumeric)? $coordNumeric[0]:"X Coordinate";echo $v?>" />
<input type="text" name="ycoord" size="12" placeholder="<?php $v=isset($coordNumeric)? $coordNumeric[1]:"Z Coordinate";echo $v?>" />
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
<a style="<?php $v= (isset($mx) && isset($my)) ? "":"visibility: hidden;"; echo $v?>" href='map.php?x=<?php echo $mx."&y=".$my?>'>Bitquest User Map Link</a>
<button class="btn" id="btn" data-clipboard-demo data-clipboard-text="map.php?x=<?php echo $mx."&y=".$my?>"> Copy link to clipboard </button>
</div>
<div id='menu'>
    <ul class="menu-list">
        <li class="menu-item"><a href="#about" class="menu-link">About</a></li>
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
<?php include '../about.html'?>
</p>
<hr/>
<h1 id='faq'>FAQ</h1>
<?php include 'faq.html'?>
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