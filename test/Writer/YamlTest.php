<?php

declare(strict_types=1);

namespace LaminasTest\Config\Writer;

use Laminas\Config\Config;
use Laminas\Config\Reader\Yaml as YamlReader;
use Laminas\Config\Writer\Yaml as YamlWriter;

use function explode;
use function getenv;

/**
 * @group      Laminas_Config
 */
class YamlTest extends AbstractWriterTestCase
{
    protected function setUp(): void
    {
        if (! getenv('TESTS_LAMINAS_CONFIG_YAML_ENABLED')) {
            $this->markTestSkipped('Yaml test for Laminas\Config skipped');
        }

        if ($lib = getenv('TESTS_LAMINAS_CONFIG_YAML_LIB_INCLUDE')) {
            require_once $lib;
        }

        if ($readerCallback = getenv('TESTS_LAMINAS_CONFIG_READER_YAML_CALLBACK')) {
            $yamlReader = explode('::', $readerCallback);
            if (isset($yamlReader[1])) {
                $this->reader = new YamlReader([$yamlReader[0], $yamlReader[1]]);
            } else {
                $this->reader = new YamlReader([$yamlReader[0]]);
            }
        } else {
            $this->reader = new YamlReader();
        }

        if ($writerCallback = getenv('TESTS_LAMINAS_CONFIG_WRITER_YAML_CALLBACK')) {
            $yamlWriter = explode('::', $writerCallback);
            if (isset($yamlWriter[1])) {
                $this->writer = new YamlWriter([$yamlWriter[0], $yamlWriter[1]]);
            } else {
                $this->writer = new YamlWriter([$yamlWriter[0]]);
            }
        } else {
            $this->writer = new YamlWriter();
        }
    }

    public function testNoSection()
    {
        $config = new Config(['test' => 'foo', 'test2' => ['test3' => 'bar']]);

        $this->writer->toFile($this->getTestAssetFileName(), $config);

        $config = $this->reader->fromFile($this->getTestAssetFileName());

        self::assertEquals('foo', $config['test']);
        self::assertEquals('bar', $config['test2']['test3']);
    }

    public function testWriteAndReadOriginalFile()
    {
        $config = $this->reader->fromFile(__DIR__ . '/_files/allsections.yaml');

        $this->writer->toFile($this->getTestAssetFileName(), $config);

        $config = $this->reader->fromFile($this->getTestAssetFileName());

        self::assertEquals('multi', $config['all']['one']['two']['three']);
    }
}
