<?php
/**
 * Unit Test for the Issuu Document List Controller
 *
 * This file contains the unit test for the Issuu Document List Controller
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

use RcmIssuu\Controller\DocumentListController;
use Zend\Mvc\Controller\PluginManager;

require_once __DIR__ . '/../autoload.php';

/**
 * Unit Test for the Issuu Document List Controller
 *
 * Unit Test for the Issuu Document List Controller
 *
 * @category  Reliv
 * @package   RcmIssuu
 * @author    Westin Shafer <wshafer@relivinc.com>
 * @copyright 2015 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: 1.0
 * @link      http://github.com/reliv
 */
class DocumentListControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \RcmIssuu\Controller\DocumentListController */
    protected $controller;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mockApi;

    /** @var PluginManager */
    protected $pluginManager;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $rcmIsSiteAdminPlugin;

    public function setup()
    {
        $mockSite = $this->getMockBuilder('\Rcm\Entity\Site')
            ->disableOriginalConstructor()
            ->getMock();


        $this->mockApi = $this->getMockBuilder('\RcmIssuu\Service\IssuuApi')
            ->disableOriginalConstructor()
            ->getMock();

        $this->rcmIsSiteAdminPlugin = $this->getMockBuilder('\Rcm\Controller\Plugin\IsSiteAdmin')
            ->disableOriginalConstructor()
            ->getMock();

        $this->pluginManager = new PluginManager();
        $this->pluginManager->setService('rcmIsSiteAdmin', $this->rcmIsSiteAdminPlugin);

        $this->controller = new DocumentListController($this->mockApi, $mockSite);
        $this->controller->setPluginManager($this->pluginManager);
    }

    public function testGetMethod()
    {

        $expected = array(
            'test1' => 'Testing Var 1',
            'test2' => 'Testing Var 2'
        );

        $this->rcmIsSiteAdminPlugin->expects($this->any())
            ->method('__invoke')
            ->will($this->returnValue(true));

        $parmsMock = $this->getMockBuilder('\Zend\Mvc\Controller\Plugin\Params')
            ->disableOriginalConstructor()
            ->getMock();

        $parmsMock->expects($this->any())
            ->method('__invoke')
            ->will($this->returnValue('someUserName'));

        $this->pluginManager->setService('params', $parmsMock);

        $this->mockApi->expects($this->once())
            ->method('getEmbed')
            ->with($this->equalTo('someUserName'), $this->equalTo(22))
            ->will($this->returnValue($expected));

        $result = $this->controller->get(22);

        $this->assertInstanceOf('\Zend\View\Model\JsonModel', $result);

        $jsonResult = $result->serialize();

        $this->assertEquals(json_encode($expected), $jsonResult);
    }

    public function testGetWithInvalidSiteAdmin()
    {
        $this->rcmIsSiteAdminPlugin->expects($this->any())
            ->method('__invoke')
            ->will($this->returnValue(false));

        /** @var \Rcm\Http\Response $result */
        $result = $this->controller->get(22);

        $this->assertInstanceOf('\Rcm\Http\Response', $result);

        $statusCode = $result->getStatusCode();

        $this->assertEquals(401, $statusCode);
    }
}