<?php

namespace App\Service;

use HAB\OAI\PMH\Model;
use HAB\OAI\PMH\ProtocolError;
use HAB\OAI\PMH\Repository\RepositoryInterface;
use Phpoaipmh\Exception\HttpException;
use Phpoaipmh\HttpAdapter\HttpAdapterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CacheClient implements HttpAdapterInterface
{


    public function __construct(
        private CacheInterface $cache,
        private LoggerInterface $logger,
        private HttpClientInterface $httpClient)
    {
    }

    public function request($url)
    {
        $key = md5($url);

        $content = $this->cache->get($key, function (ItemInterface $item) use ($url) {
//            $item->expiresAfter(3600 * 24 * 2100);
            $this->logger->warning("fetching " . $url);
            try {
                $content = $this->httpClient->request('GET', $url)->getContent();
            } catch (\Exception $exception) {
                dd($exception->getMessage());
                $content = null; // not found?
            }
            return $content;
        });

        // this is the raw XML
        return $content;
    }


    public function getRecord($identifier, $metadataPrefix): Model\ResponseBodyInterface
    {
        // TODO: Implement getRecord() method.
    }

    public function identify(): Model\Identity
    {
        $identity = new Model\Identity();
        return $identity;
        // TODO: Implement identify() method.
    }

    public function listIdentifiers($metadataPrefix, $from = null, $until = null, $set = null): Model\ResponseBodyInterface
    {
        // TODO: Implement listIdentifiers() method.
    }

    public function listRecords($metadataPrefix, $from = null, $until = null, $set = null): Model\ResponseBodyInterface
    {
        // TODO: Implement listRecords() method.
    }

    public function listMetadataFormats($identifier = null): Model\ResponseBodyInterface
    {
        // TODO: Implement listMetadataFormats() method.
    }

    public function listSets(): Model\ResponseBodyInterface
    {
        // TODO: Implement listSets() method.
    }

    public function resume($verb, $resumptionToken): Model\ResponseBodyInterface
    {
        // TODO: Implement resume() method.
    }
}
