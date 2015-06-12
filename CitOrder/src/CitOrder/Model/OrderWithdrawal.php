<?php
namespace CitOrder\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;


class OrderWithdrawal implements InputFilterAwareInterface
{
    public $id;
    public $order_id;
    public $stock_id;
    public $comment;
    
    // Additional field (not in database)
    public $caption;
    public $brand;
    public $model;
    public $identifier;
    public $serial_number;
    public $building;
    public $floor;
    public $place;
    
    protected $inputFilter;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->order_id = (isset($data['order_id'])) ? $data['order_id'] : null;
        $this->stock_id = (isset($data['stock_id'])) ? $data['stock_id'] : null;
        $this->comment = (isset($data['comment'])) ? $data['comment'] : null;

        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->brand = (isset($data['brand'])) ? $data['brand'] : null;
        $this->model = (isset($data['model'])) ? $data['model'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->serial_number = (isset($data['serial_number'])) ? $data['serial_number'] : null;
        $this->building = (isset($data['building'])) ? $data['building'] : null;
        $this->floor = (isset($data['floor'])) ? $data['floor'] : null;
        $this->place = (isset($data['place'])) ? $data['place'] : null;
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
            		'name'     => 'comment',
            		'required' => false,
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
            
            $this->inputFilter = $inputFilter;
        }
                        
        return $this->inputFilter;
    }
}
    