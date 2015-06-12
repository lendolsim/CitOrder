<?php
namespace CitOrder\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Stock implements InputFilterAwareInterface
{
    public $id;
    public $product_id;
    public $caption;    
    public $identifier;    
    public $nb_black_white_print;
    public $nb_color_print;

    ////Import champs 
  	public $serial_number;//Numéro de série de l'équipement
    public $brand;//Marque de l'équipement
    public $model;//Modèle de l'équipement
    public $liste_options;//Libellé et référence des options installées sur l'équipement
    public $exploitation_date;//Date de mise en exploitation de l'équipement
    public $site_id;//Numéro généré par Pole Emploi
    public $building;//Bâtiment où se trouve l'équipement
    public $floor;//Etage où se trouve l'équipement
    public $place;//Emplacement de l'équipement
    
    
    
    // Additional fields (belonging to joined tables)
    public $site_caption;
    public $order_identifier;
    public $order_id;
    protected $inputFilter;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->product_id = (isset($data['product_id'])) ? $data['product_id'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->brand = (isset($data['brand'])) ? $data['brand'] : null;
        $this->model = (isset($data['model'])) ? $data['model'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->serial_number = (isset($data['serial_number'])) ? $data['serial_number'] : null;
        $this->nb_black_white_print = (isset($data['nb_black_white_print'])) ? $data['nb_black_white_print'] : null;
        $this->nb_color_print = (isset($data['nb_color_print'])) ? $data['nb_color_print'] : null;
        $this->exploitation_date = (isset($data['exploitation_date'])) ? $data['exploitation_date'] : null;
        $this->site_id = (isset($data['site_id'])) ? $data['site_id'] : null;
        $this->building = (isset($data['building'])) ? $data['building'] : null;
        $this->floor = (isset($data['floor'])) ? $data['floor'] : null;
        $this->place = (isset($data['place'])) ? $data['place'] : null;
        $this->liste_options = (isset($data['liste_options'])) ? $data['liste_options'] : null;
        
        $this->site_caption = (isset($data['site_caption'])) ? $data['site_caption'] : null;
        $this->order_identifier = (isset($data['order_identifier'])) ? $data['order_identifier'] : null;
        $this->order_id = (isset($data['order_id'])) ? $data['order_id'] : null;
    }

    // Add content to this method:
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
            		'name'     => 'product_id',
            		'required' => false,
            )));

            $inputFilter->add($factory->createInput(array(
            		'name'     => 'caption',
            		'required' => TRUE,
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
            		'name'     => 'brand',
            		'required' => TRUE,
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
            		'name'     => 'model',
            		'required' => TRUE,
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
            		'name'     => 'identifier',
            		'required' => TRUE,
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
            		'name'     => 'serial_number',
            		'required' => TRUE,
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
            		'name'     => 'nb_black_white_print',
            		'required' => false,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));

            $inputFilter->add($factory->createInput(array(
            		'name'     => 'nb_color_print',
            		'required' => false,
            		'filters'  => array(
            				array('name' => 'Int'),
            		),
            )));

            $inputFilter->add($factory->createInput(array(
            		'name'     => 'exploitation_date',
            		'required' => true,
            )));
            
            $this->inputFilter = $inputFilter;
        }
		return $this->inputFilter;
    }
}
