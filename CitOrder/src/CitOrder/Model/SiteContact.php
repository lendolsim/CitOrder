<?php
namespace CitOrder\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;


class SiteContact implements InputFilterAwareInterface
{
    public $id;
    public $site_id;
    public $contact_id;
    public $is_main_contact;
    
    // Additional fields (not in database)
    public $caption;
    public $n_fn;
    public $nb_people;
    public $surface;
    public $nb_floors;
    
	protected $inputFilter;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->site_id = (isset($data['site_id'])) ? $data['site_id'] : null;
        $this->contact_id = (isset($data['contact_id'])) ? $data['contact_id'] : null;
        $this->is_main_contact = (isset($data['is_main_contact'])) ? $data['is_main_contact'] : null;
        
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->n_fn = (isset($data['n_fn'])) ? $data['n_fn'] : null;      
        $this->nb_people = (isset($data['nb_people'])) ? $data['nb_people'] : null;      
        $this->surface = (isset($data['surface'])) ? $data['surface'] : null;      
        $this->nb_floors = (isset($data['nb_floors'])) ? $data['nb_floors'] : null;      
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
            		'name'     => 'site_id',
            		'required' => false,
            		 
            )));
            
            $this->inputFilter = $inputFilter;
        }
                
        return $this->inputFilter;
    }
}
    