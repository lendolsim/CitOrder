<?php
namespace CitOrder\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use CitCore\Controller\Functions;
use CitOrder\Model\Stock;
use CitOrder\Model\SiteStock;
use CitOrder\Model\TmpStock;
use CitOrder\Form\StockForm;
use CitOrder\Form\SiteStockForm;
use Zend\Session\Container;
use Zend\Http\Client;
use Zend\Http\Request;

class StockController extends AbstractActionController
{
	protected $linkTable;
   	protected $productTable;
   	protected $siteTable;
   	protected $stockTable;
   	protected $siteStockTable;
   	protected $tmpStockTable;
   	
   	public function indexAction()
    {
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	 
    	// Retrieve the allowed routes
    	$allowedRoutes = Functions::getAllowedRoutes($this);

    	// Retrieve the user's instance
    	$instance_id = Functions::getInstanceId($this);
    	
    	// Prepare the SQL request
    	$currentPage = $this->params()->fromQuery('page', 1);
    	$major = $this->params()->fromQuery('major', NULL);
    	if (!$major) $major = 'identifier';
    	$dir = $this->params()->fromQuery('dir', NULL);
    	if (!$dir) $dir = 'ASC';
    	$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    	$select = $this->getStockTable()->getSelect()
    		->join('order_withdrawal', 'order_stock.id = order_withdrawal.stock_id', array('order_id'), 'left')
    		->order(array($major.' '.$dir, 'identifier'));
    	$paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\DbSelect($select, $adapter));
    	$paginator->setCurrentPageNumber($currentPage);
    	$paginator->setDefaultItemCountPerPage(30);

    	// Return the link list
    	return new ViewModel(array(
    		'current_user' => $current_user,
    		'title' => 'Stock',
       		'major' => $major,
    		'dir' => $dir,
    		'stocks' => $paginator,
        ));
    }

    public function addAction()
    {
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	
    	// Create the form object
    	$form = new StockForm();

    	// Retrieve the available product list
    	$select = $this->getProductTable()->getSelect()
	    	->where(array('is_on_sale' => TRUE))
	    	->order(array('caption'));
    	$cursor = $this->getProductTable()->selectWith($select);
    	$products = array();
    	foreach ($cursor as $product) $products[] = $product;

    	// Retrieve the site list
    	$select = $this->getSiteTable()->getSelect()
    	->order(array('caption'));
    	$cursor = $this->getSiteTable()->selectWith($select);
    	$sites = array();
    	foreach ($cursor as $site) $sites[$site->id] = $site;
    	 
    	$form->addElements($products, $sites);
    	 
    	$form->get('submit')->setValue('Add');

		// Set the form filters and hydrate it with the data from the request
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$stock = new Stock();
    		$form->setInputFilter($stock->getInputFilter());
     		$form->setData($request->getPost());

     		// Update the entity with the data from the valid form and create it into the database
    		if ($form->isValid()) {
    			$stock->exchangeArray($form->getData());
        		$stock->id = NULL;
        		if ($stock->product_id == '') $stock->product_id = NULL;
        		$this->getStockTable()->save($stock, $current_user);
    
    			// Redirect to the index
    			return $this->redirect()->toRoute('stock');
    		}
    	}
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Stock',
       		'form' => $form,
    		'products' => $products
    	);
    }

    public function updateAction()
    {
    	// Check the presence of the id parameter for the entity to update
    	$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
    		return $this->redirect()->toRoute('stock');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	 
    	// Retrieve the allowed routes
    	$allowedRoutes = Functions::getAllowedRoutes($this);

    	// Retrieve the user's instance
    	$instance_id = Functions::getInstanceId($this);
    	
    	// Create the entity object and initialize it from the database 
    	$stock = $this->getStockTable()->get($id);
    	
    	// Create the form object and initialize it from the existing entity
    	$form  = new StockForm();

    	// Retrieve the available products list
    	$select = $this->getProductTable()->getSelect()
	    	->where(array('is_on_sale' => TRUE))
	    	->order(array('caption'));
    	$cursor = $this->getProductTable()->selectWith($select);
    	$products = array();
    	foreach ($cursor as $product) $products[$product->id] = $product;

    	// Retrieve the site list
    	$select = $this->getSiteTable()->getSelect()
    	->order(array('caption'));
    	$cursor = $this->getSiteTable()->selectWith($select);
    	$sites = array();
    	foreach ($cursor as $site) $sites[$site->id] = $site;
    	
    	$form->addElements($products, $sites);
    	
    	$form->bind($stock);
//    	$form->get('product_id')->setValue($stock->product_id);
    	$form->get('product_id')->setAttribute('disabled', 'disabled');
    	$form->get('submit')->setValue('Update');
    	
    	// Set the form filters and hydrate it with the data from the request
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setInputFilter($stock->getInputFilter());
    		$form->setData($request->getPost());
    
     		// Update the entity with the data from the valid form and update it in the database
    		if ($form->isValid()) {
    			$this->getStockTable()->save($stock, $current_user);

    			// Redirect to the index
    			return $this->redirect()->toRoute('stock');
    		}
    	}
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Stock',
       		'form' => $form,
    		'id' => $id
    	);
    }
/*
    public function localizeAction()
    {
    	// Check the presence of the id parameter for the entity to update
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('stock');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	 
    	// Retrieve the allowed routes
    	$allowedRoutes = Functions::getAllowedRoutes($this);

    	// Retrieve the user's instance
    	$instance_id = Functions::getInstanceId($this);
    	
    	// Create the entity object and initialize it from the database
    	$stock = $this->getStockTable()->get($id);

    	// Create the entity object and initialize it from the database
    	if ($stock->product_id) $product = $this->getProductTable()->get($stock->product_id);
    	else $product = null;

    	// Retrieve the existing localization and instanciate a new empty entity if not exists
    	$select = $this->getSiteStockTable()->getSelect()
    		->where(array('stock_id' => $stock->id));
    	$rowset = $this->getSiteStockTable()->selectWith($select);
    	$siteStock = $rowset->current();
    	if (!$siteStock) {
    	    $siteStock = new SiteStock();
    		$siteStock->id = NULL;
    		$siteStock->stock_id = $stock->id;
    	}
    	
    	// Create the form object and initialize it from the existing entity
    	$form  = new SiteStockForm();
    
    	// Retrieve and give the sites to the form
    	$select = $this->getSiteTable()->getSelect()
	    	->order(array('caption'));
    	$cursor = $this->getSiteTable()->selectWith($select);
    	$sites = array();
    	foreach ($cursor as $site) $sites[$site->id] = $site;
    	$form->addElements($sites);
    	$form->get('submit')->setValue('Update');

    	$form->bind($siteStock);
    	 
    	// Set the form filters and hydrate it with the data from the request
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setInputFilter($siteStock->getInputFilter());
    		$form->setData($request->getPost());
    
    		// Update the entity with the data from the valid form and update it in the database
    		if ($form->isValid()) {
    			$this->getSiteStockTable()->save($siteStock, $current);
    
    			// Redirect to the index
    			return $this->redirect()->toRoute('stock');
    		}
    	}
    	return array(
    		'current_user' => $current_user,
    		'allowedRoutes' => $allowedRoutes,
    		'title' => 'Stock',
    		'form' => $form,
    		'id' => $id,
    		'product' => $product,
    		'stock' => $stock
    	);
    }*/

    public function importAction()
    {
    	// Check the presence of the id parameter for the entity to import
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('stock');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);

    	
    	$select = $this->getSiteTable()->getSelect();
    	$cursor = $this->getSiteTable()->selectWith($select);
    	$sites = array();
    	foreach ($cursor as $site) $sites[$site->site_id] = $site;
    	
    	
    	// Retrieve the link and its parent folder
    	$link = $this->getLinkTable()->get($id);
    	$parent_id = $link->parent_id;
    
    	$file = 'data/documents/'.$link->id;
    	$validity = Functions::controlCsv(
    			$file, // Path to the file
    			array(	255, // Numéro de série de l'équipement
    					255, // Marque de l'équipement
    					255, // Modèle de l'équipement
    					255, // Libellé et référence des options installées sur l'équipement
    					'date', // Date de mise en exploitation de l'équipement
    					$sites, // Numéro généré par Pole Emploi
    					255, // Bâtiment où se trouve l'équipement
    					255, // Etage où se trouve l'équipement
    					255, // Emplacement de l'équipement
    			), // Type list
    			TRUE, // Ignore first row (column headers)
    			200); // Max number of rows
    			foreach ($validity as $ok => $content) { // content is a list of errors if not ok
    				// sort between duplicate and not duplicate rows according to the primary key last_name + first_name
    				$not_duplicate = array();
    				$duplicate = array();
    				if ($ok) {
    					foreach ($content as $row) {
    						$select = $this->getStockTable()->getSelect()->where(array('serial_number' => $row[0]));
    						$cursor = $this->getStockTable()->selectWith($select);
    						if (count($cursor) > 0) $duplicate[] = $row;
    						else $not_duplicate[] = $row;
    					}
    					$request = $this->getRequest();
    					if ($request->isPost()) {
    						$confirm = $request->getPost('confirm', $this->getServiceLocator()->get('translator')->translate('No'));
    
    						if ($confirm == $this->getServiceLocator()->get('translator')->translate('Import the data')) {
    
    							// Empty the temporary table
    							$this->getTmpStockTable()->multipleDelete(array("1" => "1"));
    
    							// Load the temporary table
    							$tmpStock = new TmpStock();
    							foreach ($not_duplicate as $row) {
    								$tmpStock->serial_number = $row[0];
    								$tmpStock->brand = $row[1];
    								$tmpStock->model = $row[2];
    								$tmpStock->liste_options = $row[3];
    								$tmpStock->exploitation_date = $row[4];
    							//	$tmpStock->site_id = $row[5];
    								$tmpStock->building = $row[6];
    								$tmpStock->floor = $row[7];
    								$tmpStock->place = $row[8];
    								$this->getTmpStockTable()->save($tmpStock, $current_user);
    							}
    							// Insert the stocks
    							$select = $this->getTmpStockTable()->getSelect();
    							$cursor = $this->getTmpStockTable()->selectWith($select);
    							$stock = new Stock();
    						//	$siteStock = new SiteStock();
    								
    							foreach ($cursor as $tmpStock) {
    								$stock->serial_number = $tmpStock->serial_number;
    								$stock->brand = $tmpStock->brand;
    								$stock->model = $tmpStock->model;
    								$stock->liste_options = $tmpStock->liste_options;
    								$stock->exploitation_date = $tmpStock->exploitation_date;
    								$stock->site_id = $row[5];
    								$stock->building = $tmpStock->building;
    								$stock->floor = $tmpStock->floor;
    								$stock->place = $tmpStock->place;
    								$stock_id = $this->getStockTable()->save($stock, $current_user);
/*
    								$siteStock->stock_id = $stock_id;
    								$siteStock->building = $tmpStock->building;
    								$siteStock->floor = $tmpStock->floor;
    								$siteStock->place = $tmpStock->place;
    								$this->getSiteStockTable()->save($siteStock, $current_user);*/
    							}
    						}
    						return $this->redirect()->toRoute('stock');
    					}
    
    					return array(
				    		'current_user' => $current_user,
				    		'title' => 'Stock',
    						'id'    => $id,
    						'ok' => $ok,
    						'not_duplicate' => $not_duplicate,
    						'duplicate' => $duplicate
    					);
    				}
    				else {
    					// Return the page
    					return new ViewModel(array(
				    		'current_user' => $current_user,
				    		'title' => 'Stock',
    						'ok' => $ok,
    						'errors' => $content
    					));
    				}
    			}
    }
    
    public function deleteAction()
    {
		// Check the presence of the id parameter for the entity to delete
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('stock');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	
    	// Retrieve the user validation from the post
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$del = $request->getPost('del', $this->getServiceLocator()->get('translator')->translate('No'));
    
			// And delete the entity from the database in the "yes" case
    		if ($del == $this->getServiceLocator()->get('translator')->translate('Yes')) {
    			$id = (int) $request->getPost('id');
    			$this->getStockTable()->delete($id);
    		}
    
    		// Redirect to the index
    		return $this->redirect()->toRoute('stock');
    	}
    
    	return array(
			'current_user' => $current_user,
			'title' => 'Stock',
    		'id' => $id,
    	);
    }

    public function getLinkTable()
    {
    	if (!$this->linkTable) {
    		$sm = $this->getServiceLocator();
    		$this->linkTable = $sm->get('CitCore\Model\LinkTable');
    	}
    	return $this->linkTable;
    }
    
    public function getProductTable()
    {
    	if (!$this->productTable) {
    		$sm = $this->getServiceLocator();
    		$this->productTable = $sm->get('CitMasterData\Model\ProductTable');
    	}
    	return $this->productTable;
    }

    public function getSiteTable()
    {
    	if (!$this->siteTable) {
    		$sm = $this->getServiceLocator();
    		$this->siteTable = $sm->get('CitOrder\Model\SiteTable');
    	}
    	return $this->siteTable;
    }
    
    public function getStockTable()
    {
    	if (!$this->stockTable) {
    		$sm = $this->getServiceLocator();
    		$this->stockTable = $sm->get('CitOrder\Model\StockTable');
    	}
    	return $this->stockTable;
    }
    
    public function getSiteStockTable()
    {
    	if (!$this->siteStockTable) {
    		$sm = $this->getServiceLocator();
    		$this->siteStockTable = $sm->get('CitOrder\Model\SiteStockTable');
    	}
    	return $this->siteStockTable;
    }

    public function getTmpStockTable()
    {
    	if (!$this->tmpStockTable) {
    		$sm = $this->getServiceLocator();
    		$this->tmpStockTable = $sm->get('CitOrder\Model\TmpStockTable');
    	}
    	return $this->tmpStockTable;
    }

    // Don't remove if using UserTable::retrieveHabilitations
    public $routes;
    protected $instanceTable;
    protected $userTable;
    protected $userPerimeterTable;
    protected $userRoleTable;
    protected $userRoleLinkerTable;
    protected $vcardTable;
    protected $vcardPropertyTable;
    
    public function getInstanceTable()
    {
    	if (!$this->instanceTable) {
    		$sm = $this->getServiceLocator();
    		$this->instanceTable = $sm->get('CitCore\Model\InstanceTable');
    	}
    	return $this->instanceTable;
    }
    
    public function getUserTable()
    {
    	if (!$this->userTable) {
    		$sm = $this->getServiceLocator();
    		$this->userTable = $sm->get('CitUser\Model\UserTable');
    	}
    	return $this->userTable;
    }
    
    public function getUserPerimeterTable()
    {
    	if (!$this->userPerimeterTable) {
    		$sm = $this->getServiceLocator();
    		$this->userPerimeterTable = $sm->get('CitUser\Model\UserPerimeterTable');
    	}
    	return $this->userPerimeterTable;
    }
    
    public function getUserRoleTable()
    {
    	if (!$this->userRoleTable) {
    		$sm = $this->getServiceLocator();
    		$this->userRoleTable = $sm->get('CitUser\Model\UserRoleTable');
    	}
    	return $this->userRoleTable;
    }
    
    public function getUserRoleLinkerTable()
    {
    	if (!$this->userRoleLinkerTable) {
    		$sm = $this->getServiceLocator();
    		$this->userRoleLinkerTable = $sm->get('CitUser\Model\UserRoleLinkerTable');
    	}
    	return $this->userRoleLinkerTable;
    }
    
    public function getVcardTable()
    {
    	if (!$this->vcardTable) {
    		$sm = $this->getServiceLocator();
    		$this->vcardTable = $sm->get('CitContact\Model\VcardTable');
    	}
    	return $this->vcardTable;
    }
    
    public function getVcardPropertyTable()
    {
    	if (!$this->vcardPropertyTable) {
    		$sm = $this->getServiceLocator();
    		$this->vcardPropertyTable = $sm->get('CitContact\Model\VcardPropertyTable');
    	}
    	return $this->vcardPropertyTable;
    }
}
