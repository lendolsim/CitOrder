<?php
namespace CitOrder\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use CitOrder\Model\Site;
use CitOrder\Model\TmpSite;
use CitOrder\Form\SiteForm;
use CitOrder\Form\SiteFormResp;
use CitCore\Controller\Functions;
use Zend\Session\Container;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Db\TableGateway\TableGateway;

class SiteController extends AbstractActionController
{
	protected $linkTable;
	protected $regionTable;
	protected $siteTable;
   	protected $siteContactTable;
	protected $tmpSiteTable;
   	
   	public function indexAction()
    {		 
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	$current_user->retrieveHabilitations($this);
    	 
       	// Prepare the SQL request
    	$major = $this->params()->fromQuery('major', NULL);
    	if (!$major) $major = 'caption';
    	$dir = $this->params()->fromQuery('dir', NULL);
    	if (!$dir) $dir = 'ASC';
    	$select = $this->getSiteTable()->getSelect();
    	$select->order(array($major.' '.$dir, 'caption'))
 	  		   ->join('contact_vcard', 'order_site.contact_id = contact_vcard.id', array('n_fn'), 'left');
     	
    	// Execute the request
    	$sites = $this->getSiteTable()->selectWith($select);

    	// Return the link list
    	return new ViewModel(array(
    			'current_user' => $current_user,
    			'title' => 'Sites',
    			'major' => $major,
    			'dir' => $dir,
    			'sites' => $sites,
    	));    	
    }

    protected function setHeader($site, $current_user)
    {
    	// Retrieve the contacts
    	$select = $this->getSiteContactTable()->getSelect()
    	->where(array('site_id' => $site->id))
    	->join('contact_vcard', 'order_site_contact.contact_id = contact_vcard.id', array('n_fn'), 'left')
    	->order(array('n_fn'));
    	$cursor = $this->getSiteContactTable()->selectWith($select);
    	$main_contact = '';
    	$site_contacts = ''; $first = true;
    	foreach($cursor as $siteContact) {
    		if ($siteContact->is_main_contact) $main_contact = $siteContact->n_fn;
    		else{
    			if (!$first) $site_contacts .= ', ';
    			$site_contacts .= $siteContact->n_fn;
    		}
    	}
    
    	// Prepare the form header
    	return array(
    			'caption' => array('label' => 'Caption', 'value' => $site->caption),
    			'description' => array('label' => 'Description', 'value' => $site->description),
    			'region' => array('label' => 'Region', 'value' => $site->region),
    			'nb_people' => array('label' => 'Number of people', 'value' => $site->nb_people),
    			'surface' => array('label' => 'Surface', 'value' => $site->surface),
    			'nb_floors' => array('label' => 'Number of floors', 'value' => $site->nb_floors),
    			'address_street' => array('label' => 'Address - street', 'value' => $site->address_street),
    			'address_complt' => array('label' => 'Address - complt', 'value' => $site->address_complt),
    			'address_post_office_box' => array('label' => 'Address - post office box', 'value' => $site->address_post_office_box),
    			'address_zip' => array('label' => 'Address - zip', 'value' => $site->address_zip),
    			'address_city' => array('label' => 'Address - city', 'value' => $site->address_city),
    			'address_state' => array('label' => 'Address - state', 'value' => $site->address_state),
    			'address_country' => array('label' => 'Address - country', 'value' => $site->address_country),
    			'disabled_workers' => array('label' => 'Disabled workers', 'value' => $site->disabled_workers),
    			'availability' => array('label' => 'Availability', 'value' => $site->availability),
    			'security' => array('label' => 'Security', 'value' => $site->security),
    			'lift' => array('label' => 'Lift', 'value' => $site->lift),
    			'parking' => array('label' => 'Parking', 'value' => $site->parking),
    			'comment' => array('label' => 'Comment', 'value' => $site->comment),
    	);
    }
    
    public function detailAction()
    {
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('site');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    
    	// Retrieve the allowed routes
    	$allowedRoutes = Functions::getAllowedRoutes($this);
    
    	// Retrieve the user's instance
    	$instance_id = Functions::getInstanceId($this);
    
    	// Retrieve the order
    	$site = $this->getSiteTable()->get($id);
    
    	return array(
    			'current_user' => $current_user,
    			'title' => 'Site',
    			'header' => $this->setHeader($site, $current_user),
    			'site' => $site,
    	);
    }
    
    public function addAction()
    {
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	
    	// Retrieve the allowed routes
    	$allowedRoutes = Functions::getAllowedRoutes($this);

    	// Retrieve the user's instance
    	$instance_id = Functions::getInstanceId($this);
     	
    	// Retrieve the regions
    	$select = $this->getRegionTable()->getSelect();
    	$cursor = $this->getRegionTable()->selectWith($select);
    	$regions = array();
    	foreach ($cursor as $region) $regions[] = $region;
    	
    	$form = new SiteForm();
    	$form->addElements($regions);
    	$form->get('submit')->setValue('Ajouter');
    	
    	// Set the form filters and hydrate it with the data from the request
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$entity = new Site();
    		$form->setInputFilter($entity->getInputFilter());
    		$form->setData($request->getPost());
    	
    		// Update the entity with the data from the valid form and create it into the database
    		if ($form->isValid()) {
    			$entity->exchangeArray($form->getData());
    			$entity->id = NULL;
    			if (!$entity->surface) $entity->surface = NULL;
    			$entity->id = $this->getSiteTable()->save($entity, $current_user);
    			// Redirect to index
    			return $this->redirect()->toRoute('site');
    		}
    	}
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Sites',
    		'form' => $form,
    	);    	
    }
    
    public function updateAction()
    {
    	// Check the presence of the id parameter for the entity to update
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('site/index');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	
    	// Retrieve the allowed routes
    	$allowedRoutes = Functions::getAllowedRoutes($this);

    	// Retrieve the user's instance
    	$instance_id = Functions::getInstanceId($this);
     	
    	    	// Create the entity object and initialize it from the database
    	$site = $this->getSiteTable()->get($id);

    	// Retrieve the regions
    	$select = $this->getRegionTable()->getSelect();
    	$cursor = $this->getRegionTable()->selectWith($select);
    	$regions = array();
    	foreach ($cursor as $region) $regions[] = $region;
    	 
    	$form = new SiteForm();
    	$form->addElements($regions);
    	$form->bind($site);
        $form->get('submit')->setAttribute('value', $this->getServiceLocator()->get('translator')->translate('Update'));
     	
    	// Set the form filters and hydrate it with the data from the request
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		
    		$form->setInputFilter($site->getInputFilter());
    		$form->setData($request->getPost());

    		// Update the entity with the data from the valid form and update it in the database
    		if ($form->isValid()) {
    			$this->getSiteTable()->save($form->getData(), $current_user);
    	
    			// Redirect to the index
    			return $this->redirect()->toRoute('site');
    		}
    	}
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Sites',
    		'form' => $form,
    		'site' => $site,
    		'id' => $id,
    	);
    }
        
    public function editAction()
    {
    	// Check the presence of the id parameter for the entity to update
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('site/index');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	 
    	// Retrieve the allowed routes
    	$allowedRoutes = Functions::getAllowedRoutes($this);
    	
    	// Retrieve the user's instance
    	$instance_id = Functions::getInstanceId($this);
    	
    	// Create the entity object and initialize it from the database
    	$site = $this->getSiteTable()->get($id);
    	 
    	// Create the form object and initialize it from the existing entity
    	$form = new SiteFormResp();
    	$select = $this->getVcardTable()->getSelect();
    	//$select->where(array('contact_id' => $id, 'status' => 'Active'));
    	$select->order(array('n_last', 'n_first'));
    	$cursor = $this->getVcardTable()->selectWith($select);
    	$vcards = array();
    	foreach ($cursor as $vcard ) {
    		$vcards[$vcard->id] = $vcard->n_last." ".$vcard->n_first;
    	}
    	 
    	$form->addElements($vcards);
    	$form->bind($site);
    	$form->get('submit')->setAttribute('value', $this->getServiceLocator()->get('translator')->translate('Update'));
    
    	// Set the form filters and hydrate it with the data from the request
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    
    		$form->setInputFilter($site->getInputFilter());
    		$form->setData($request->getPost());
    
    		// Update the entity with the data from the valid form and update it in the database
    		if ($form->isValid()) {
    			$this->getSiteTable()->save($form->getData(), $cuurent_user);
    			 
    			// Redirect to the index
    			return $this->redirect()->toRoute('site');
    		}
    	}
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Sites',
    		'form' => $form,
    		'id' => $id,
    	);
    }
   
    public function importAction()
    {
    	// Check the presence of the id parameter for the entity to import
    	$id = (int) $this->params()->fromRoute('id', 0);
    	if (!$id) {
    		return $this->redirect()->toRoute('site');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);
    	
    	// Retrieve the allowed routes
    	$allowedRoutes = Functions::getAllowedRoutes($this);

    	// Retrieve the user's instance
    	$instance_id = Functions::getInstanceId($this);
     	
    	// Retrieve all Region
    	$select = $this->getRegionTable()->getSelect();
    	$cursor = $this->getRegionTable()->selectWith($select);
    	$regions = array();
    	foreach ($cursor as $region) $regions[$region->caption] = $region;
    	
    	
    	// Retrieve the link and its parent folder
    	$link = $this->getLinkTable()->get($id);
    	$parent_id = $link->parent_id;
    
    	$file = 'data/documents/'.$link->id;
    	$validity = Functions::controlCsv(
    			$file, // Path to the file
    			array(	255, // Numéro généré par Pôle Emploi
    					255, // Nom du site à livrer
    					'int', // N° SIRET du site à livrer
    					255, // Numéro et rue site à livrer
    					'int', // CodePostal
    					255, // Ville
    					255, // ZoneGéographique
    					'int', //  N° SIRET de l'entité à facturer
    					$regions, //Nom de l'entité à facturer
    					'int', // Personnel affecté au site
    					'float', // Nombre de m² du site
    					'int', // Nombre d'étages du site
    					'int', // Numéro de contact standard du site
    					255, // Créneau horaire de livraison
    					255, // Commentaire sur les contraintes logistiques
    					255, // Commentaire facilitation accès site
    					255, // Nom du contact principal
    					'int', // Numéro de téléphone fixe/portable du contact principal
    					255, // Email du contact principal
    					255, // Nom du second contact
    					'int', // Numéro de téléphone fixe/portable du second contact
    					255, // EmailContactLivraison2

    				), // Type list
    			TRUE, // Ignore first row (column headers)
    			200); // Max number of rows
    	foreach ($validity as $ok => $content) { // content is a list of errors if not ok
    		// sort between duplicate and not duplicate rows according to the primary key last_name + first_name
    		$not_duplicate = array();
    		$duplicate = array();
    		if ($ok) {
    			foreach ($content as $row) {
    				$select = $this->getSiteTable()->getSelect()->where(array('site_id' => $row[0]));
    				$cursor = $this->getSiteTable()->selectWith($select);
    				if (count($cursor) > 0) $duplicate[] = $row;
    				else $not_duplicate[] = $row;
    			}
    			$request = $this->getRequest();
    			if ($request->isPost()) {
    				$confirm = $request->getPost('confirm', $this->getServiceLocator()->get('translator')->translate('No'));
    
    				if ($confirm == $this->getServiceLocator()->get('translator')->translate('Import the data')) {
    
    					// Empty the temporary table
    					$this->getTmpSiteTable()->multipleDelete(array("1" => "1"));
    					 
    					// Load the temporary table
    					$tmpSite = new TmpSite();
    					foreach ($not_duplicate as $row) {
    						$tmpSite->site_id = $row[0];
    						$tmpSite->raison_sociale_livraison = $row[1];
    						$tmpSite->siret_livraison = $row[2];
    						$tmpSite->adresse = $row[3];
    						$tmpSite->code_postal = $row[4];
    						$tmpSite->ville = $row[5];
    						$tmpSite->zone_geographique = $row[6];
    						$tmpSite->siret_facturation = $row[7];
    						$tmpSite->entite_facturation = $row[8];
    						$tmpSite->effectif = $row[9];
    						$tmpSite->superficie = $row[10];
    						$tmpSite->nombre_etages = $row[11];
    						$tmpSite->telephone_site = $row[12];
    						$tmpSite->horaires_logistique = $row[13];
    						$tmpSite->contraintes_logistique = $row[14];
    						$tmpSite->accessibilite_livraison = $row[15];
    						$tmpSite->nom_contact_livraison = $row[16];
    						$tmpSite->tel_contact_livraison = $row[17];
    						$tmpSite->email_contact_livraison = $row[18];
    						$tmpSite->nom_contact_livraison2 = $row[19];
    						$tmpSite->tel_contact_livraison2 = $row[20];
    						$tmpSite->email_contact_livraison2 = $row[21];
    						$this->getTmpSiteTable()->save($tmpSite, $current_user);
    					}
    					// Insert the sites
    					$select = $this->getTmpSiteTable()->getSelect();
    					$cursor = $this->getTmpSiteTable()->selectWith($select);
    					$site = new Site();
    					 
    					foreach ($cursor as $tmpSite) {
    						$site->site_id = $tmpSite->site_id;
    						$site->raison_sociale_livraison = $tmpSite->raison_sociale_livraison;
    						$site->siret_livraison = $tmpSite->siret_livraison;
    						$site->adresse = $tmpSite->adresse;
    						$site->code_postal = $tmpSite->code_postal;
    						$site->ville = $tmpSite->ville;
    						$site->zone_geographique = $tmpSite->zone_geographique;
    						$site->siret_facturation = $tmpSite->siret_facturation;
    						$site->entite_facturation = $tmpSite->entite_facturation;
    						$site->effectif = $tmpSite->effectif;
    						$site->superficie = $tmpSite->superficie;
    						$site->nombre_etages = $tmpSite->nombre_etages;
    						$site->telephone_site = $tmpSite->telephone_site;   						
    						$site->horaires_logistique = $tmpSite->horaires_logistique;
    						$site->contraintes_logistique = $tmpSite->contraintes_logistique;
    						$site->accessibilite_livraison = $tmpSite->accessibilite_livraison;
    						$site->nom_contact_livraison = $tmpSite->nom_contact_livraison;
    						$site->tel_contact_livraison = $tmpSite->tel_contact_livraison;
    						$site->email_contact_livraison = $tmpSite->email_contact_livraison;
    						$site->nom_contact_livraison2 = $tmpSite->nom_contact_livraison2;
    						$site->tel_contact_livraison2 = $tmpSite->tel_contact_livraison2;
    						$site->email_contact_livraison2 = $tmpSite->email_contact_livraison2;
    						
    						$site->id = $this->getSiteTable()->save($site, $current_user);
    					}
    				}
    				return $this->redirect()->toRoute('site');
    			}
    
    			return array(
			    		'current_user' => $current_user,
    					'title' => 'Sites',
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
    					'title' => 'Sites',
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
    		return $this->redirect()->toRoute('site');
    	}
    	// Retrieve the current user
    	$current_user = Functions::getUser($this);

    	// Retrieve the allowed routes
    	$allowedRoutes = Functions::getAllowedRoutes($this);

    	// Retrieve the link and its parent folder
    	$site = $this->getSiteTable()->get($id);
    	$parent_id = $site->id;
    	 
    	// Retrieve the user validation from the post
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		$del = $request->getPost('del', $this->getServiceLocator()->get('translator')->translate('No'));
    	
    		// And delete the link into the database in the "yes" case
    		if ($del == $this->getServiceLocator()->get('translator')->translate('Yes')) {
    			$id = (int) $request->getPost('id');
    			$this->getSiteTable()->delete($id);
    		}
    	
    		// Redirect to the index
    		return $this->redirect()->toRoute('site', array('action' => 'index', 'id' => $parent_id));
    	}
    	
    	return array(
    		'current_user' => $current_user,
    		'title' => 'Sites',
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
    
    public function getTmpSiteTable()
    {
    	if (!$this->tmpSiteTable) {
    		$sm = $this->getServiceLocator();
    		$this->tmpSiteTable = $sm->get('CitOrder\Model\TmpSiteTable');
    	}
    	return $this->tmpSiteTable;
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
