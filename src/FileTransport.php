<?php

namespace Maximaster\FileMailerTransport;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;

class FileTransport extends AbstractTransport implements TransportInterface
{
    /** @var string */
    protected $pathTemplate;

    /** @var LoggerInterface */
    protected $log;

    protected $options = [
        'new_directory_mode' => 0777,
        'hash_algo' => 'crc32',
        'path_renderer' => 'strftime',
    ];

    public function __construct(
        string $baseDirectory,
        array $options,
        EventDispatcherInterface $dispatcher = null,
        LoggerInterface $logger = null
    ) {
        parent::__construct($dispatcher, $logger);

        $this->log = $logger ?: new NullLogger;
        $this->pathTemplate = $baseDirectory;
        $this->options = array_merge($this->options, $options);
    }

    protected function doSend(SentMessage $message): void
    {
        $filePath = $this->buildPath($this->pathTemplate, $message);
        $fileDir = dirname($filePath);

        if (!file_exists($fileDir) && !mkdir($fileDir, $this->options['new_directory_mode'], true)) {
            throw new RuntimeException(sprintf('Unable to create directory "%s".', $fileDir));
        }

        $saveResult = file_put_contents($filePath, $message->toString());

        if (false === $saveResult) {
            $this->log->error(sprintf('Unable to save message as "%s"', $filePath));
        }
    }

    protected function buildPath(string $template, SentMessage $message): string
    {
        $path = $template;

        $path = str_replace(
            '@hash',
            hash($this->options['hash_algo'], $message->getMessage()->toString()),
            $path
        );

        $path = strftime($path);
        return $path;
    }

    public function __toString(): string
    {
        return 'file';
    }
}
