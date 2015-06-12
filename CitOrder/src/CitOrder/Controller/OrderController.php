<?php
namespace CitOrder\Controller;

use DateInterval;
use Date;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use CitCore\Controller\Functions;
use CitDeployment\Model\Deployment;
use CitDeployment\Model\DeploymentProduct;
use CitDeployment\Model\DeploymentWithdrawal;
use CitOrder\Model\SiteContact;
use CitOrder\Model\SiteStock;
use CitOrder\Model\Stock;
use CitOrder\Model\Order;
use CitOrder\Model\OrderProduct;
use CitOrder\Model\OrderProductOption;
use CitOrder\Model\OrderWithdrawal;
use CitOrder\Form\OrderForm;
use CitOrder\Form\OrderRetractionForm;
use CitOrder\Form\OrderUpdateForm;
use CitOrder\Form\OrderValidationForm;
use CitOrder\Form\OrderValidationRequestForm;
use CitOrder\Form\SiteStockForm;
use DOMPDFModule\View\Model\PdfModel;
use Zend\Session\Container;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\db\sql\Where;

class OrderController extends AbstractActionController
{
	protected $deploymentTable;
   	protected $deploymentProductTable;
	protected $deploymentWithdrawalTable;
	protected $linkTable;
	protected $orderTable;
   	protected $orderProductTable;
   	protected $orderProductOptionTable;
   	protected $orderWithdrawalTable;
   	protected $productTable;
   	protected $productOptionTable;
   	protected $productOptionMatrixTable;
   	protected $regionTable;
   	protected $siteTable;
   	protected $siteContactTable;
   	protected $stockTable;
   	
   	public function indexAction()
    {
    	// Retrieve the current user and its habilitations
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	
    	// Prepare the SQL request
    	$currentPage = $this->params()->fromQuery('page', 1);
    	$major = $this->params()->fromQuery('major', NULL);
    	if (!$major) $major = 'identifier';
    	$dir = $this->params()->fromQuery('dir', NULL);
    	if (!$dir) $dir = 'ASC';
    	$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    	$select = $this->getOrderTable()->getSelect()
    		->join('order_site', 'order.site_id = order_site.id', array('site_caption' => 'caption'), 'left')
//    		->join('user', 'order.responsible_id = user.user_id', array('delegatee_id', 'delegation_begin', 'delegation_end'), 'left')
    		->order(array($major.' '.$dir, 'identifier'));

    	// Defines the perimeter according to the current user role
    	$allowedSites = array();
    	foreach ($current_user->perimeters as $perimeter) {
    		if ($perimeter->table == 'order_site') $allowedSites[] = $perimeter->value;
    	}
    	
       	$where = new Where();
		if (count($allowedSites) > 0) {
	    	$where->in('site_id', $allowedSites);
       	}

       	$select->where($where);
    	
    	$paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\DbSelect($select, $adapter));
    	$paginator->setCurrentPageNumber($currentPage);
    	$paginator->setDefaultItemCountPerPage(30);

    	// Return the link list
    	return new ViewModel(array(
    		'current_user' => $current_user,
    		'title' => 'Order',
    		'major' => $major,
    		'dir' => $dir,
    		'orders' => $paginator
    	));
    }

    protected function setHeader($order, $current_user)
    {
    	// Retrieve the responsible
/*    	if ($order->responsible_id) {
	    	$responsible = $this->getUserTable()->get($order->responsible_id);
	    	$contact = $this->getVcardTable()->get($responsible->contact_id);
	    	$responsible->n_fn = $contact->n_fn;
    	}
	    else $responsible = null;*/
	    	
    	// Retrieve the approver
/*    	if ($order->approver_id) {
    		$approver = $this->getUserTable()->get($order->approver_id);
    		$contact = $this->getVcardTable()->get($approver->contact_id);
    		$approver->n_fn = $contact->n_fn;
    	}
    	else $approver = null;*/
    
    	// Retrieve the order site
    	$site = $this->getSiteTable()->get($order->site_id);

    	// Retrieve the VAT rate depending on the region
    	$region = $this->getRegionTable()->get($site->region_id);
    	$vat_rate = $region->vat_rate;
    	 
    	// Retrieve the order products
    	$select = $this->getOrderProductTable()->getSelect()
    	->where(array('order_id' => $order->id));
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
    	->where(array('order_id' => $order->id));
    	$cursor = $this->getOrderProductOptionTable()->selectWith($select);
    	foreach ($cursor as $option) {
    		$orderProducts[$option->order_product_id]->option_price += $option->price;
    	}
    	// Compute the sum
    	$sum = 0; $netSum = 0;
    	foreach ($orderProducts as $orderProduct) {
    		$sum += $orderProduct->price;
    		$net = $orderProduct->price + $orderProduct->option_price;
    		$netSum += $net;
    	}
    	$vat = round($netSum * $vat_rate, 2);
    	$including_vat = $netSum + $vat;
    	 
    	// Prepare the form header
    	return array(
//    			'responsible_n_fn' => array('label' => 'Responsible', 'value' => ($responsible) ? $responsible->n_fn : null),
//    			'approver_n_fn' => array('label' => 'Approver', 'value' => ($approver) ? $approver->n_fn : null),
    			'site_caption' => array('label' => 'Site', 'value' => $site->caption),
    			'status' => array('label' => 'Status', 'value' => $order->status),
    			'identifier' => array('label' => 'Identifier', 'value' => $order->identifier.'-'.sprintf('%1$06d', $order->id)),
    			'accounting_identifier' => array('label' => 'Accounting identifier', 'value' => $order->accounting_identifier),
    			'caption' => array('label' => 'Caption', 'value' => $order->caption),
    			'order_date' => array('label' => 'Order date', 'value' => Functions::formatDate($order->order_date, $current_user)),
    			'retraction_limit' => array('label' => 'Retraction limit', 'value' => Functions::formatDate($order->retraction_limit, $current_user)),
    			'product_count' => array('label' => 'Products', 'value' => count($orderProducts)),
    			'sum_without_option' => array('label' => 'Sum without options', 'value' => number_format($sum,2,',',' ').' €'),
    			'sum_with_options' => array('label' => 'Sum with options', 'value' => number_format($netSum,2,',',' ').' €'),
    			'vat' => array('label' => 'VAT', 'value' => number_format($vat,2,',',' ').' €'),
    			'sum_including_vat' => array('label' => 'Including VAT sum', 'value' => number_format($including_vat,2,',',' ').' €'),
    			'comment' => array('label' => 'Comment', 'value' => $order->comment),
    	);
    }
    
    public function detailAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('order');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    
    	// Retrieve the order
    	$order = $this->getOrderTable()->get($id);
    
    	return array(
    			'current_user' => $current_user,
    			'title' => 'Order',
    			'header' => $this->setHeader($order, $current_user),
    			'order' => $order,
    	);
    }

    public function addAction()
    {
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	    	
    	// Retrieve the approver list
/*    	$select = $this->getUserRoleLinkerTable()->getSelect()  
    		->join('user', 'user_role_linker.user_id = user.user_id', array(), 'left')
    		->join('contact_vcard', 'user.contact_id = contact_vcard.id', array('n_fn'), 'left')
    		->where(array('role_id' => 'valideur') );
     	$cursor = $this->getUserRoleLinkerTable()->selectWith($select);
    	$userRoles = array();
    	foreach ($cursor as $userRole) {
    		$userRoles[$userRole->user_id] = $userRole->n_fn;
    	}*/
    	// Retrieve the site list on which the current user is a contact
/*    	$select = $this->getSiteContactTable()->getSelect()
    		->join('order_site', 'order_site_contact.site_id = order_site.id', array('caption', 'nb_people', 'surface', 'nb_floors'), 'left')
    		->where(array('order_site_contact.contact_id = '.$current_user->contact_id))
    		->order(array('caption'));
    	$cursor = $this->getSiteContactTable()->selectWith($select);
    	$sites = array();
    	foreach ($cursor as $site) $sites[] = $site;*/

    	// Retrieve the site perimeters of the current user
    	$sites = array();
    	foreach ($current_user->perimeters as $perimeter) {
    		if ($perimeter->table == 'order_site') {
    			$sites[] = $this->getSiteTable()->get($perimeter->value);
    		}
    	}
    	
    	// Create the form object
    	$form = new OrderForm();
    	$form->addElements(/*$userRoles, */$sites);
    	$form->get('submit')->setValue('Create');

    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$order = new Order();
    		$form->setInputFilter($order->getInputFilter());
     		$form->setData($request->getPost());

     		// Update the entity with the data from the valid form and create it into the database
    		if ($form->isValid()) {
	    		$order->exchangeArray($form->getData());
				if ($order->initial_hoped_delivery_date < $order->order_date) {
	    			$form->get('initial_hoped_delivery_date')
	    				->setMessages(array(array('initial_hoped_delivery_date' =>
	    					$this->getServiceLocator()->get('translator')->translate('The hoped delivery date should not be earlier than the order date'))));
    			}
	    		else {
	    			$order->id = NULL;
	        		$order->identifier = 'C'.date('Y');
//	        		$order->responsible_id = $current_user->user_id;
	        		$order->status = 'A générer';
	        		$order->current_hoped_delivery_date = $order->initial_hoped_delivery_date;
	        		$id = $this->getOrderTable()->save($order, $current_user);
	    
	    			// Redirect to the index
	    			return $this->redirect()->toRoute('orderProduct/index', array('id' => $id));
    			}
    		}
    	}
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Order',
    		'form' => $form,
    		'sites' => $sites,
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
    	
    	// Retrieve the order
    	$order = $this->getOrderTable()->get($id);

    	// Retrieve the responsible
/*    	$responsible = $this->getUserTable()->get($order->responsible_id);
    	$contact = $this->getVcardTable()->get($responsible->contact_id);
    	$responsible->n_fn = $contact->n_fn;*/
    	 
    	// Retrieve the approver list
/*    	$select = $this->getUserRoleLinkerTable()->getSelect()  
    		->join('user', 'user_role_linker.user_id = user.user_id', array(), 'left')
    		->join('contact_vcard', 'user.contact_id = contact_vcard.id', array('n_fn'), 'left')
    		->where(array('role_id' => 'valideur') );
     	$cursor = $this->getUserRoleLinkerTable()->selectWith($select);
    	$userRoles = array();
    	foreach ($cursor as $userRole) {
    		$userRoles[$userRole->user_id] = $userRole->n_fn;
    	}*/
		// Retrieve the order site
		$site = $this->getSiteTable()->get($order->site_id);
    	$siteContact = new SiteContact();
		$siteContact->site_id = $site->id;
		$siteContact->caption = $site->caption;
		$sites[] = $siteContact;
		
    	// Create the form object and initialize it from the existing entity
		$form = new OrderUpdateForm();
    	$form->bind($order);
    	$form->get('submit')->setValue('Update');
//    	$form->get('site_id')->setAttribute('Disabled', 'Disabled');
    	
    	// Set the form filters and hydrate it with the data from the request
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setInputFilter($order->getInputFilter());
    		$form->setData($request->getPost());
    
     		// Update the entity with the data from the valid form and update it in the database
    		if ($form->isValid()) {
			if ($order->initial_hoped_delivery_date < $order->order_date) {
	    			$form->get('initial_hoped_delivery_date')
	    				->setMessages(array(array('initial_hoped_delivery_date' =>
	    					$this->getServiceLocator()->get('translator')->translate('The hoped delivery date should not be earlier than the order date'))));
    			}
	    		else {
	        		$order->current_hoped_delivery_date = $order->initial_hoped_delivery_date;
	    			$this->getOrderTable()->save($order, $current_user);

    				// Redirect to the index
    				return $this->redirect()->toRoute('order');
	    		}
    		}
    	}
    	$header = array(
//    		array('label' => 'Responsible', 'value' => $responsible->n_fn),
    		array('label' => 'Order number', 'value' => $order->identifier.'-'.sprintf('%1$06d', $order->id)),
    		array('label' => 'Site', 'value' => $site->caption)
    	);
    	
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Order',
    		'form' => $form,
    		'id' => $id,
    		'order' => $order,
    		'header' => $header
    	);
    }

    public function validationRequestAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('order');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    
    	// Retrieve the order
    	$order = $this->getOrderTable()->get($id);    

    	// Retrieve the approver list
/*    	$select = $this->getUserRoleLinkerTable()->getSelect()
    		->join('user', 'user_role_linker.user_id = user.user_id', array(), 'left')
    		->join('contact_vcard', 'user.contact_id = contact_vcard.id', array('n_fn'), 'left')
    		->where(array('role_id' => 'valideur') );
    	$cursor = $this->getUserRoleLinkerTable()->selectWith($select);
    	$approvers = array();
    	foreach ($cursor as $approver) {
    		$approvers[$approver->user_id] = $approver->n_fn;
    	}*/
    	$header = $this->setHeader($order, $current_user);
    	 
    	// Create the form object and initialize it from the existing entity
    	$form = new OrderValidationRequestForm();
//    	$form->addElements($approvers);
    	$form->bind($order);
//    	$form->get('retraction_limit')->setValue(date('Y-m-d'));
    	 
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$form->setInputFilter($order->getInputFilter());
    		$form->setData($request->getPost());
    
    		if ($form->isValid()) {
    			
    			// Update the entity with the data from the valid form and update it in the database
    			$order->status = 'A valider';
    			if ($order->comment) $order->comment .= PHP_EOL.PHP_EOL;
    			$order->comment .= Date('d/m/Y').' ('.$current_user->n_fn.') :'.PHP_EOL.$form->get('new_comment')->getValue();
    			$this->getOrderTable()->save($order, $current_user);
    			 
    			// Notify the approvers
/*    			$approver = $this->getUserTable()->get($order->approver_id);
    			$select = $this->getVcardPropertyTable()->getSelect()
    				->where(array('vcard_id' => $approver->contact_id, 'name' => 'EMAIL'));
    			$approver_email = $this->getVcardPropertyTable()->selectWith($select)->current();*/
    			$config = $this->getServiceLocator()->get('config');
    			$settings = $config['citCoreSettings'];
    			$toList = Functions::getToList('valideur', 'order_site', 'site_id', $order->site_id, $this->getUserTable());
    			foreach ($toList as $addressee) {
	    			Functions::envoiMail(
	    					$this->getServiceLocator(),
	    					$addressee->username,
	    					'Votre validation est demandée pour la commande : '.
	    					$order->identifier.'-'.sprintf('%1$06d', $order->id).
	    					' accessible à ce lien : '.
	    					$settings['domainName'].'order/validation-request/'.$order->id,
	    					'Equipements d\'impression : Demande de validation de commande',
	    					NULL, NULL);
    			}
    			 
    			// Redirect to the index
    			return $this->redirect()->toRoute('order');
    		}
    	}
    	return array(
    			'current_user' => $current_user,
    			'title' => 'Order',
    			'header' => $header,
    			'form' => $form,
    			'id' => $id,
    			'order' => $order,
    	);
    }
    
    protected function deployOrder($order, $user) {

    	// Load the deployment entity
    	$deployment = new Deployment();
    	$deployment->order_id = $order->id;
//    	$deployment->responsible_id = $order->responsible_id;
//    	$deployment->approver_id = $order->approver_id;
    	$deployment->site_id = $order->site_id;
    	$deployment->order_date = $order->order_date;
    	$deployment->identifier = $order->identifier.'-'.sprintf('%1$06d', $order->id);
    	$deployment->caption = $order->caption;
    	$deployment->retraction_limit = $order->retraction_limit;
    	$deployment->current_hoped_delivery_date = $order->initial_hoped_delivery_date;
		$deployment->status = 'A compléter';
		$deployment_id = $this->getDeploymentTable()->save($deployment, $user);
		
		// Load the products
		$select = $this->getOrderProductTable()->getSelect()
			->where(array('order_id' => $order->id));
		$cursor = $this->getOrderProductTable()->selectWith($select);
		$deploymentProduct = new DeploymentProduct();
		foreach($cursor as $orderProduct) {
			$deploymentProduct->deployment_id = $deployment_id;
			$deploymentProduct->order_product_id = $orderProduct->id;
			$deploymentProduct->status = 'A compléter';
			
			// Concatenate option captions
			$select = $this->getOrderProductOptionTable()->getSelect()
				->where(array('order_product_id' => $orderProduct->id));
			$cursor = $this->getOrderProductOptionTable()->selectWith($select);
			$options = ''; $first = true;
			foreach($cursor as $orderProductOption) {
				$productOption = $this->getProductOptionTable()->get($orderProductOption->product_option_id);
				if (!$first) $options .= ', ';
				$first = null;
				$options .= $productOption->caption;
			}
			$deploymentProduct->options = $options;
			$this->getDeploymentProductTable()->save($deploymentProduct, $user);
		}
    		
		// Load the withdrawals
		$select = $this->getOrderWithdrawalTable()->getSelect()
			->where(array('order_id' => $order->id));
		$cursor = $this->getOrderWithdrawalTable()->selectWith($select);
		$deploymentWithdrawal = new DeploymentWithdrawal();
		foreach($cursor as $orderWithdrawal) {
			$deploymentWithdrawal->deployment_id = $deployment_id;
			$deploymentWithdrawal->order_withdrawal_id = $orderWithdrawal->id;
			$this->getDeploymentWithdrawalTable()->save($deploymentWithdrawal, $user);
		}
    }

    public function validateAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('order');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    
    	// Retrieve the order
    	$order = $this->getOrderTable()->get($id);

    	$header = $this->setHeader($order, $current_user);

       	// Create the form object and initialize it from the existing entity
    	$form = new OrderValidationForm();
    	$form->bind($order);

    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$validate = $request->getPost('validate', 'No');
       		$form->setData($request->getPost());
    
    		if ($form->isValid()) {

				$valid = true;
				
				// Validation case
    			if ($validate != 'No') {
    				$order->status = 'Validée';
    				$text = 'validée';
       				$order->issue_date = Date('Y-m-d');
       				
       				// Copy to a new deployment entity tree
       				$this->deployOrder($order, $current_user);
    			}
    			else {
    				// Comment required on reject
    				if (!$form->get('new_comment')->getValue()) {
			    		$form->get('new_comment')->setMessages(array(array('new_comment' => $this->getServiceLocator()->get('translator')->translate('A comment is required'))));
			    		$valid = false;
			    	}
			    	else {
			    		$order->status = 'Nouvelle';
			    		$text = 'rejetée';
			    	}
    			}
				if ($valid) {
	    			// Update the entity with the data from the valid form and update it in the database
	    			if ($order->comment) $order->comment .= PHP_EOL.PHP_EOL;
	    			$order->comment .= Date('d/m/Y').' ('.$current_user->n_fn.') :'.PHP_EOL.$form->get('new_comment')->getValue();
	    			$this->getOrderTable()->save($order, $current_user);
	    			 
	    			// Notify the order responsibles
/*	    			$responsible = $this->getUserTable()->get($order->responsible_id);
	    			$select = $this->getVcardPropertyTable()->getSelect()
		    			->where(array('vcard_id' => $responsible->contact_id, 'name' => 'EMAIL'));
	    			$responsible_email = $this->getVcardPropertyTable()->selectWith($select)->current();*/
	
	    			$config = $this->getServiceLocator()->get('config');
	    			$settings = $config['citCoreSettings'];
	    			$toList = Functions::getToList('resp_commande', 'order_site', 'site_id', $order->site_id, $this->getUserTable());
	    			foreach ($toList as $addressee) {
		    			\CitCore\Controller\Functions::envoiMail(
		    					$this->getServiceLocator(),
		    					$addressee->username,
		    					'Nous vous informons que votre commande : '.
		    					$order->identifier.'-'.sprintf('%1$06d', $order->id).
		    					' a été '.$text.' par : '.$current_user->n_fn.'.',
		    					'Equipements d\'impression : Validation ou rejet de commande',
		    					NULL, NULL);
	    			}
	
	    			// Redirect to the index
	    			return $this->redirect()->toRoute('order');
				}
			}
    	}
    	return array(
    			'current_user' => $current_user,
    			'title' => 'Order',
    			'header' => $this->setHeader($order, $current_user),
    			'form' => $form,
    			'id' => $id,
    			'order' => $order,
    	);
    }
    
    public function retractionAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('order');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    
    	// Retrieve the order
    	$order = $this->getOrderTable()->get($id);
    
    	// Create the form object and initialize it from the existing entity
    	$form = new OrderRetractionForm();
    	$form->bind($order);
    
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$validate = $request->getPost('validate', 'No');
    		$form->setData($request->getPost());
    
    		if ($form->isValid()) {

    			// Comment required on reject
    			if (!$form->get('new_comment')->getValue()) {
    				$form->get('new_comment')->setMessages(array(array('new_comment' => $this->getServiceLocator()->get('translator')->translate('A comment is required'))));
    			}
    			else {
	    				 
	    			// Update the order and the deployment status
	    			$order->status = 'Annulée';
	    			$order->retraction_date = Date('Y-m-d');
	    			if ($order->comment) $order->comment .= PHP_EOL.PHP_EOL;
	    			$order->comment .= Date('d/m/Y').' ('.$current_user->n_fn.') :'.PHP_EOL.$form->get('new_comment')->getValue();
	    			$this->getOrderTable()->save($order, $current_user);
	    			$deployment = $this->getDeploymentTable()->get($order->id, 'order_id');
	    			$deployment->status = 'Annulée';
	    			$deployment->retraction_date = $order->retraction_date;
	    			$this->getDeploymentTable()->save($deployment, $current_user);
	    			 
	    			$text = 'Nous vous confirmons que la commande : '.
	    					$order->identifier.'-'.sprintf('%1$06d', $order->id).
	    					' a été annulée avant la fin de période de rétractation par : '.$current_user->n_fn.'.';
	    			$title = 'Equipements d\'impression : Annulation de commande en période de rétractation';
	
	    			// Notify the responsibles
/*	    			$responsible = $this->getUserTable()->get($order->responsible_id);
	    			$select = $this->getVcardPropertyTable()->getSelect()
	    			->where(array('vcard_id' => $responsible->contact_id, 'name' => 'EMAIL'));
	    			$responsible_email = $this->getVcardPropertyTable()->selectWith($select)->current();*/
	   
	    			$config = $this->getServiceLocator()->get('config');
	    			$settings = $config['citCoreSettings'];
	    			$toList = Functions::getToList('resp_commande', 'order_site', 'site_id', $order->site_id, $this->getUserTable());
	    			foreach (toList as $addressee) {
		    			\CitCore\Controller\Functions::envoiMail(
		    					$this->getServiceLocator(),
		    					$addressee->username,
								$text,
		    					$title,
		    					NULL, NULL);
	    			}
	
	    			// Notify the approvers
/*	    			if ($order->approver_id) {
		    			$approver = $this->getUserTable()->get($order->approver_id);
		    			$select = $this->getVcardPropertyTable()->getSelect()
		    			->where(array('vcard_id' => $approver->contact_id, 'name' => 'EMAIL'));
		    			$approver_email = $this->getVcardPropertyTable()->selectWith($select)->current();*/
		    			
	    			$config = $this->getServiceLocator()->get('config');
	    			$settings = $config['citCoreSettings'];
	    			$toList = Functions::getToList('valideur', 'order_site', 'site_id', $order->site_id, $this->getUserTable());
	    			foreach (toList as $addressee) {
		    			\CitCore\Controller\Functions::envoiMail(
		    					$this->getServiceLocator(),
		    					$addressee->username,
		    					$text,
		    					$title,
		    					NULL, NULL);
	    			}
    
	    			// Redirect to the index
	    			return $this->redirect()->toRoute('order');
    			}
    		}
    	}
    	return array(
    			'current_user' => $current_user,
    			'title' => 'Order',
    			'header' => $this->setHeader($order, $current_user),
    			'form' => $form,
    			'id' => $id,
    			'order' => $order,
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

    	// Retrieve the order
    	$order = $this->getOrderTable()->get($id);

    	// Retrieve the responsible
/*    	$responsible = $this->getUserTable()->get($order->responsible_id);
    	$contact = $this->getVcardTable()->get($responsible->contact_id);
    	$responsible->n_fn = $contact->n_fn;*/
    	 
    	// Retrieve the site
    	$site = $this->getSiteTable()->get($order->site_id);
    	
    	// Retrieve the user validation from the post
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$del = $request->getPost('del', $this->getServiceLocator()->get('translator')->translate('No'));
    
			// And delete the entity from the database in the "yes" case
    		if ($del == $this->getServiceLocator()->get('translator')->translate('Yes')) {
    			$id = (int) $request->getPost('id');
    			$this->getOrderTable()->delete($id);
    			$this->getOrderProductTable()->multipleDelete(array('order_id' => $id));
    		}
    
    		// Redirect to the index
    		return $this->redirect()->toRoute('order');
    	}
    
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Order',
    		'id' => $id,
    		'order' => $order,
    		'site' => $site,
//    		'responsible' => $responsible
       	);
    }

    public function pdfAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('order');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    
    	// Retrieve the order
    	$order = $this->getOrderTable()->get($id);

    	$pdf = new PdfModel();
	    $pdf->setOption("filename", "confirmation");
	    $pdf->setOption("paperSize", "a4"); //Defaults to 8x11
	    $pdf->setVariables(array(
    			'current_user' => $current_user,
    			'allowedRoutes' => $current_user->allowedRoutes,
    			'title' => 'Order',
    			'header' => $this->setHeader($order, $current_user),
    			'order' => $order,
	    ));
	    return $pdf;
    }

    public function importAction()
    {
    	// Check the presence of the id parameter for the entity to import
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('order');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    		
    	// Retrieve the link and its parent folder
    	$link = $this->getLinkTable()->get($id);
    	$parent_id = $link->parent_id;
    
    	// Retrieve the site list
    	$select = $this->getSiteTable()->getSelect();
//    		->where(array('is_open' => '1'));
    	$cursor = $this->getSiteTable()->selectWith($select);
    	$sites = array();
    	foreach ($cursor as $site) $sites[$site->caption] = $site;

    	$types = array('Equipement' => 'Equipement', 'Retrait' => 'Equipement');
    	
    	// Retrieve the product list
    	$select = $this->getProductTable()->getSelect()
    		->where(array('is_on_sale' => '1'));
    	$cursor = $this->getProductTable()->selectWith($select);
    	$products = array();
    	foreach ($cursor as $product) $products[$product->caption] = $product;
    	 
    	// Retrieve the contact list
    	$select = $this->getVcardTable()->getSelect();
    	$cursor = $this->getVcardTable()->selectWith($select);
    	$contacts = array();
    	foreach ($cursor as $contact) $contacts[$contact->n_fn] = $contact;
    	 
    	$file = 'data/documents/'.$link->id;
    	$validity = Functions::controlCsv(
    			$file, // Path to the file
    			array(	255, // Temporary identifier, 
    					$sites, // Site caption
    					'date', // Order date
    					255, // Order caption
    					2047, // Description
    					'int', // Number of people
    					'float', // Surface
    					'int', // Number of floors
    					$types, // Type
    					255, //$products, // Product caption or stock caption depending on the type
    					3, // Option 1
    					3, // Option 2
    			    	3, // Option 3
    			    	3, // Option 4
    			    	3, // Option 5
    			    	3, // Option 6
    			    	3, // Option 7
    			    	3, // Option 8
    			    	3, // Option 9
    			    	3, // Option 10
    			    	3, // Option 11
    			    	3, // Option 12
    			    	3, // Option 13
    			    	3, // Option 14
    			    	3, // Option 15
    			    	$contacts, // Contact
    			    	'date', // Hoped delivery date
    			    	255, // Building
    			    	255, // Floor
    			    	255, // Department
    			    	2047 // Comment
    			), // Type list
    			TRUE, // Ignore first row (column headers)
    			200); // Max number of rows
    			foreach ($validity as $ok => $content) { // content is a list of errors if not ok

    				if ($ok) {
    						
    					// sort between duplicate, inconsistent and ok rows
    					$not_duplicate = array();
	    				$duplicate = array();
	    				$not_consistent = array();
	    				$tmpOrders = array();
    					
    					foreach ($content as $row) {

    						// Load the working array
    						$tmpOrder[] = array();
    						$tmpOrder['tmpIdentifier'] = $row[0];
    						$tmpOrder['site_caption'] = $row[1];
    						$tmpOrder['order_date'] = $row[2];
    						$tmpOrder['caption'] = $row[3];
    						$tmpOrder['description'] = $row[4];
    						$tmpOrder['nb_people'] = $row[5];
    						$tmpOrder['surface'] = $row[6];
    						$tmpOrder['nb_floors'] = $row[7];
    						$tmpOrder['type'] = $row[8];
    						$tmpOrder['product_caption'] = $row[9];
    						$tmpOrder['options'] = array();
    						$tmpOrder['options'][0] = $row[10];
    						$tmpOrder['options'][1]  = $row[11];
    						$tmpOrder['options'][2]  = $row[12];
    						$tmpOrder['options'][3]  = $row[13];
    						$tmpOrder['options'][4]  = $row[14];
    						$tmpOrder['options'][5]  = $row[15];
    						$tmpOrder['options'][6]  = $row[16];
    						$tmpOrder['options'][7]  = $row[17];
    						$tmpOrder['options'][8]  = $row[18];
    						$tmpOrder['options'][9]  = $row[19];
    						$tmpOrder['options'][10]  = $row[20];
    						$tmpOrder['options'][11]  = $row[21];
    						$tmpOrder['options'][12]  = $row[22];
    						$tmpOrder['options'][13]  = $row[23];
    						$tmpOrder['options'][14]  = $row[24];
    						$tmpOrder['n_fn'] = $row[25];
    						$tmpOrder['hoped_delivery_date'] = $row[26];
    						$tmpOrder['building'] = $row[27];
    						$tmpOrder['floor'] = $row[28];
    						$tmpOrder['department'] = $row[29];
    						$tmpOrder['comment'] = $row[30];
    						$tmpOrders[] = $tmpOrder;
    						
    						// Check the unicity and consistency
    						$status = 'ok';
    						
    						// Duplicate order case : Check if an order already exists on the same site
    						$site = null;
    						foreach ($sites as $caption => $s) {
    							if ($caption == $row[1]) {
	    							$site = $s; break;
    							}
    						}
    						$select = $this->getOrderTable()->getSelect()->where(array('site_id' => $site->id));
    						$cursor = $this->getOrderTable()->selectWith($select);

    						// Save the order for further deleting before re-importing
    						if (count($cursor) > 0) $order = $cursor->current(); else $order = null;
    						
							// The order can be re-imported only if it is not in status 'Importée'
    						if (count($cursor) > 0 && $order->status != 'Importée') $status = 'duplicate';
    						else {
    							
    							// Equipements
    							if ($tmpOrder['type'] == 'Equipement') {
/*
    								if (!array_key_exists($tmpOrder['product_caption'], $products)) {
    									$status = 'duplicate';
    									break;
    								}*/
    								$product_id = $products[$tmpOrder['product_caption']]->id;
	    							$select = $this->getProductOptionTable()->getSelect()
	    								->where(array('product_id' => $product_id))
	    								->order(array('reference'));
	    							$cursor = $this->getProductOptionTable()->selectWith($select);
	    							$productOptions = array();
	    							$count = 0;
	    							foreach ($cursor as $productOption) {
	    								if ($tmpOrder['options'][$count] == 'Oui') {
	    									$row[10 + $count] = $productOption->caption;
	    									$tmpOrder['options'][$count] = $productOption->caption;
	    								}
	    								else {
	    									$row[10 + $count] = null;
	    									$tmpOrder['options'][$count] = null;
	    								}
	    								$productOptions[$productOption->id] = $count++;
	    							}
	    							foreach ($productOptions as $option_id => $row_count) {
	    								if ($tmpOrder['options'][$row_count]) {
	    									
	    									// Check option dependency (constraint = 1)
	    									$select = $this->getProductOptionMatrixTable()->getSelect()
	    										->where(array('product_id' => $product_id, 'row_option_id' => $option_id, 'constraint' => 1));
	    									$cursor = $this->getProductOptionMatrixTable()->selectWith($select);
	    									foreach($cursor as $cell) {
		    									$col_count = $productOptions[$cell->col_option_id];
		    									if (!$tmpOrder['options'][$col_count]) {
		    										$status = 'inconsistent';
		    										break;
		    									}
		    								}
	    									// Check option exclusion (constraint = 2)
	    									$select = $this->getProductOptionMatrixTable()->getSelect()
	    										->where(array('product_id' => $product_id, 'row_option_id' => $option_id, 'constraint' => 2));
	    									$cursor = $this->getProductOptionMatrixTable()->selectWith($select);
	    									foreach($cursor as $cell) {
		    									$col_count = $productOptions[$cell->col_option_id];
		    									if ($tmpOrder['options'][$col_count]) {
		    										$status = 'inconsistent';
		    										break;
		    									}
	    									}
	    								}
	    							}
    							}
    							// Check for duplicate Withdrawals (stocks already linked to another order)
								else {

									// Retrieve the stock list
									$select = $this->getStockTable()->getSelect()
										->where(array('site_id' => $sites[$tmpOrder['site_caption']]->id));
									$cursor = $this->getStockTable()->selectWith($select);
									$stocks = array();
									foreach ($cursor as $stock) $stocks[$stock->caption] = $stock;

									if (!array_key_exists($tmpOrder['product_caption'], $stocks)) {
										$status = 'inconsistent';
									}
									else {
										// Fetch a withdrawal on the given stock 
										$stock = $stocks[$tmpOrder['product_caption']];
										$select = $this->getOrderWithdrawalTable()->getSelect()
											->where(array('stock_id' => $stock->id));
										$cursor = $this->getOrderWithdrawalTable()->selectWith($select);
										if (count($cursor) > 0) {
											
											// The withdrawal is duplicate if it il linked to another order
											$withdrawal = $cursor->current();
											if (!$order || $withdrawal->order_id != $order->id) $status = 'duplicate';
										}
									}
								}
    						}
    						if ($status == 'ok') $not_duplicate[] = $row;
    						elseif ($status == 'inconsistent') $not_consistent[] = $row;
    						else $duplicate[] = $row;
    					}
    					$request = $this->getRequest();
    					if ($request->isPost()) {
    						$confirm = $request->getPost('confirm', $this->getServiceLocator()->get('translator')->translate('No'));
    
    						if ($confirm == $this->getServiceLocator()->get('translator')->translate('Import the data')) {

    							// If a duplicate order exists, it can be re-imported only if in the status 'Importée'
    							if ($order) {
    								// Delete the products and options
    								$select = $this->getOrderProductTable()->getSelect()
    									->where(array('order_id' => $order->id));
    								$cursor = $this->getOrderProductTable()->selectWith($select);
    								foreach ($cursor as $orderProduct) {
    									$this->getOrderProductOptionTable()->multipleDelete(array('order_product_id' => $orderProduct->id));
    								}
    								$this->getOrderProductTable()->multipleDelete(array('order_id' => $order->id));
    								
    								// Delete the withdrawal
    								$this->getOrderWithdrawalTable()->multipleDelete(array('order_id' => $order->id));
    								
    								// Delete the order
    								$this->getOrderTable()->delete($order->id);
    							}
    							
    							$order = new Order();
    							$orderProduct = new OrderProduct();
    							$orderProductOption = new OrderProductOption();
    							$orderWithdrawal = new OrderWithdrawal();
    							$tmpId = null;
    							foreach ($tmpOrders as $tmpOrder) {
    								if ($tmpOrder['tmpIdentifier'] != $tmpId) {
	    								// Insert the order
	    								$order->site_id = $sites[$tmpOrder['site_caption']]->id;
	    								$order->order_date = $tmpOrder['order_date'];
	    								$order->identifier = 'C'.date('Y');
	    								$order->caption = $tmpOrder['caption'];
	   									$order->description = $tmpOrder['description'];
	   									$order->nb_people = $tmpOrder['nb_people'];
	   									$order->surface = $tmpOrder['surface'];
	   									$order->nb_floors = $tmpOrder['nb_floors'];
	   									$order->status = 'Importée';
	         							$order_id = $this->getOrderTable()->save($order, $current_user);
    								}
    								$tmpId = $tmpOrder['tmpIdentifier'];

    								if ($tmpOrder['type'] == 'Equipement') {
		    							// Insert the order product
		    							$orderProduct->order_id = $order_id;
		    							$orderProduct->product_id = $products[$tmpOrder['product_caption']]->id;
		    							$product = $this->getProductTable()->get(array($orderProduct->product_id));
		    							$orderProduct->price = $product->price;
		    							$orderProduct->contact_id = $contacts[$tmpOrder['n_fn']]->id;
		    							$orderProduct->hoped_delivery_date = $tmpOrder['hoped_delivery_date'];
		    							$orderProduct->building = $tmpOrder['building'];
		    							$orderProduct->floor = $tmpOrder['floor'];
		    							$orderProduct->department = $tmpOrder['department'];
		    							$orderProduct->comment = $tmpOrder['comment'];
		    							$order_product_id = $this->getOrderProductTable()->save($orderProduct, $current_user);
		    							
		    							// Insert the options
		    							$orderProductOption->order_product_id = $order_product_id;
										$select = $this->getProductOptionTable()->getSelect()
											->where(array('product_id' => $orderProduct->product_id))
								    		->order(array('reference'));
										$cursor = $this->getProductOptionTable()->selectWith($select);
										$count = 0;
										foreach ($cursor as $productOption) {
											if ($tmpOrder['options'][$count] == 'Oui') {
												$orderProductOption->order_product_id = $order_product_id;
												$orderProductOption->product_option_id = $productOption->id;
												$orderProductOption->price = $productOption->price;
		    									$this->getOrderProductOptionTable()->save($orderProductOption, $current_user);
											}
											$count++;
										}
    								}
    								else {
    									$orderWithdrawal->order_id = $order_id;
		    							$orderWithdrawal->stock_id = $stocks[$tmpOrder['product_caption']]->id;
		    							$orderWithdrawal->comment = $tmpOrder['comment'];
		    							$order_withdrawal_id = $this->getOrderWithdrawalTable()->save($orderWithdrawal, $current_user);
    								}
    							}
    						}
    						return $this->redirect()->toRoute('order');
    					}
    
    					return array(
    							'current_user' => $current_user,
    							'allowedRoutes' => $current_user->allowedRoutes,
    							'title' => 'Order',
    							'id'    => $id,
    							'ok' => $ok,
    							'not_duplicate' => $not_duplicate,
    							'duplicate' => $duplicate,
    							'not_consistent' => $not_consistent,
    					);
    				}
    				else {
    					// Return the page
    					return new ViewModel(array(
    							'current_user' => $current_user,
    							'allowedRoutes' => $current_user->allowedRoutes,
    							'title' => 'Stock',
    							'ok' => $ok,
    							'errors' => $content
    					));
    				}
    			}
    }

    public function startAction()
    {
    	// Check the presence of the id parameter 
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('order');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    
    	// Retrieve the order
    	$order = $this->getOrderTable()->get($id);

    	$header = $this->setHeader($order, $current_user);
    
    	// Retrieve the site
    	$site = $this->getSiteTable()->get($order->site_id);
    	 
    	// Retrieve the user validation from the post
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$order->status = 'Nouvelle';
//    		$order->responsible_id = $current_user->user_id;
    		$this->getOrderTable()->save($order, $current_user);
    
    		// Redirect
    		return $this->redirect()->toRoute('order');
    	}
    
    	return array(
    			'current_user' => $current_user,
    			'allowedRoutes' => $current_user->allowedRoutes,
    			'title' => 'Order',
    			'header' => $header,
    			'id' => $id,
    			'order' => $order,
    			'site' => $site,
    	);
    }
    
    public function getDeploymentTable()
    {
    	if (!$this->deploymentTable) {
    		$sm = $this->getServiceLocator();
    		$this->deploymentTable = $sm->get('CitDeployment\Model\DeploymentTable');
    	}
    	return $this->deploymentTable;
    }

    public function getDeploymentProductTable()
    {
    	if (!$this->deploymentProductTable) {
    		$sm = $this->getServiceLocator();
    		$this->deploymentProductTable = $sm->get('CitDeployment\Model\DeploymentProductTable');
    	}
    	return $this->deploymentProductTable;
    }

    public function getDeploymentWithdrawalTable()
    {
    	if (!$this->deploymentWithdrawalTable) {
    		$sm = $this->getServiceLocator();
    		$this->deploymentWithdrawalTable = $sm->get('CitDeployment\Model\DeploymentWithdrawalTable');
    	}
    	return $this->deploymentWithdrawalTable;
    }

    public function getLinkTable()
    {
    	if (!$this->linkTable) {
    		$sm = $this->getServiceLocator();
    		$this->linkTable = $sm->get('CitCore\Model\LinkTable');
    	}
    	return $this->linkTable;
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
    
    public function getOrderWithdrawalTable()
    {
    	if (!$this->orderWithdrawalTable) {
    		$sm = $this->getServiceLocator();
    		$this->orderWithdrawalTable = $sm->get('CitOrder\Model\OrderWithdrawalTable');
    	}
    	return $this->orderWithdrawalTable;
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

    public function getProductOptionMatrixTable()
    {
    	if (!$this->productOptionMatrixTable) {
    		$sm = $this->getServiceLocator();
    		$this->productOptionMatrixTable = $sm->get('CitMasterData\Model\ProductOptionMatrixTable');
    	}
    	return $this->productOptionMatrixTable;
    }

    public function getRegionTable()
    {
    	if (!$this->regionTable) {
    		$sm = $this->getServiceLocator();
    		$this->regionTable = $sm->get('CitMasterData\Model\RegionTable');
    	}
    	return $this->regionTable;
    }
    
    public function getSiteTable()
    {
    	if (!$this->siteTable) {
    		$sm = $this->getServiceLocator();
    		$this->siteTable = $sm->get('CitOrder\Model\SiteTable');
    	}
    	return $this->siteTable;
    }

    public function getSiteContactTable()
    {
    	if (!$this->siteContactTable) {
    		$sm = $this->getServiceLocator();
    		$this->siteContactTable = $sm->get('CitOrder\Model\SiteContactTable');
    	}
    	return $this->siteContactTable;
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
