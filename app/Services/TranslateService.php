<?php

namespace App\Services;

use ErrorException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use App\Traits\TokenGenerator;

class TranslateService
{
    use TokenGenerator;

    /**
     * @var Client HTTP Client
     */
    protected $client;

    /**
     * @var string|null Source language - from where the string should be translated
     */
    protected $source;

    /**
     * @var string Target language - to which language string should be translated
     */
    protected $target;

    /**
     * @var string URL base
     */
    protected $url = 'https://translate.google.com/translate_a/single';

    /**
     * @var array URL Parameters
     */
    protected $urlParams = [
        'client' => 'webapp',
        'dt' => 't',
        'sl' => null,
        'tl' => null,
        'q' => null,
    ];

    /**
     * @param string $source
     * @param string $target
     */
    public function __construct(string $source = 'en', string $target = 'hy')
    {
        $this->client = new Client();
        $this->setTarget($target)->setSource($source);
    }


    public function prepareParams(string $text)
    {
        $this->urlParams = array_merge($this->urlParams, [
            'sl' => $this->source,
            'tl' => $this->target,
            'tk' => $this->TL($text),
            'q' => $text
        ]);
    }


    /**
     * @param string $target
     * @return $this
     */
    public function setTarget(string $target): self
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @param string|null $source
     * @return $this
     */
    public function setSource(string $source = null): self
    {
        $this->source = $source ?? 'auto';
        return $this;
    }


    /**
     * @param string $text String to translate
     * @param bool $isTest
     * @return string|null
     * @throws ErrorException|GuzzleException
     */
    public function translate(string $text, $isTest = false)
    {
        $this->prepareParams($text);
        return $isTest ? $this->getData() : collect($this->getData())->get(0);
    }

    /**
     * Get response array.
     * @return array
     * @throws ErrorException|GuzzleException
     * @throws RequestException
     */
    public function getData(): array
    {
        try {
            $response = $this->client->get($this->url, [
                    'query' => http_build_query($this->urlParams)
                ]
            );
        } catch (RequestException $e) {
            throw new ErrorException($e->getMessage());
        }
        return json_decode($response->getBody(), true);
    }

}
