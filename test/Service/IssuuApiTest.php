<?php
/**
 * Unit Test for the Issuu Api Service
 *
 * This file contains the unit test for the Issuu Api Service
 *
 * PHP version 5.4
 *
 * LICENSE: BSD
 *
 * @category  Reliv
 * @package   RcmIssuu
 * @author    Westin Shafer <wshafer@relivinc.com>
 * @copyright 2015 Reliv International
 * @license   License.txt New BSD License
 * @version   GIT: <git_id>
 * @link      http://github.com/reliv
 */
namespace RcmIssuuTest\Controller;

use Guzzle\Http\Message\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Service\Client;
use RcmIssuu\Service\IssuuApi;

require_once __DIR__ . '/../autoload.php';

/**
 * Unit Test for the Issuu Api Service
 *
 * Unit Test for the Issuu Api Service
 *
 * @category  Reliv
 * @package   RcmIssuu
 * @author    Westin Shafer <wshafer@relivinc.com>
 * @copyright 2015 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: 1.0
 * @link      http://github.com/reliv
 */
class IssuuApiTest extends \PHPUnit_Framework_TestCase
{
    /** @var IssuuApi */
    protected $service;

    public function setup()
    {
        $this->service = new IssuuApi();
    }

    public function testGetEmbed()
    {
        $client = $this->service->getClient();

        $expected = array(
            'html' => '<embed id="test_embed" src="#"></embed>'
        );

        $mock = new MockPlugin();
        $mock->addResponse(new Response(200, null, json_encode($expected)));

        $client->addSubscriber($mock);

        $result = $this->service->getEmbed('test_account', 'test_title');

        $this->assertEquals($expected, $result);
    }

    /**
     * @expectedException \Guzzle\Http\Exception\ServerErrorResponseException
     */
    public function testGetEmbedRemoteServerError()
    {
        $client = $this->service->getClient();

        $mock = new MockPlugin();
        $mock->addResponse(new Response(500));

        $client->addSubscriber($mock);

        $this->service->getEmbed('test_account', 'test_title');
    }

    /**
     * @expectedException \RcmIssuu\Exception\ApiException
     */
    public function testGetEmbedIncorrectStatusCode()
    {
        $client = $this->service->getClient();

        $mock = new MockPlugin();
        $mock->addResponse(new Response(201));

        $client->addSubscriber($mock);

        $this->service->getEmbed('test_account', 'test_title');
    }

    /**
     * @expectedException \RcmIssuu\Exception\InvalidFormatException
     */
    public function testGetEmbedInvalidFormatReturned()
    {
        $client = $this->service->getClient();

        $expected = array();

        $mock = new MockPlugin();
        $mock->addResponse(new Response(200, null, json_encode($expected)));

        $client->addSubscriber($mock);

        $this->service->getEmbed('test_account', 'test_title');
    }
}