<?php
/**
 * Created by PhpStorm.
 * User: estevao.santos
 * Date: 25/09/2017
 * Time: 16:56
 */

require_once(__DIR__ . '/../vendor/autoload.php');


function getIncludes()
{
    static $yml = '';

    if ($yml !== '') {
        return $yml;
    }

    $directory = new DirectoryIterator(realpath(__DIR__ . '/includes'));
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
            $filepath = realpath(__DIR__ . '/../dist/');
            $filename = str_replace('.yml', '.json', $f->getFilename());
            $schema = readSchema($f->getRealPath());
            createJson($schema, realpath($filepath . '/' . $filename));
        }
    }
}

function readSchema(string $filepath)
{
    $yml = getIncludes() . file_get_contents($filepath);
    $schema = \Symfony\Component\Yaml\Yaml::parse($yml);
    return $schema;
}


function createJson(array $schema, string $path)
{
    $schema['id'] = str_replace('.yml', '.json', $schema['id']);
    return file_put_contents($path, json_encode($schema, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
}


recurseDirectories(realpath(__DIR__ . '/schemas'));





//
