<?php

namespace LosMiddleware\LosLog;

use org\bovigo\vfs\vfsStream;
use LosMiddleware\LosLog\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Zend\Log\PsrLoggerAdapter;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2015-11-02 at 10:24:09.
 */
class AbstractLoggerTest extends TestCase
{
    /**
     * @var AbstractLogger
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = $this->getMockForAbstractClass('LosMiddleware\LosLog\AbstractLogger');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers LosMiddleware\LosLog\AbstractLogger::generateFileLogger
     */
    public function testGenerateFileLogger()
    {
        $this->assertInstanceOf(PsrLoggerAdapter::class, $this->object->generateFileLogger('error.log', '.'));
    }

    /**
     * @covers LosMiddleware\LosLog\AbstractLogger::validateLogFile
     */
    public function testValidateLogFileWithInvalidDir()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->object->validateLogFile('error.log', 'missing');
    }

    /**
     * @covers LosMiddleware\LosLog\AbstractLogger::validateLogFile
     */
    public function testValidateLogFile()
    {
        $this->assertSame('./error.log', $this->object->validateLogFile('error.log', '.'));
    }

    /**
     * @covers LosMiddleware\LosLog\AbstractLogger::validateLogFile
     */
    public function testValidateLogFileWithStream()
    {
        vfsStream::setup('home');
        $file = vfsStream::url('home/static.log');

        $this->assertSame($file, $this->object->validateLogFile($file, '.'));
    }
}
