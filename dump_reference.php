<?php

use ScayTrase\Api\Cruds\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;

require_once __DIR__ . '/bootstrap.php';

$config = new Configuration();
$dumper = new YamlReferenceDumper();

echo $dumper->dump($config);
