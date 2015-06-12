<?php
namespace CitOrder\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use CitCore\Controller\Functions;
use CitOrder\Model\Order;
use CitOrder\Model\OrderProduct;
use CitOrder\Model\Product;
use CitOrder\Form\OrderProductAddForm;
use CitOrder\Form\OrderProductIndexForm;
use CitOrder\Form\OrderProductUpdateForm;
use Zend\Session\Container;
use Zend\Http\Client;
use Zend\Http\Request;

class OrderProductController extends AbstractActionController
{
	protected $orderTable;
	protected $orderProductTable;
	protected $orderProductOptionTable;
	protected $productTable;
	protected $siteTable;
   	
   	public function indexAction()
    {
        // Check the presence of the id parameter (order)
    	$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
    		return $this->redirect()->toRoute('order');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	
    	// Retrieve the order
    	$order = $this->getOrderTable()->get($id);

    	// Retrieve the responsible
/*    	$responsible = $this->getUserTable()->get($order->responsible_id);
    	$contact = $this->getVcardTable()->get($responsible->contact_id);
    	$responsible->n_fn = $contact->n_fn;*/
    	 
    	// Prepare the SQL request
    	$major = $this->params()->fromQuery('major', NULL);
    	if (!$major) $major = 'caption';
    	$dir = $this->params()->fromQuery('dir', NULL);
    	if (!$dir) $dir = 'ASC';
    	$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    	$select = $this->getOrderProductTable()->getSelect()
    		->join('md_product', 'order_product.product_id = md_product.id', array('caption', 'brand', 'model'), 'left')
    		->join('contact_vcard', 'order_product.contact_id = contact_vcard.id', array('n_fn'), 'left')
    		->where(array('order_id' => $id))
    		->order(array($major.' '.$dir, 'caption', 'id'));
    	$cursor = $this->getOrderProductTable()->selectWith($select);

    	// Option price per product : 1) Create an working array with the order products
    	$orderProducts = array();
    	foreach ($cursor as $orderProduct) {
    		$orderProduct->option_price = 0;
    		$orderProducts[$orderProduct->id] = $orderProduct;
    	}
    	// Option price per product : 2) Select all the options for this order and store in the working array the sum per product
    	$select = $this->getOrderProductOptionTable()->getSelect()
	    	->join('order_product', 'order_product_option.order_product_id = order_product.id', array('order_id'), 'left')
    		->where(array('order_id' => $id));
    	$cursor = $this->getOrderProductOptionTable()->selectWith($select);
    	foreach ($cursor as $option) {
    		$orderProducts[$option->order_product_id]->option_price += $option->price;
    	}
    	 
    	// Create the form object and initialize it from the existing entity
   		$select = $this->getVcardTable()->getSelect();
   		$select->order(array('N_LAST', 'N_FIRST'));
   		$cursor = $this->getVcardTable()->selectWith($select);
   		$contacts = array();
   		foreach ($cursor as $vcard ) {
   			$contacts[$vcard->id] = $vcard->n_last." ".$vcard->n_first ;		
   		}

    	$form = new OrderProductIndexForm();
    	$form->addElements($orderProducts, $contacts);
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setData($request->getPost());
    			 
    			// Check the input values
/*    			if ($form->get('hoped_delivery_date')->getValue() < $order->order_date) {
    				$form->get('hoped_delivery_date')
    					->setMessages(array(array('hoped_delivery_date' =>
    						$this->getServiceLocator()->get('translator')->translate('The hoped delivery date should not be earlier than the order date'))));
    			}
    			else {*/
    				
    				// Update the selected rows
    				foreach ($orderProducts as $orderProduct) {
    					
    					if ($form->get('product'.$orderProduct->id)->getValue()) { // Row selected

    						// Contact
    						$action = $request->getPost('update_contact', 'Cancel');
    						if ($action == $this->getServiceLocator()->get('translator')->translate('Set contact')) {
	    						$orderProduct->contact_id = $form->get('contact_id')->getValue();
	    						$this->getOrderProductTable()->save($orderProduct, $current_user);
    						}
    					
    						// Hoped delivery date
    						$action = $request->getPost('update_hoped_delivery_date', 'Cancel');
        					if ($action == $this->getServiceLocator()->get('translator')->translate('Set hoped delivery date')) {
	    						$orderProduct->hoped_delivery_date = $form->get('hoped_delivery_date')->getValue();
	    						$this->getOrderProductTable()->save($orderProduct, $current_user);
        					}
    					    					
    						// Destination
    						$action = $request->getPost('update_destination', 'Cancel');
	    					if ($action == $this->getServiceLocator()->get('translator')->translate('Set destination')) {
	    						$orderProduct->building = $form->get('building')->getValue();
	    						$orderProduct->floor = $form->get('floor')->getValue();
	    						$orderProduct->department = $form->get('department')->getValue();
	    						$orderProduct->comment = $form->get('comment')->getValue();
	    						$this->getOrderProductTable()->save($orderProduct, $current_user);
	    					}
	    					
    					}
   					}
    	
    				// Redirect to the index
    				return $this->redirect()->toRoute('orderProduct/index', array('id' => $id));
//    		}
    	}
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Order',
    		'current_role' => $current_user->role_id,
    		'major' => $major,
    		'dir' => $dir,
//    		'responsible' => $responsible,
    		'order' => $order,
    		'orderProducts' => $orderProducts,
    		'form' => $form
        );
    }

    public function addAction()
    {
        // Check the presence of the id parameter (order)
    	$id = (int) $this->params()->fromRoute('id', 0);
		if (!$id) {
    		return $this->redirect()->toRoute('order');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	
    	// Retrieve the order
    	$order = $this->getOrderTable()->get($id);
    	 
    	// Retrieve the products
    	$select = $this->getProductTable()->getSelect()
    		->where(array('is_on_sale' => true))
    		->order(array('caption'));
    	$cursor = $this->getProductTable()->selectWith($select);
    	$products = array();
    	foreach ($cursor as $product) $products[] = $product;
	
    	// Create the form object
    	$form = new OrderProductAddForm();
    	$form->addElements($products);    	 
    	$form->get('submit')->setValue('Add');

    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$orderProduct = new OrderProduct();
    		$form->setInputFilter($orderProduct->getInputFilter());
     		$form->setData($request->getPost());

     		// Update the entity with the data from the valid form and create it into the database
    		if ($form->isValid()) {
    			$orderProduct->exchangeArray($form->getData());
        		$orderProduct->id = NULL;
        		$orderProduct->order_id = $id;
        		$orderProduct->hoped_delivery_date = $order->initial_hoped_delivery_date;

        		// Retrieve the current product price
        		$product = $this->getProductTable()->get($orderProduct->product_id);
        		$orderProduct->price = $product->price;

				// Generates the specified quantity of order rows
        		for ($i = 0; $i < $form->get('quantity')->getValue(); $i++) {
        			$this->getOrderProductTable()->save($orderProduct, $current_user);
        		}

        		// Update the order status
        		$order->status = 'Nouvelle';
        		$this->getOrderTable()->save($order, $current_user);
        		
    			// Redirect to the index
    			return $this->redirect()->toRoute('orderProduct/index', array('id' => $id));
    		}
    	}
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Order',
    		'form' => $form,
    		'order' => $order,
    		'id' => $id,
    		'products' => $products
    	);
    }

    public function updateAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('order');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	    
    	// Retrieve the order row
    	$orderProduct = $this->getOrderProductTable()->get($id);
    	$product = $this->getProductTable()->get($orderProduct->product_id);
    	$orderProduct->caption = $product->caption;

    	// Retrieve the order
    	$order = $this->getOrderTable()->get($orderProduct->order_id);
    	 
    	// Retrieve the site
    	$site = $this->getSiteTable()->get($order->site_id);

  		// Create the form object and initialize it from the existing entity
       	$select = $this->getVcardTable()->getSelect();
   		$select->order(array('N_LAST', 'N_FIRST'));
   		$cursor = $this->getVcardTable()->selectWith($select);
   		$contacts = array();
   		foreach ($cursor as $vcard ) {
   			$contacts[$vcard->id] = $vcard->n_last." ".$vcard->n_first ;		
   		}
   		$form = new OrderProductUpdateForm();
    	$form->addElements($contacts);
    	$form->bind($orderProduct);
    	$form->get('submit')->setValue('Update');
    	 
    	// Set the form filters and hydrate it with the data from the request
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setInputFilter($orderProduct->getInputFilter());
    		$form->setData($request->getPost());
    
    		// Update the entity with the data from the valid form and update it in the database
    		if ($form->isValid()) {
    			// Check the input values
    			if ($orderProduct->hoped_delivery_date < $order->order_date) {
    				$form->get('hoped_delivery_date')
    					->setMessages(array(array('hoped_delivery_date' =>
    						$this->getServiceLocator()->get('translator')->translate('The hoped delivery date should not be earlier than the order date'))));
    			}
    			else {
    				$this->getOrderProductTable()->save($orderProduct, $current_user);
    
    				// Redirect to the index
    				return $this->redirect()->toRoute('orderProduct/index', array('id' => $order->id));
    			}
    		}
    	}
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Order',
    		'form' => $form,
    		'id' => $id,
    		'order' => $order,
    		'orderProduct' => $orderProduct,
    		'site' => $site,
    	);
    }

    public function deleteAction()
    {
		// Check the presence of the id parameter for the entity to delete
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('order');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	
    	// Retrieve the order product row
    	$orderProduct = $this->getOrderProductTable()->get($id);
    	 
    	// Retrieve the order
    	$order = $this->getOrderTable()->get($orderProduct->order_id);

    	// Retrieve the product
    	$product = $this->getProductTable()->get($orderProduct->product_id);
    	 
    	// Retrieve the user validation from the post
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$del = $request->getPost('del', 'No');
    
			// And delete the entity from the database in the "yes" case
    		if ($del == $this->getServiceLocator()->get('translator')->translate('Yes')) {
    			$id = (int) $request->getPost('id');
    			$this->getOrderProductTable()->delete($id);
    		}
    
    		// Redirect to the index
    		return $this->redirect()->toRoute('orderProduct/index', array('id' => $order->id));
    	}
    
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Order',
    		'id' => $id,
    		'order' => $order,
    		'product' => $product
    	);
    }

    public function getOrderTable()
    {
    	if (!$this->orderTable) {
    		$sm = $this->getServiceLocator();
    		$this->orderTable = $sm->get('CitOrder\Model\OrderTable');
    	}
    	return $this->orderTable;
    }

    public function getOrderProductTable()
    {
    	if (!$this->orderProductTable) {
    		$sm = $this->getServiceLocator();
    		$this->orderProductTable = $sm->get('CitOrder\Model\OrderProductTable');
    	}
    	return $this->orderProductTable;
    }

    public function getOrderProductOptionTable()
    {
    	if (!$this->orderProductOptionTable) {
    		$sm = $this->getServiceLocator();
    		$this->orderProductOptionTable = $sm->get('CitOrder\Model\OrderProductOptionTable');
    	}
    	return $this->orderProductOptionTable;
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
