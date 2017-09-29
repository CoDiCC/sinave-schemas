<?php
/**
 * Created by PhpStorm.
 * User: estevao.santos
 * Date: 29/09/2017
 * Time: 15:42
 */

namespace CoDiCC\SinaveSchema;


use Symfony\Component\Yaml\Yaml;

class SchemaLoader
{
    /** @var string  */
    protected $basePath;

    /**
     * TODO make this return a Schema Object
     * @var array
     */
    protected $schemas = [];


    public function getSchemas()
    {
        return $this->schemas;
    }

    public function __construct(string $basePath = null, string $fileExtension = 'json')
    {
        $fileExtension = strtolower(ltrim(trim($fileExtension), '.'));

        $basePath = $basePath ?? __DIR__ . '/../dist/';
        $path = realpath($basePath);

        if (!$path) {
            throw new LogicException(sprintf('$basepath %s path could not be found', $basePath));
        }

        $this->basePath = $path;
        $this->loadSchemas($path, $fileExtension);
    }

    private function loadSchemas(string $path, string $format)
    {
        $dirIterator = new \DirectoryIterator($path);

        foreach ($dirIterator as $f) {
            if ($f->isDot()) {
                continue;
            }
            if ($f->isDir()) {
                $this->loadSchemas($f->getRealPath(), $format);
            } else if ($f->isFile()){
                $start = strlen($format);
                $start = -$start;
                $ext = substr($f->getRealPath(), $start);
                if ($ext === $format) {
                    $file = file_get_contents($f->getRealPath());

                    switch($format) {
                        case 'yaml':
                        case 'yml':
                            $schema = Yaml::parse($file);
                            break;
                        case 'json':
                            $schema = json_decode($file);
                            break;
                        default:
                            throw new RuntimeExtension(sprintf('Format %s is not supported'));
                    }
                    $key = explode('.', $f->getFilename())[0];
                    $this->schemas[$key] = $schema;
                }
            }
        }
    }
}
