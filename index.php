<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>SlankLink</title>
  <link rel="stylesheet" href="slanklink.css">
</head>

<body>

<h1>SlankLink</h1>

<form action="./lnk.php" method="post">
<p>
<input type="text" name="url" class="urlentry" placeholder="enter your url here">
<input type="submit" value="save">
</p>
</form>

<?php

/* SlankLink - A simple, single-file php script that acts as a "url shortener".
 * Does not rely on a database, everything is stored in filesystem. No
 * external dependencies, requires only a web server with PHP support.
 *
 * VERSION: 20200722
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

echo "<p><span class=\"grey\">target url:</span> {$url}</p>\n";
$id = rand(0, 60466175);

$ids = base_convert($id, 10, 36);

if (file_exists($ids)) {
  echo '<p class="error">ERROR: collision. try again.</p>';
  echo "<p class=\"debug\">orig id: {$id}<br>\n";
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

echo "<p><span class=\"grey\">short url:</span> <a href=\"{$ids}\">{$selfaddr_root}{$ids}</a></p>";

SKIP:
?>
</body>
</html>
