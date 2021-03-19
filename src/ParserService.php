<?php

declare(strict_types=1);

namespace Kminek\Marklink;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;
use Throwable;

/**
 * Class ParserService
 * @package Kminek\Marklink
 */
class ParserService implements ParserInterface
{
    /**
     * @var ClientInterface
     */
    protected $httpClient;

    /**
     * Constructor
     *
     * @param ClientInterface|null $httpClient
     */
    public function __construct(ClientInterface $httpClient = null)
    {
        $options = [
            'base_uri' => 'https://awesomelist.kminek.pl/api/',
            'timeout' => 10,
            'http_errors' => true,
        ];
        $this->httpClient = ($httpClient === null) ? new Client($options) : $httpClient;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(string $markdown): array
    {
        try {
            $response = $this->httpClient->request('POST', 'markdown', [
                'form_params' => [
                    'input' => $markdown,
                ]
            ]);
            $responseBody = (string)$response->getBody();
            $result = json_decode(
                $responseBody,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
            return $result;
        } catch (Throwable $e) {
            throw new ParserException($e->getMessage(), 0, $e);
        }
    }
}
