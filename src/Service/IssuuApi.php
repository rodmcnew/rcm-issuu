<?php

namespace RcmIssuu\Service;

use Guzzle\Http\Client;
use RcmIssuu\Exception\ApiException;
use RcmIssuu\Exception\InvalidFormatException;

class IssuuApi
{
    /** @var \Guzzle\Http\Client */
    protected $client;

    public function getEmbed($userName, $docTitle)
    {
        $endPoint = 'https://issuu.com/oembed';

        $url = 'http://issuu.com/'.$userName.'/docs/'.$docTitle;

        $send = array(
            'url' => $url,
            'format' => 'json'
        );

        $client = $this->getClient();

        $request = $client->get($endPoint, array(), array(
            'query' => $send
        ));

        $response = $request->send();

        $statusCode = $response->getStatusCode();

        if ($statusCode != 200) {
            throw new ApiException('Unable to get document from Issuu');
        }

        $jsonData = $response->json();

        if (empty($jsonData['html'])) {
            throw new InvalidFormatException('Invalid Format for response');
        }

        return $jsonData;
    }

    /**
     * Get the set Guzzle client or return a new client if none present
     *
     * @return \Guzzle\Http\Client
     */
    public function getClient()
    {
        if (!$this->client) {
            $this->client = new Client();
        }

        return $this->client;
    }
}
