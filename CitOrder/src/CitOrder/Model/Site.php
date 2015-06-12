<?php
namespace CitOrder\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;


class Site implements InputFilterAwareInterface
{
	public $id;
    public $caption;
    public $description;
//    public $region;
	public $contact_id;
	public $region_id;
/*	public $n_fn;
	public $sec_contact_id;*/
    public $nb_people;
    public $surface;
    public $nb_floors;
    public $address_street;
	public $address_complt;
	public $address_post_office_box;
	public $address_zip;
	public $address_city;
	public $address_state;
	public $address_country;
//	public $is_open;
    public $disabled_workers;
	public $availability;
	public $security;
	public $lift;
	public $parking;
	public $comment;
	
	
	// Additional fields
	public $n_fn;
	
	//Import champs 
	public $site_id;//Numéro généré par Pôle Emploi
	public $raison_sociale_livraison;//Nom du site à livrer
	public $siret_livraison;//N° SIRET du site à livrer
	public $adresse;//Numéro et rue site à livrer
	public $code_postal;//Code postal du site à livrer
	public $ville;//Ville du site à livrer
	public $zone_geographique;//Zone géographique de livraison(entité region)
	public $siret_facturation;//N° SIRET de l'entité à facturer
	public $entite_facturation;//Nom du site à livrer
	public $effectif;//Personnel affecté au site
	public $superficie;//Nombre de m² du site
	public $nombre_etages;//Nombre d'étages du site
	public $telephone_site;//Numéro de contact standard du site
	public $horaires_logistique;//Créneau horaire de livraison
	public $contraintes_logistique;//Commentaire sur les contraintes logistiques
	public $accessibilite_livraison;//Commentaire facilitation accès site
	public $nom_contact_livraison;//Nom du contact principal
	public $tel_contact_livraison;//Numéro de téléphone fixe/portable du contact principal
	public $email_contact_livraison;//email du contact principal
	public $nom_contact_livraison2;//Nom du second contact
	public $tel_contact_livraison2;//Numéro de téléphone fixe/portable du second contact
	public $email_contact_livraison2;//Email du second contact
	
	
	
	
	
	protected $inputFilter;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
//        $this->region = (isset($data['region'])) ? $data['region'] : null;
        $this->contact_id = (isset($data['contact_id'])) ? $data['contact_id'] : null;
        $this->region_id = (isset($data['region_id'])) ? $data['region_id'] : null;
/*        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        $this->sec_contact_id = (isset($data['sec_contact_id'])) ? $data['sec_contact_id'] : null;*/
        $this->nb_people = (isset($data['nb_people'])) ? $data['nb_people'] : null;
        $this->surface = (isset($data['surface'])) ? $data['surface'] : null;
        $this->nb_floors = (isset($data['nb_floors'])) ? $data['nb_floors'] : null;
        $this->address_street = (isset($data['address_street'])) ? $data['address_street'] : null;
        $this->address_complt = (isset($data['address_complt'])) ? $data['address_complt'] : null;
        $this->address_post_office_box = (isset($data['address_post_office_box'])) ? $data['address_post_office_box'] : null;
        $this->address_zip = (isset($data['address_zip'])) ? $data['address_zip'] : null;
        $this->address_city = (isset($data['address_city'])) ? $data['address_city'] : null;
        $this->address_state = (isset($data['address_state'])) ? $data['address_state'] : null;
        $this->address_country = (isset($data['address_country'])) ? $data['address_country'] : null;
//        $this->is_open = (isset($data['is_open'])) ? $data['is_open'] : null;
        $this->disabled_workers = (isset($data['disabled_workers'])) ? $data['disabled_workers'] : null;
        $this->availability = (isset($data['availability'])) ? $data['availability'] : null;
        $this->security = (isset($data['security'])) ? $data['security'] : null;
        $this->lift = (isset($data['lift'])) ? $data['lift'] : null;
        $this->parking = (isset($data['parking'])) ? $data['parking'] : null;
        $this->comment = (isset($data['comment'])) ? $data['comment'] : null;
        
        // Additional fields
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;
        
        
        
        //Import champs
        $this->site_id = (isset($data['site_id'])) ? $data['site_id'] : null;
        $this->raison_sociale_livraison = (isset($data['raison_sociale_livraison'])) ? $data['raison_sociale_livraison'] : null;
        $this->siret_livraison = (isset($data['siret_livraison'])) ? $data['siret_livraison'] : null;
        $this->adresse = (isset($data['adresse'])) ? $data['adresse'] : null;
        $this->code_postal = (isset($data['code_postal'])) ? $data['code_postal'] : null;
        $this->ville = (isset($data['ville'])) ? $data['ville'] : null;
        $this->zone_geographique = (isset($data['zone_geographique'])) ? $data['zone_geographique'] : null;
        $this->siret_facturation = (isset($data['siret_facturation'])) ? $data['siret_facturation'] : null;
        $this->entite_facturation = (isset($data['entite_facturation'])) ? $data['entite_facturation'] : null;
        $this->effectif = (isset($data['effectif'])) ? $data['effectif'] : null;
        $this->superficie = (isset($data['superficie'])) ? $data['superficie'] : null;
        $this->nombre_etages = (isset($data['nombre_etages'])) ? $data['nombre_etages'] : null;
        $this->telephone_site = (isset($data['telephone_site'])) ? $data['telephone_site'] : null;
        $this->horaires_logistique = (isset($data['horaires_logistique'])) ? $data['horaires_logistique'] : null;
        $this->contraintes_logistique = (isset($data['contraintes_logistique'])) ? $data['contraintes_logistique'] : null;
        $this->accessibilite_livraison = (isset($data['accessibilite_livraison'])) ? $data['accessibilite_livraison'] : null;
        $this->nom_contact_livraison = (isset($data['nom_contact_livraison'])) ? $data['nom_contact_livraison'] : null;
        $this->tel_contact_livraison = (isset($data['tel_contact_livraison'])) ? $data['tel_contact_livraison'] : null;
        $this->email_contact_livraison = (isset($data['email_contact_livraison'])) ? $data['email_contact_livraison'] : null;
        $this->nom_contact_livraison2 = (isset($data['nom_contact_livraison2'])) ? $data['nom_contact_livraison2'] : null;
        $this->tel_contact_livraison2 = (isset($data['tel_contact_livraison2'])) ? $data['tel_contact_livraison2'] : null;
        $this->email_contact_livraison2 = (isset($data['email_contact_livraison2'])) ? $data['email_contact_livraison2'] : null;
        
    }

 public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

   public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
            		'name'     => 'csrf',
            		'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
            		'name'     => 'id',
            		'required' => false,
            		 
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'caption',
            		'required' => true,
            		'filters'  => array(
            				array('name' => 'StripTags'),
            				array('name' => 'StringTrim'),
            		),
            		'validators' => array(
            				array(
            						'name'    => 'StringLength',
            						'options' => array(
            								'encoding' => 'UTF-8',
            								'min'      => 1,
            								'max'      => 255,
            						),
            				),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'description',
            		'required' => FALSE,
            		'filters'  => array(
            				array('name' => 'StripTags'),
            				array('name' => 'StringTrim'),
            		),
            		'validators' => array(
            				array(
            						'name'    => 'StringLength',
            						'options' => array(
            								'encoding' => 'UTF-8',
            								'min'      => 1,
            								'max'      => 2047,
            						),
            				),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'nb_people',
            		'required' => false,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));

            $inputFilter->add($factory->createInput(array(
            		'name'     => 'surface',
            		'required' => false,
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'nb_floors',
            		'required' => false,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));
            
            $this->inputFilter = $inputFilter;
        }
                
        return $this->inputFilter;
    }
}
    