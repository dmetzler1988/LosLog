<?php
namespace LosMiddleware\LosLog;

use Interop\Container\ContainerInterface;
use Laminas\Log\Logger;
use Laminas\Log\PsrLoggerAdapter;
use Laminas\Log\Writer\Stream;
use Laminas\ServiceManager\Factory\FactoryInterface;

class LoggerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return object|PsrLoggerAdapter
     * @throws Exception\InvalidArgumentException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $losConfig = array_key_exists('loslog', $config) ? $config['loslog'] : [];

        $logDir = array_key_exists('log_dir', $losConfig) ? $losConfig['log_dir'] : 'data/logs';
        $logFile = array_key_exists('error_logger_file', $losConfig) ? $losConfig['error_logger_file'] : 'error.log';

        $fileName = $this->validateLogFile($logFile, $logDir);
        $zendLogLogger = new Logger();
        $zendLogLogger->addWriter(new Stream($fileName));

        return new PsrLoggerAdapter($zendLogLogger);
    }

    /**
     * @param string $logFile
     * @param string $logDir
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public static function validateLogFile($logFile, $logDir)
    {
        // Is logFile a stream url?
        if (strpos($logFile, '://') !== false) {
            return $logFile;
        }

        if (! file_exists($logDir) || ! is_writable($logDir)) {
            throw new Exception\InvalidArgumentException("Log dir {$logDir} must exist and be writable!");
        }

        $fileName = $logDir.DIRECTORY_SEPARATOR.$logFile;

        if (file_exists($fileName) && ! is_writable($fileName)) {
            throw new Exception\InvalidArgumentException("Log file {$fileName} must be writable!");
        }

        return $fileName;
    }
}
