<?php
namespace CitOrder\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;


class Order implements InputFilterAwareInterface
{
    public $id;
/*    public $responsible_id;
    public $approver_id;*/
	public $site_id;
	public $order_date;
	public $identifier;
	public $accounting_identifier;
	public $caption;
	public $description;
    public $nb_people;
    public $surface;
    public $nb_floors;
    public $comment;
	public $retraction_limit;
    public $issue_date;
    public $retraction_date;
	public $initial_hoped_delivery_date;
/*	public $current_hoped_delivery_date;
	public $management_date;
	public $expected_delivery_date;
	public $actual_delivery_date;*/
	public $finalized_order_date;
    public $status;

    // Additional field (not in database)
    public $site_caption;
    public $vat_rate;
/*    public $delegatee_id;
    public $delegation_begin;
    public $delegation_end;
    public $responsible_n_fn;
    public $approver_n_fn;*/
    
    protected $inputFilter;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
/*        $this->responsible_id = (isset($data['responsible_id'])) ? $data['responsible_id'] : null;
        $this->approver_id = (isset($data['approver_id'])) ? $data['approver_id'] : null;*/
        $this->site_id = (isset($data['site_id'])) ? $data['site_id'] : null;
        $this->order_date = (isset($data['order_date'])) ? $data['order_date'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->accounting_identifier = (isset($data['accounting_identifier'])) ? $data['accounting_identifier'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->nb_people = (isset($data['nb_people'])) ? $data['nb_people'] : null;
        $this->surface = (isset($data['surface'])) ? $data['surface'] : null;
        $this->nb_floors = (isset($data['nb_floors'])) ? $data['nb_floors'] : null;
        $this->previous_comment = (isset($data['previous_comment'])) ? $data['previous_comment'] : null;
        $this->comment = (isset($data['comment'])) ? $data['comment'] : null;
        $this->retraction_limit = (isset($data['retraction_limit'])) ? $data['retraction_limit'] : null;
        $this->issue_date = (isset($data['issue_date'])) ? $data['issue_date'] : null;
        $this->retraction_date = (isset($data['retraction_date'])) ? $data['retraction_date'] : null;
        $this->initial_hoped_delivery_date = (isset($data['initial_hoped_delivery_date'])) ? $data['initial_hoped_delivery_date'] : null;
/*        $this->current_hoped_delivery_date = (isset($data['current_hoped_delivery_date'])) ? $data['current_hoped_delivery_date'] : null;
        $this->management_date = (isset($data['management_date'])) ? $data['management_date'] : null;
        $this->expected_delivery_date = (isset($data['expected_delivery_date'])) ? $data['expected_delivery_date'] : null;
        $this->actual_delivery_date = (isset($data['actual_delivery_date'])) ? $data['actual_delivery_date'] : null;*/
        $this->finalized_order_date = (isset($data['finalized_order_date'])) ? $data['finalized_order_date'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;

        $this->site_caption = (isset($data['site_caption'])) ? $data['site_caption'] : null;
        $this->vat_rate = (isset($data['vat_rate'])) ? $data['vat_rate'] : null;
/*        $this->delegatee_id = (isset($data['delegatee_id'])) ? $data['delegatee_id'] : null;
        $this->delagation_begin = (isset($data['delegation_begin'])) ? $data['delegation_begin'] : null;
        $this->delagation_end = (isset($data['delegation_end'])) ? $data['delegation_end'] : null;*/
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
/*
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'approver_id',
            		'required' => false,
            )));*/
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'caption',
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
            		'name'     => 'identifier',
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
            		'name'     => 'new_comment',
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
            		'name'     => 'surface',
            		'required' => false,
            		 
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'nb_people',
            		'required' => false,
            		 
            )));
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'nb_floors',
            		'required' => false,
            		 
            )));
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'retraction_limit',
            		'required' => false,
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'retraction_date',
            		'required' => false,
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'current_hoped_delivery_date',
            		'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'management_date',
            		'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'expected_delivery_date',
            		'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'actual_delivery_date',
            		'required' => false,
            )));
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'finalized_order_date',
            		'required' => false,
            )));           
            
            $this->inputFilter = $inputFilter;
        }
                
        return $this->inputFilter;
    }
}
    