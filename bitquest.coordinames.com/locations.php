<h3>Current Notable Locations:</h3>
<ul>
<?php 
$lines = file('alias.txt',FILE_IGNORE_NEW_LINES);
sort($lines);
foreach($lines as $line)
{
        $parts = explode(",",$line);
        $titlename=str_replace('_', ' ', $parts[0]);
        $titlename=ucwords ($titlename);
  echo "<li style=\" display: block;\"><a href=\"map/$parts[0]\">$titlename</a></li>";
}

?>
</ul>