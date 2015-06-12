<?php
namespace CitOrder\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use CitCore\Controller\Functions;
use CitOrder\Model\Order;
use CitOrder\Model\OrderWithdrawal;
use CitOrder\Form\OrderWithdrawalIndexForm;
use Zend\Session\Container;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Memory\Value;

class OrderWithdrawalController extends AbstractActionController
{
	protected $orderTable;
	protected $orderWithdrawalTable;
	protected $siteTable;
	protected $siteStockTable;
	protected $stockTable;

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
    	$select = $this->getStockTable()->getSelect()
    		->join('order_withdrawal', 'order_stock.id = order_withdrawal.stock_id', array('order_id'), 'left')
    		->join('order', 'order_withdrawal.order_id = order.id', array('identifier'), 'left')
    		->join('order_site', 'order_stock.site_id = order_site.id', array('site_caption' => 'caption'), 'left')
    		->where(array('order_site.id' => $order->site_id))
    		->order(array($major.' '.$dir, 'caption', 'id'));
    	$cursor = $this->getStockTable()->selectWith($select);
    	$stocks = array();
    	foreach ($cursor as $stock) $stocks[] = $stock;

    	$form = new OrderWithdrawalIndexForm();
    	$form->addElements($stocks, $id);
    	
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setData($request->getPost());
    		
    		// Delete the existing rows
    		$this->getOrderWithdrawalTable()->multipleDelete(array('order_id' => $id));
    	
			foreach($stocks as $stock) {

				if (!$stock->order_id || $stock->order_id == $id) {
					if ($form->get('stock'.$stock->id)->getValue() == 1) {

			    		// Add the withdrawal
			    		$orderWithdrawal = new OrderWithdrawal();
			    		$orderWithdrawal->id = NULL;
			    		$orderWithdrawal->order_id = $id;    	
			    		$orderWithdrawal->stock_id = $stock->id;
			    		$this->getOrderWithdrawalTable()->save($orderWithdrawal, $current_user);
					}
				}
			}
			// Redirect to the index
    		return $this->redirect()->toRoute('orderWithdrawal/index', array('id' => $id));
    	}
    	 
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Order',
    		'form' => $form,
    		'major' => $major,
    		'dir' => $dir,
//    		'responsible' => $responsible,
    		'order' => $order,
    		'stocks' => $stocks,
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

    public function getOrderWithdrawalTable()
    {
    	if (!$this->orderWithdrawalTable) {
    		$sm = $this->getServiceLocator();
    		$this->orderWithdrawalTable = $sm->get('CitOrder\Model\OrderWithdrawalTable');
    	}
    	return $this->orderWithdrawalTable;
    }

    public function getSiteTable()
    {
    	if (!$this->siteTable) {
    		$sm = $this->getServiceLocator();
    		$this->siteTable = $sm->get('CitOrder\Model\SiteTable');
    	}
    	return $this->siteTable;
    }
    
    public function getSiteStockTable()
    {
    	if (!$this->siteStockTable) {
    		$sm = $this->getServiceLocator();
    		$this->siteStockTable = $sm->get('CitOrder\Model\SiteStockTable');
    	}
    	return $this->siteStockTable;
    }

    public function getStockTable()
    {
    	if (!$this->stockTable) {
    		$sm = $this->getServiceLocator();
    		$this->stockTable = $sm->get('CitOrder\Model\StockTable');
    	}
    	return $this->stockTable;
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
    