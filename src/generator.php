<?php
/**
 * generator.php
 *
 * CoDiCC - SINAVE Schemas <https://github.com/CoDiCC/sinave-schemas>
 * Sinave Schemas for Communicable Disease Case Classifier Library
 *
 * ---
 *
 * Copyright (C) 2017 Estevão Soares dos Santos - All Rights Reserved
 *
 * You may use, distribute and modify this code under the
 * terms of the MIT license.
 * You should have received a copy of the MIT license with
 * this file. If not, please write visit :
 * https://github.com/CoDiCC/sinave-schemas/blob/master/LICENSE
 *
 */

require_once(__DIR__ . '/../vendor/autoload.php');

use \Symfony\Component\Yaml\Yaml;


function getIncludes()
{
    static $yml = '';

    if ($yml !== '') {
        return $yml;
    }

    // first get the includes
    $directory = new DirectoryIterator(realpath(__DIR__ . '/includes'));
    foreach ($directory as $f) {
        if ($f->isDot() || $f->isDir()) continue;
        $yml .= "\n" . file_get_contents($f->getRealPath());
    }

    // then get partials
    $directory = new DirectoryIterator(realpath(__DIR__ . '/partials'));
    foreach ($directory as $f) {
        if ($f->isDot() || $f->isDir()) continue;
        $yml .= "\n" . file_get_contents($f->getRealPath());
    }

    return $yml;
}

function recurseDirectories(string $path)
{
    $directories = new DirectoryIterator($path);

    foreach ($directories as $f) {
        if ($f->isDot()) continue;

        if ($f->isDir()) {
            //new directory iterator and recurse
        } else {
            $filename = str_replace('.yml', '', $f->getFilename());
            $filepath = realpath(__DIR__ . '/../dist/') . '/' . $filename;
            $schema = readSchema($f->getRealPath());
            createYaml($schema, $filepath);
            createJson($schema, $filepath);
        }
    }
}

function readSchema(string $filepath, bool $includeBase = true)
{
    $yml = getIncludes() . file_get_contents($filepath);
    $schema = Yaml::parse($yml);
    $newSchema = [];
    foreach ($schema as $key => $val) {
      if (substr($key,0,1) === '@') {
        continue;
      }
      $newSchema[$key] = $val;
    }
    return $newSchema;
}


function createJson(array $schema, string $path)
{
    $path = $path . '.json';
    $schema['id'] = str_replace('.yml', '.json', $schema['id']);
    return file_put_contents($path, json_encode($schema, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}

function createYaml(array $schema, string $path)
{
    $path = $path . '.yml';
    return file_put_contents($path, Yaml::dump($schema, 6, 2));
}

recurseDirectories(realpath(__DIR__ . '/schemas'));
