<?php
declare(strict_types=1);

namespace Kminek\Marklink;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ServerException;
use Exception;

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
            'base_uri' => 'http://awesomelist.kminek.pl/api/',
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
        $request = new Request('POST', 'marklink/parse');
        $requestBody = \GuzzleHttp\Psr7\stream_for(json_encode([
            'markdown' => $markdown,
        ]));
        $request = $request->withBody($requestBody);
        try {
            $response = $this->httpClient->send($request);
            $responseBody = (string) $response->getBody();
            $result = json_decode($responseBody, true);
            return $result;
        } catch (ServerException $e) {
            $responseBody = (string) $e->getResponse()->getBody();
            $result = json_decode($responseBody, true);
            if (!isset($result['error'])) {
                throw $e;
            }
            $e = ($result['error']['type'] === ParserException::class) ?
                new ParserException($result['error']['message'], (int) $result['error']['code']) :
                new Exception($result['error']['message'], (int) $result['error']['code'])
            ;
            throw $e;
        }
    }
}
