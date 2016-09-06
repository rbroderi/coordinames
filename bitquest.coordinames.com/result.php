<?php
$adj= htmlspecialchars($_GET["adj"]);
$verb=htmlspecialchars($_GET["verb"]);
$noun=htmlspecialchars($_GET["noun"]);
$xcoord=htmlspecialchars($_GET["xcoord"]);
$ycoord=htmlspecialchars($_GET["ycoord"]);

include "../bce.php";
$time_start = microtime(true);

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
$search = $test_noun;
$lines = file('http://localhost/coordinames/animals_px_py.txt',FILE_IGNORE_NEW_LINES);
// Store true when the text is found
$found = false;
foreach($lines as $line)
{
  if(strpos($line, $search) !== false)
  {
    $found = true;
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
setQuadrantName($noun);
echo decode(fromAVN($str))[0].",".decode(fromAVN($str))[1];
}
else{
setQuadrantNumeric($xcoord,$ycoord);
echo toAVN(encode($xcoord,$ycoord))[0].",". toAVN(encode($xcoord,$ycoord))[1].",". toAVN(encode($xcoord,$ycoord))[2];
}
echo "<hr/>";

/*
$time_end = microtime(true);
$time = $time_end - $time_start;

echo "Script Runtime = $time seconds\n";
*/

?>