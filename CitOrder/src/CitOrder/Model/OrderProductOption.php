<?php
namespace CitOrder\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class OrderProductOption implements InputFilterAwareInterface
{
    public $id;
    public $order_product_id;
    public $product_option_id;
	public $price;

	// Additionnal fields (not in database)
	public $caption;
	public $description;
	public $order_id;
	
    protected $inputFilter;

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->order_product_id = (isset($data['order_product_id'])) ? $data['order_product_id'] : null;
        $this->product_option_id = (isset($data['product_option_id'])) ? $data['product_option_id'] : null;
        $this->price = (isset($data['price'])) ? $data['price'] : null;

        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->order_id = (isset($data['order_id'])) ? $data['order_id'] : null;
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
            
            $this->inputFilter = $inputFilter;
        }
                
        return $this->inputFilter;
    }
}
    