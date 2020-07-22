<!DOCTYPE html>
<html style="background: #fff;">

<head>
  <meta charset="UTF-8">
  <title>SlankLink</title>
  <link rel="stylesheet" href="slanklink.css">
</head>

<body style="font-family: sans; font-size: 1.2em">

<h1>SlankLink</h1>

<form action="./lnk.php" method="post">
<p style="text-align: center; margin-top: 3em;">
<input type="text" name="url" style="width: 50em;">
<input type="submit" value="save">
</p>
</form>


<?php

/* SlankLink - A simple, single-file php script that acts as a "url shortener".
 * Does not rely on a database, everything is stored in filesystem. No
 * external dependencies, requires only a web server with PHP support.
 *
 * Copyright (C) 2020 Mateusz Viste
 *
 * SlankLink is open-source software, published under the terms of the ISC license.
 *
 * Permission to use, copy, modify, and/or distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE. */

if (!isset($_POST['url'])) goto SKIP;
$url = trim($_POST['url']);
if (empty($url)) goto SKIP;

// validate & adjust url
if (stristr($url, '://') === FALSE) $url = 'http://' . $url;

echo "<p style=\"text-align: center;\"><span style=\"color: #777;\">target url:</span> {$url}</p>\n";
$id = rand(0, 60466175);

$ids = base_convert($id, 10, 36);

if (file_exists($ids)) {
  echo '<p style="background-color: #f66; font-weight: bold; text-align: center; padding: 0.5em;">ERROR: collision. try again.</p>';
  echo "<p style=\"color: #777;\">orig id: {$id}<br>\n";
  echo "encd id: {$ids}</p>\n";
  goto SKIP;
}

mkdir($ids);
$f = fopen($ids . "/index.php", "w");
fwrite($f, "<?php header('location: " . trim($url) . "', true, 301); exit(); ?>\n");
fclose($f);

$selfaddr_root = 'http://';
if (!empty($_SERVER['HTTPS'])) $selfaddr_root = 'https://';
$selfaddr_root .= $_SERVER['HTTP_HOST'];
if (isset($_SERVER['SERVER_PORT']) && (intval($_SERVER['SERVER_PORT']) != 80)) $selfaddr_root .= ':' . $_SERVER['SERVER_PORT'];
$selfaddr_root .= dirname($_SERVER['REQUEST_URI']);

echo "<p style=\"text-align: center;\">short url: <a href=\"{$ids}\">{$selfaddr_root}{$ids}</a></p>";

SKIP:
?>

</body>
</html>
