<?php
namespace CitOrder\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Session\Container;

class SiteTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function getAdapter()
    {
    	return $this->tableGateway->getAdapter();
    }
    
    public function getSelect()
    {
		$select = new \Zend\Db\Sql\Select();
	    $select->from($this->tableGateway->getTable());
    	return $select;
    }

    public function selectWith($select)
    {
//    	throw new \Exception($select->getSqlString($this->getAdapter()->getPlatform()));
    	return $this->tableGateway->selectWith($select);
    }
    
    public function selectWithAsArray($select)
    {
    	$statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    	return $resultSet;
    }

    public function fetchDistinct($column)
    {
		$select = new \Zend\Db\Sql\Select();
    	$select->from($this->tableGateway->getTable())
			   ->columns(array($column))
    		   ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT);
		return $this->tableGateway->selectWith($select);
    }
    
    public function get($id, $column = 'id')
    {
    	$id  = (int) $id;
    	$rowset = $this->tableGateway->select(array($column => $id));
    	$row = $rowset->current();
    	if (!$row) {
    		throw new \Exception("Could not find row $id");
    	}
    	return $row;
    }

    public function save($entity, $user)
    {
    	$data = array();

    	// Specific
    	$data['caption'] = $entity->caption;
		$data['description'] = $entity->description;
//		$data['region'] = $entity->region;
		$data['contact_id'] = (int) $entity->contact_id;
		$data['region_id'] = (int) $entity->region_id;
		$data['nb_people'] = $entity->nb_people;
		$data['surface'] = (float) $entity->surface;
		$data['nb_floors'] = $entity->nb_floors;
		$data['address_street'] = $entity->address_street;
		$data['address_complt'] = $entity->address_complt;
		$data['address_post_office_box'] = $entity->address_post_office_box;
		$data['address_zip'] = $entity->address_zip;
		$data['address_city'] = $entity->address_city;
		$data['address_state'] = $entity->address_state;
		$data['address_country'] = $entity->address_country;
//		$data['is_open'] = $entity->is_open;
		$data['disabled_workers'] = $entity->disabled_workers;
		$data['availability'] = $entity->availability;
		$data['security'] = $entity->security;
		$data['lift'] = $entity->lift;
		$data['parking'] = $entity->parking;
		$data['comment'] = $entity->comment;
		
		//Import champs
		$data['site_id'] = $entity->site_id;
		$data['raison_sociale_livraison'] = $entity->raison_sociale_livraison;
		$data['siret_livraison'] = $entity->siret_livraison;
		$data['adresse'] = $entity->adresse;
		$data['code_postal'] = $entity->code_postal;
		$data['ville'] = $entity->ville;
		$data['zone_geographique'] = $entity->zone_geographique;
		$data['siret_facturation'] = $entity->siret_facturation;
		$data['entite_facturation'] = $entity->entite_facturation;
		$data['effectif'] = $entity->effectif;
		$data['superficie'] = $entity->superficie;
		$data['nombre_etages'] = $entity->nombre_etages;
		$data['telephone_site'] = $entity->telephone_site;
		$data['horaires_logistique'] = $entity->horaires_logistique;
		$data['contraintes_logistique'] = $entity->contraintes_logistique;
		$data['accessibilite_livraison'] = $entity->accessibilite_livraison;
		$data['nom_contact_livraison'] = $entity->nom_contact_livraison;
		$data['tel_contact_livraison'] = $entity->tel_contact_livraison;
		$data['email_contact_livraison'] = $entity->email_contact_livraison;
		$data['nom_contact_livraison2'] = $entity->nom_contact_livraison2;
		$data['tel_contact_livraison2'] = $entity->tel_contact_livraison2;
		$data['email_contact_livraison2'] = $entity->email_contact_livraison2;

			
		//test
		$data['caption'] = $entity->raison_sociale_livraison;
		$data['nb_people']=$entity->effectif;
		$data['surface'] =$entity->superficie;
		$data['nb_floors']=$entity->nombre_etages;
		
		
		$data['instance_id'] = $user->instance_id;
		$data['update_time'] = date("Y-m-d H:i:s");
		$data['update_user'] = $user->user_id;
        $id = (int)$entity->id;
        if ($id == 0) {
        	$data['creation_time'] = date("Y-m-d H:i:s");
        	$data['creation_user'] = $user->user_id;
        	$this->tableGateway->insert($data);
        	return $this->getAdapter()->getDriver()->getLastGeneratedValue();
        } else {
            if ($this->get($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }

    public function multipleDelete($where)
    {
        $this->tableGateway->delete($where);
    }
}
