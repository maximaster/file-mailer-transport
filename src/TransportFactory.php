<?php

namespace Maximaster\FileMailerTransport;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TransportFactory
{
    /** @var string */
    protected $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function __invoke(string $dsn, EventDispatcherInterface $dispatcher = null, HttpClientInterface $client = null, LoggerInterface $logger = null): TransportInterface
    {
        $url = parse_url($dsn);

        $parsedOptions = [];
        if (!empty($url['query'])) {
            parse_str($url['query'], $parsedOptions);
        }

        if ($url['scheme'] === 'file') {
            return new FileTransport(
                implode(DIRECTORY_SEPARATOR, [
                    $this->projectDir,
                    $url['path'],
                ]),
                $parsedOptions,
                $dispatcher,
                $logger
            );
        }

        return Transport::fromDsn($dsn, $dispatcher, $client, $logger);
    }
}
