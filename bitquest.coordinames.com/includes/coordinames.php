<?php
//version=1.0
include "bce.php";
$a = file('http://coordinames.com/wordlists/adj_shuf.txt',FILE_IGNORE_NEW_LINES);
$b = file('http://coordinames.com/wordlists/gerund_shuf.txt',FILE_IGNORE_NEW_LINES);
$c;
$totalcomb;
$factor;
$RADIUS;
$alias;

function setFactor($f=16){
	global $factor;
	$factor=$f;
}
//large num compliant
function setQuadrantNumeric($x,$y){
global $c,$totalcomb,$RADIUS,$a,$b;
	if(bccomp($x,0)!=-1){ //$x>=0
		if(bccomp($y,0)!=-1){ //$y>=0
		$c=file('http://coordinames.com/wordlists/animals_px_py_shuf.txt',FILE_IGNORE_NEW_LINES);
		}
		else{
		$c=file('http://coordinames.com/wordlists/natural_px_ny_shuf.txt',FILE_IGNORE_NEW_LINES);
		}
	}
	else{
		if($y>=0){
		$c=file('http://coordinames.com/wordlists/plants_nx_py_shuf.txt',FILE_IGNORE_NEW_LINES);
		}
		else{
		$c=file('http://coordinames.com/wordlists/noun_nx_ny_shuf.txt',FILE_IGNORE_NEW_LINES);
		}
	}
$totalcomb=bcmul(bcmul(count($a),count($b)),count($c)); //count($a)*count($b)*count($c)
$RADIUS=bcfloor(bcsqrt($totalcomb)); //floor(sqrt($totalcomb))
}

function setQuadrantName($test_noun){  //todo fix for not found
global $c,$totalcomb,$RADIUS,$a,$b;
$test_noun=strtolower($test_noun);
$q=0;
$search = $test_noun;
$lines = file('http://coordinames.com/wordlists/animals_px_py_shuf.txt',FILE_IGNORE_NEW_LINES);
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

$lines = file('http://coordinames.com/wordlists/natural_px_ny_shuf.txt',FILE_IGNORE_NEW_LINES);
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

$lines = file('http://coordinames.com/wordlists/plants_nx_py_shuf.txt',FILE_IGNORE_NEW_LINES);
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

$lines = file('http://coordinames.com/wordlists/noun_nx_ny_shuf.txt',FILE_IGNORE_NEW_LINES);
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
      throw new Exception('Noun not found unable to set Quadrant');
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
	global $factor;
    return array (bcdiv($x,$factor,0),bcdiv($y,$factor,0)); // ((int)($x/$factor),(int)($y/$factor)); 
}
//large num compliant
function encode($x,$y){
/*	global $totalcomb;
	global $RADIUS;
    $chunkxy=getChunk($x,$y);
    $actualcombonumber = bcadd(bcmul($RADIUS,$chunkxy[1]),$chunkxy[0]); //$RADIUS*$chunkxy[1]+$chunkxy[0];
	return scramble($actualcombonumber);*/
      $chunkxy=getChunk($x,$y);
    return scramble(ulamEncode($chunkxy[0],$chunkxy[1]));
}
//large num compliant
function decode($encodeval){
	/*global $RADIUS;
	//$encodeval = (int)$encodeval;
    $actualcombonumber = descramble($encodeval);
	return array (bcmod($actualcombonumber,$RADIUS),bcdiv($actualcombonumber,$RADIUS),0); //($encodeval % $RADIUS,(int)($encodeval/$RADIUS))
*/
$out= ulamDecode(descramble($encodeval));
return $out;
}

function ulamDecode($i){
    /*
     $m = floor((sqrt($i)+1)/2);
    $k = $i - 4*$m*($m-1);
    $x;
    $y;
    if ($k <= 2*$m){
        $x = $m;
        $y = $k - $m;
    } else if ($k <= 4*$m){
        $x = 3*$m - $k;
        $y = $m;
    } else if ($k <= 6*$m){
        $x = -$m;
        $y = 5*$m - $k;
    } else {
        $x = $k - 7*$m;
        $y = -$m;
    }
    return array($x,$y);*/
         $m = bcfloor(bcdiv((bcadd(bcsqrt($i),1)),2));
    $k = bcsub($i,bcmul(4,bcmul($m,(bcsub($m,1)))));
    $x;
    $y;
    if (bccomp($k,bcmul(2,$m)) !=1){
        $x = $m;
        $y = bcsub($k,$m);
    } else if (bccomp($k,bcmul(4,$m)) !=1){
        $x = bcsub(bcmul(3,$m),$k);
        $y = $m;
    } else if (bccomp($k,bcmul(6,$m)) !=1){
        $x = bcmul(-1,$m);
        $y = bcsub(bcmul(5,$m),$k);
    } else {
        $x = bcsub($k,bcmul(7,$m));
        $y = bcmul(-1,$m);
    }
    return array($x,$y);
}
function ulamEncode($x, $y){
/*   $x=(int)$x;
    $y=(int)$y;
    $m = max(abs($x),abs($y));
    if ($x === $m && $y !== -$m) return 4*$m*($m-1) + $m + $y;
    else if ($y === $m) return 4*$m*($m-1) + 3*$m - $x;
    else if ($x === -$m) return 4*$m*($m-1) + 5*$m - $y;
    else return 4*$m*($m-1) + 7*$m + $x;
  */
  $m = bcmax(bcabs($x),bcabs($y));
    if (bccomp($x,$m)==0 && bccomp($y,bcmul(-1,$m))!=0){
      return bcadd(bcmul(bcmul(4,$m),(bcsub($m,1))),bcadd($m,$y));  
    } 
    else if (bccomp($y,$m)==0){
      return bcadd(bcmul(bcmul(4,$m),bcsub($m,1)),bcsub(bcmul(3,$m),$x));  
    } 
    else if (bccomp($x,bcmul(-1,$m))==0){
      return bcadd(bcmul(bcmul(4,$m),(bcsub($m,1))),bcsub(bcmul(5,$m),$y));  
    } 
    else {
        return bcadd(bcmul(bcmul(4,$m),(bcsub($m,1))),bcadd(bcmul(7,$m),$x));
}
}


/* old
function scramble($x){
	global $totalcomb;
	$a=1; //chosen to keep values low but positive
	//grows to quickly
	return $x;
	//return ((($a+$x)*(($a+$x)+1)/2) + $a);
	}
function descramble($x){
	$a=1;
	return $x;
	//return 1/2*(sqrt(-8*$a+8*$x+1)-2*$a-1);
	
}*/
function getLastdigit($x){
    $x=(string)$x;
    return $x[strlen($x)-1];
}
function scramble($z){
    if($z==0)
        return $z;
    $last = getLastDigit($z);
    $last = lowValScramble($last);
    $z=(string)$z;
    if(!$last==0){
        $z = substr($z,0,strlen($z)-1);
        $z=bcmul($z,$last);
        $z=$z.$last;
    }
    return ltrim($z, '0'); // REMOVE LEADING ZEROES
}
function descramble($z){
    if($z==0)
        return $z;
    $last = getLastDigit($z);
    $z=(string)$z;
    if(!$last==0){
        $z = substr($z, 0,strlen($z)-1);
        $z=bcdiv($z,$last);
        $last = lowValDescramble($last);
        $z=$z.$last;
    }
    return ltrim($z, '0'); // REMOVE LEADING ZEROES
}
function lowValScramble($x){  // for values less than 10
    if($x<10){
        $scramArray=array(0,8,4,1,6,2,7,9,5,3);
        return $scramArray[$x];
    }
    else{
        echo "error value to large for function";
    }
}
function lowValDescramble($x){
    if($x<10){
        $scramArray=array(0,8,4,1,6,2,7,9,5,3);
        return array_search($x,$scramArray);
    }
    else{
        echo "error value to large for function";
    }
}
//large num compliant
function toAVN($encodeval){
    global $totalcomb,$a,$b,$c;
	$inrange=bcsub($encodeval,$totalcomb,0)<=0?1:-1;
	$encodeval=bcmod($encodeval,$totalcomb);
    $firstpart = bcdiv($totalcomb,count($a));
    $secondpart = bcdiv($firstpart,count($b));
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
  $firstpart = bcdiv($totalcomb,count($a));
    $secondpart = bcdiv($firstpart,count($b));
	$numeric = fromAVNNumeric($AVNstr);
	return bcadd(bcadd(bcmul($firstpart,$numeric[0]),bcmul($secondpart,$numeric[1])),$numeric[2]); //$firstpart*$numeric[0]+$secondpart*$numeric[1]+$numeric[2]
}

function checkAlias($name){
	global $alias;
		return array_key_exists($name,$alias)?$alias[$name]:"no";
	}
	
function setAliasList($list){
	global $alias;
	$alias=$list;
}

function createAliasList($file){
	global $alias;
	foreach(file($file,FILE_IGNORE_NEW_LINES) as $line){
		$parts = explode(",",$line);
		$alias[$parts[0]]= array($parts[1],$parts[2]);
	}
}

function lookupFromXY($x,$y){
	global $totalcomb;
	setQuadrantNumeric($x,$y);
$xabs=bcabs($x);
$yabs=bcabs($y);
$encodeval=encode($xabs,$yabs); //problem here

$count=bcdiv($encodeval,$totalcomb,0);
$encodeval = bcmod($encodeval,$totalcomb);
$encodeval = bcadd(bcmul($count,$totalcomb),bcmod($encodeval,$totalcomb));
$coordAVN= toAVN($encodeval);

array_unshift($coordAVN,$count);
	return $coordAVN;
}

function lookupFromAVN($scalenumber,$adj,$verb,$noun){
	global $totalcomb,$factor;
	$str=array(strtolower($adj),strtolower($verb),strtolower($noun));
$q = setQuadrantName($noun);
if(!isset($scalenumber) || $scalenumber==""){
	$scalenumber=1;
}

$coordNumeric= decode(bcadd(bcmul($scalenumber,$totalcomb),fromAVN($str)));
$coordNumeric[0] = bcmul($coordNumeric[0],$factor);
$coordNumeric[1] = bcmul($coordNumeric[1],$factor);
if($q==2){
$coordNumeric[0] = bcmul($coordNumeric[0],-1);
}
else if($q==4){
$coordNumeric[1] = bcmul($coordNumeric[1],-1);
}
else if($q==3){
$coordNumeric[0] = bcmul($coordNumeric[0],-1);
$coordNumeric[1] = bcmul($coordNumeric[1],-1);
}
	return $coordNumeric;
}

?>