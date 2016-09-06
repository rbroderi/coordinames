<?php
include "../includes/coordinames.php";
$xcoord= isset($_GET["x"])?htmlspecialchars($_GET["x"]):null;
$ycoord= isset($_GET["z"])?htmlspecialchars($_GET["z"]):null;
$aliasstring= isset($_GET["alias"])?htmlspecialchars($_GET["alias"]):null;
if($aliasstring !=null){
	setFactor(16);
$aliasfile = '../alias.txt';
createAliasList($aliasfile);
$temparray = checkAlias($aliasstring);
if($temparray==="no"){
	goto fail;
}
$xcoord = $temparray[0];
$ycoord = $temparray[1]; 	
}
if($xcoord !=null && $ycoord !=null)
{
	header ("Location: http://map.bitquest.co/#/".$xcoord."/64/".$ycoord."/max/0/0");
	exit(0);
}
	
else
{
	goto fail;
}

fail:
echo "<h1> Invalid map redirect please check coordinates </h1>";
?>