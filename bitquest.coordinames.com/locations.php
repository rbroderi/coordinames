<ul>
<?php
$lines = file('alias.txt', FILE_IGNORE_NEW_LINES);
sort($lines);
$towns    = array();
$stations = array();
$others   = array();
foreach ($lines as $line) {
    $parts = explode(",", $line);
    if (strcmp($parts[3], "town") === 0) {
        array_push($towns, $line);
    } else if (strcmp($parts[3], "station") === 0) {
        array_push($stations, $line);
    } else {
        array_push($others, $line);
    }
}
echo "<h2> Minecart Stations </h2>";
foreach ($stations as $line) {
    $parts     = explode(",", $line);
    $titlename = str_replace('_', ' ', $parts[0]);
    $titlename = ucwords($titlename);
    echo "<li style=\" display: block;\"><a href=\"map/$parts[0]\">$titlename</a></li>";
    
}
echo "<h2> Towns </h2>";
foreach ($towns as $line) {
    $parts     = explode(",", $line);
    $titlename = str_replace('_', ' ', $parts[0]);
    $titlename = ucwords($titlename);
    echo "<li style=\" display: block;\"><a href=\"map/$parts[0]\">$titlename</a></li>";
}
echo "<h2> Other Locations </h2>";
foreach ($others as $line) {
    $parts     = explode(",", $line);
    $titlename = str_replace('_', ' ', $parts[0]);
    $titlename = ucwords($titlename);
    echo "<li style=\" display: block;\"><a href=\"map/$parts[0]\">$titlename</a></li>";
}

?>
</ul>