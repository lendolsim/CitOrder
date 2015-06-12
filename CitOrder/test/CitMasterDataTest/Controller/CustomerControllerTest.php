<?php

namespace CitMasterDataTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class CustomerControllerTest extends AbstractHttpControllerTestCase
{
	protected $traceError = true;
	
	public function setUp()
	{
		$this->setApplicationConfig(
				include '/Library/WebServer/Documents/confianceit.net/config/application.config.php'
		);
		parent::setUp();
	}
	public function testIndexActionCanBeAccessed()
	{
	    $customerTableMock = $this->getMockBuilder('CitMasterData\Model\CustomerTable')
	                              ->disableOriginalConstructor()
	                              ->getMock();
	
	    $customerTableMock->expects($this->once())
	                      ->method('fetchAllPagination')
	                      ->will($this->returnValue(array()));
	
	    $serviceManager = $this->getApplicationServiceLocator();
	    $serviceManager->setAllowOverride(true);
	    $serviceManager->setService('CitMasterData\Model\CustomerTable', $customerTableMock);

		$this->dispatch('/customer');
	    $this->assertResponseStatusCode(200);
	
	    $this->assertModuleName('Customer');
	    $this->assertControllerName('MasterData\Controller\Customer');
	    $this->assertControllerClass('CustomerController');
	    $this->assertMatchedRouteName('customer');
	}
}
