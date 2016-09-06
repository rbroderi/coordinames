<?php
include "includes/coordinames.php";
setFactor(16);
$aliasfile = 'alias.txt';
//createAliasList($aliasfile);
//var_dump (checkAlias("edoras"));

for ($i = 0; $i <= 100; $i++) {
  //  for ($z = 0; $z <= 100; $z++) {
    $x=mt_rand(1600,16000);
	$y=mt_rand(1600,16000);
    echo "encodeval=".encode($x,$y);
        echo "<br/>";
     echo "decode x y =".var_dump(decode(encode($x,$y)));
    echo "<br/>";
//for ($y = 0; $y <= 500; $y+=16) {	
//setQuadrantNumeric($x,$y);
$coordAVN =lookupFromXY($x,$y);
$adj=$coordAVN[1];
$verb=$coordAVN[2];
$noun=$coordAVN[3];
echo "$x,$y";
echo "<br/>";
echo "$coordAVN[0],$adj,$verb,$noun";
echo "<br/>";
$coordAVN= lookupFromAVN($coordAVN[0],$adj,$verb,$noun);
echo "$coordAVN[0],$coordAVN[1]";
echo "<br/>";
echo "<hr/>";
}
//}

?>