<?php

require __DIR__ . '/FTPDeploy.php';

$configEncoded = $argv[1];

$config = json_decode(base64_decode($configEncoded), true);

print_r($config);

$ftp = new FTPDeploy($config['sourceDir'], $config['targetDir'], $config);
$ftp->deploy();
