<?php
namespace CitOrder\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use CitCore\Controller\Functions;
use CitOrder\Model\OrderProductOption;
use CitOrder\Form\OrderProductOptionForm;
use Zend\Session\Container;
use Zend\Http\Client;
use Zend\Http\Request;

class OrderProductOptionController extends AbstractActionController
{
	protected $productTable;
	protected $productOptionTable;
	protected $productOptionMatrixTable;
	protected $orderTable;
	protected $orderProductTable;
	protected $orderProductOptionTable;
	protected $siteTable;
	
	public function indexAction()
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

		// Retrieve the order
		$order = $this->getOrderTable()->get($orderProduct->order_id);

		// Retrieve the option matrix
		$select = $this->getProductOptionMatrixTable()->getSelect()
			->where(array('product_id' => $orderProduct->product_id));
		$cursor = $this->getProductOptionMatrixTable()->selectWith($select);
		$matrix = array();
		foreach($cursor as $cell) $matrix[] = $cell;
		
		// Initialize the form
		$product = $this->getProductTable()->get($orderProduct->product_id);
		$select = $this->getProductOptionTable()->getSelect()->where(array('product_id' => $product->id))
			->where(array('is_on_sale' => true))
			->order(array('caption'));
		$cursor = $this->getProductOptionTable()->selectWith($select);
		$options = array();
		foreach($cursor as $option) $options[$option->id] = $option;		
		$form = new OrderProductOptionForm();
		$form->addElements($options);

		// Retrieve the existing options and update the form
		$select = $this->getOrderProductOptionTable()->getSelect()->where(array('order_product_id' => $orderProduct->id));
		$cursor = $this->getOrderProductOptionTable()->selectWith($select);
		$sum = 0;
		foreach ($cursor as $orderOption) {
			$form->get('option'.$orderOption->product_option_id)->setValue(1);
			$sum += $orderOption->price;
		}
		
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$orderOption = new OrderProductOption();
			$form->setInputFilter($order->getInputFilter());
			$form->setData($request->getPost());
		
			// Update the entity with the data from the valid form and create it into the database
			if ($form->isValid()) {
				$this->getOrderProductOptionTable()->multipleDelete(array('order_product_id' => $orderProduct->id));
				$orderOption->exchangeArray($form->getData());
				$orderOption->id = NULL;
				$orderOption->order_product_id = $id;
				foreach ($options as $option) {
					if ($form->get('option'.$option->id)->getValue()) {
						$orderOption->product_option_id = $option->id;
						$orderOption->price = $option->price;
						$this->getOrderProductOptionTable()->save($orderOption, $current_user);
					}
				}				 
				// Redirect to the index
				return $this->redirect()->toRoute('orderProduct/index', array('id' => $order->id));
			}
		}
		return array(
    		'current_user' => $current_user,
    		'title' => 'Order',
    		'current_role' => $current_user->role_id,
			'order' => $order,
			'product' => $product,
			'options' => $options,
			'form' => $form,
			'id' => $id,
			'sum' => $sum,
			'matrix' => $matrix
		);
	}

	public function getProductTable()
	{
		if (!$this->productTable) {
			$sm = $this->getServiceLocator();
			$this->productTable = $sm->get('CitMasterData\Model\ProductTable');
		}
		return $this->productTable;
	}
	
    public function getProductOptionTable()
    {
    	if (!$this->productOptionTable) {
    		$sm = $this->getServiceLocator();
    		$this->productOptionTable = $sm->get('CitMasterData\Model\ProductOptionTable');
    	}
    	return $this->productOptionTable;
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

    public function getProductOptionMatrixTable()
    {
    	if (!$this->productOptionMatrixTable) {
    		$sm = $this->getServiceLocator();
    		$this->productOptionMatrixTable = $sm->get('CitMasterData\Model\ProductOptionMatrixTable');
    	}
    	return $this->productOptionMatrixTable;
    }
    
    public function getSiteTable()
    {
    	if (!$this->siteTable) {
    		$sm = $this->getServiceLocator();
    		$this->siteTable = $sm->get('CitCommande\Model\SiteTable');
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
   