<?php
/**
 * Created by PhpStorm.
 * User: estevao.santos
 * Date: 29/09/2017
 * Time: 16:46
 */

require_once(__DIR__ . '/../vendor/autoload.php');

$loader = new \CoDiCC\SinaveSchema\SchemaLoader();

var_dump($loader->getSchemas());

