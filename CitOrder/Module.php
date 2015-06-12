<?php
namespace CitOrder;

use CitOrder\Model\Order;
use CitOrder\Model\OrderTable;
use CitOrder\Model\OrderProduct;
use CitOrder\Model\OrderProductTable;
use CitOrder\Model\OrderProductOption;
use CitOrder\Model\OrderProductOptionTable;
use CitOrder\Model\OrderWithdrawal;
use CitOrder\Model\OrderWithdrawalTable;
use CitOrder\Model\Site;
use CitOrder\Model\SiteContact;
use CitOrder\Model\SiteContactTable;
use CitOrder\Model\SiteStock;
use CitOrder\Model\SiteStockTable;
use CitOrder\Model\SiteTable;
use CitOrder\Model\Stock;
use CitOrder\Model\StockTable;
use CitOrder\Model\TmpSite;
use CitOrder\Model\TmpStock;
use CitCore\Model\GenericTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Authentication\Storage;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\EventManager\EventInterface;
use Zend\Validator\AbstractValidator;

class Module //implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'CitOrder\Model\OrderTable' =>  function($sm) {
                	$tableGateway = $sm->get('OrderTableGateway');
                	$table = new OrderTable($tableGateway);
                	return $table;
                },
                'OrderTableGateway' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new Order());
                	return new TableGateway('order', $dbAdapter, null, $resultSetPrototype);
                },
                'CitOrder\Model\OrderProductTable' =>  function($sm) {
                	$tableGateway = $sm->get('OrderProductTableGateway');
                	$table = new OrderProductTable($tableGateway);
                	return $table;
                },
                'OrderProductTableGateway' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new OrderProduct());
                	return new TableGateway('order_product', $dbAdapter, null, $resultSetPrototype);
                },
                'CitOrder\Model\OrderProductOptionTable' =>  function($sm) {
                	$tableGateway = $sm->get('OrderProductOptionTableGateway');
                	$table = new OrderProductOptionTable($tableGateway);
                	return $table;
                },
                'OrderProductOptionTableGateway' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new OrderProductOption());
                	return new TableGateway('order_product_option', $dbAdapter, null, $resultSetPrototype);
                },
                'CitOrder\Model\OrderWithdrawalTable' =>  function($sm) {
                	$tableGateway = $sm->get('OrderWithdrawalTableGateway');
                	$table = new OrderWithdrawalTable($tableGateway);
                	return $table;
                },
                'OrderWithdrawalTableGateway' => function ($sm) {
                	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                	$resultSetPrototype = new ResultSet();
                	$resultSetPrototype->setArrayObjectPrototype(new OrderWithdrawal());
                	return new TableGateway('order_withdrawal', $dbAdapter, null, $resultSetPrototype);
                },
                'CitOrder\Model\SiteTable' =>  function($sm) {
                    $tableGateway = $sm->get('SiteTableGateway');
                    $table = new SiteTable($tableGateway);
                    return $table;
                },
                'SiteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Site());
                    return new TableGateway('order_site', $dbAdapter, null, $resultSetPrototype);
                },
 	          	'CitOrder\Model\SiteContactTable' =>  function($sm) {
                    $tableGateway = $sm->get('SiteContactTableGateway');
                    $table = new SiteContactTable($tableGateway);
                    return $table;
                },
                'SiteContactTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new SiteContact());
                    return new TableGateway('order_site_contact', $dbAdapter, null, $resultSetPrototype);
                },
 	          	'CitOrder\Model\StockTable' =>  function($sm) {
                    $tableGateway = $sm->get('StockTableGateway');
                    $table = new StockTable($tableGateway);
                    return $table;
                },
                'StockTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Stock());
                    return new TableGateway('order_stock', $dbAdapter, null, $resultSetPrototype);
                },
 	          	'CitOrder\Model\TmpSiteTable' =>  function($sm) {
                    $tableGateway = $sm->get('TmpSiteTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'TmpSiteTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TmpSite());
                    return new TableGateway('tmp_order_site', $dbAdapter, null, $resultSetPrototype);
                },
 	          	'CitOrder\Model\TmpStockTable' =>  function($sm) {
                    $tableGateway = $sm->get('TmpStockTableGateway');
                    $table = new GenericTable($tableGateway);
                    return $table;
                },
                'TmpStockTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new TmpStock());
                    return new TableGateway('tmp_order_stock', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }
    
    public function onBootstrap(EventInterface $e)
    {
    	$serviceManager = $e->getApplication()->getServiceManager();
    
    	// Set the translator for default validation messages
    	$translator = $serviceManager->get('translator');
    	AbstractValidator::setDefaultTranslator($translator);
    }
}
