<?php
namespace CitOrder\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use CitOrder\Model\Site;
use CitOrder\Model\SiteContact;
use CitOrder\Model\GenericTable;
use CitOrder\Form\SiteForm;
use CitOrder\Form\SiteContactForm;
use CitCore\Controller\Functions;
use Zend\Session\Container;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Db\TableGateway\TableGateway;

class SiteContactController extends AbstractActionController
{
	protected $siteTable;
   	protected $siteContactTable;

   	public function indexAction()
   	{
   		$id = (int) $this->params()->fromRoute('id', 0);
   		if (!$id) {
   			return $this->redirect()->toRoute('site/index');
   		}   		
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
   		   		
   		$major = $this->params()->fromQuery('major', NULL);
   		if (!$major) $major = 'caption';
   		$dir = $this->params()->fromQuery('dir', NULL);
   		if (!$dir) $dir = 'ASC';   		
   		
   		// Retrieve the site
   		$site = $this->getSiteTable()->get($id);
   		
   		$select = $this->getSiteContactTable()->getSelect();
   		$select->where(array('site_id = ?' => $id))
 			->join('contact_vcard', 'order_site_contact.contact_id = contact_vcard.id', array('n_fn'), 'left');
   		// Execute the request
   		$sitecontacts = $this->getSiteContactTable()->selectWith($select);
   		 
   		// Return the link list
   		return new ViewModel(array(
    		'current_user' => $current_user,
   			'title' => 'Sites',
   			'major' => $major,
   			'dir' => $dir,
   			'sitecontacts' => $sitecontacts,
   			'id' => $id,
   			'site' => $site
   		));
   	}
   	
   	public function addAction()
   	{
   	
   		$id = (int) $this->params()->fromRoute('id', 0);
   		if (!$id) {
   			return $this->redirect()->toRoute('site');
   		}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
   		
   		$form = new SiteContactForm();
   		   		 
   		$select = $this->getVcardTable()->getSelect();
   		$select->order(array('N_LAST', 'N_FIRST'));
   		$cursor = $this->getVcardTable()->selectWith($select);
   		$vcards = array();
   		foreach ($cursor as $vcard ) {
   			$vcards[$vcard->id] = $vcard->n_last." ".$vcard->n_first ;		
   		}
   		$form->addElements($vcards);
		$form->get('submit')->setValue('Ajouter');

 		// Set the form filters and hydrate it with the data from the request
   		$request = $this->getRequest();
   		if ($request->isPost()) {
   			$entity = new SiteContact();
   			
   			$form->setInputFilter($entity->getInputFilter());
   			$form->setData($request->getPost());
   			
   			if ($form->isValid()) {
   				$entity->exchangeArray($form->getData());

	   			// Overrides the existing main contact
	   			if ($entity->is_main_contact) {
	   				$this->getSiteContactTable()->update(array('is_main_contact' => null), array('site_id' => $id), $current_user);
	   			}   				
   				$entity->id = NULL;
   				$entity->site_id = $id;
         		$entity->id = $this->getSiteContactTable()->save($entity, $current_user);
   				
   				// Sets the main contact in the site entity
   				if ($entity->is_main_contact) {
	   				$site = $this->getSiteTable()->get($id);
	   				$site->contact_id = $entity->contact_id;
	   				$this->getSiteTable()->save($site, $current_user);
   				}
   				return $this->redirect()->toRoute('siteContact/index', array('id' => $id));
   			}
   		}
   		return array(
    		'current_user' => $current_user,
   			'title' => 'Sites',
   			'form' => $form,
   			'id' => $id,
   		);
   	}
   	
   	public function deleteAction()
   	{
   		// Check the presence of the id parameter for the entity to delete
   		$id = (int) $this->params()->fromRoute('id', 0);
   		if (!$id) {
   			return $this->redirect()->toRoute('site/add');
   		}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
   		   		
   		// Retrieve the entity
   		$siteContact = $this->getSiteContactTable()->get($id);

   		// Retrieve the entity
   		$site = $this->getSiteTable()->get($siteContact->site_id); 
   		 
   		// Retrieve the user validation from the post
   		$request = $this->getRequest();
   		if ($request->isPost()) {
   			$del = $request->getPost('del', $this->getServiceLocator()->get('translator')->translate('No'));
   			 
   			// And delete the link into the database in the "yes" case
   			if ($del == $this->getServiceLocator()->get('translator')->translate('Yes')) {
   				$id = (int) $request->getPost('id');
   				$this->getSiteContactTable()->delete($id);
   			}
   			// Remove the main contact in the site entity
   			if ($siteContact->is_main_contact) {
   				$site = $this->getSiteTable()->get($siteContact->site_id);
   				$site->contact_id = null;
   				$this->getSiteTable()->save($site, $current_user);
   			}
   			
   			return $this->redirect()->toRoute('siteContact/index', array('id' => $siteContact->site_id));
   		}
   		 
   		return array(
    		'current_user' => $current_user,
    		'title' => 'Sites',
   			'id' => $id,
   			'site' => $site,
   			'siteContact' => $siteContact
   		);		
    }

    public function getSiteContactTable()
    {
    	if (!$this->siteContactTable) {
    		$sm = $this->getServiceLocator();
    		$this->siteContactTable = $sm->get('CitOrder\Model\SiteContactTable');
    	}
    	return $this->siteContactTable;
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
    