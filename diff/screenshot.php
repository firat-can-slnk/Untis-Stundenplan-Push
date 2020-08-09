<?php
$rand = rand();
$filename = "image_$rand.png";
$url = "$locationURL/diff/?$rand";
$secret_key = ""; //EDIT
$final_url = "http://api.screenshotlayer.com/api/capture?access_key=XXXXXXXXXXXXXXXXXXX&url=$locationURL/diff/?$rand&viewport=380x1100&width=380&secret_key=$secret_key";
file_put_contents("$filename", file_get_contents($final_url));
echo "$locationURL/diff/$filename";
?>

