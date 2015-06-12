<?php
namespace CitOrder\Form;

use Zend\Form\Form;

use CitOrder\Model\Vcard;

class OrderProductAddForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('order_product');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }
    
    public function addElements($products){
       
		$productList = array();
		foreach ($products as $product) $productList[$product->id] = $product->caption;
    	$this->add(
        		array(
        				'name' => 'product_id',
        				'type' => 'Select',
        				'attributes' => array(
        						'id'    => 'product_id',
        						'onchange' => 'displayBrandModel()'
        				),
        				'options' => array(
        						'label' => 'Product',
        						'value_options' => $productList,
        						'empty_option'  => '--- Please choose ---'
        				),
        		)
        );
    	 
        $this->add(array(
        		'name' => 'brand',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        				'id' => 'brand'
        		),
        		'options' => array(
        				'label' => 'Brand',
        		),
        ));
        $this->get('brand')->setAttribute('disabled', 'disabled');
        
        $this->add(array(
        		'name' => 'model',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        				'id' => 'model'
        		),
        		'options' => array(
        				'label' => 'Model',
        		),
        ));
        $this->get('model')->setAttribute('disabled', 'disabled');

        $this->add(array(
        		'name' => 'price',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        				'id' => 'price'
        		),
        		'options' => array(
        				'label' => 'Price',
        		),
        ));
        $this->get('price')->setAttribute('disabled', 'disabled');
        
        $this->add(array(
        		'type' => 'Zend\Form\Element\Number',
        		'name' => 'quantity',
        		'options' => array(
        				'label' => 'Order quantity'
        		),
        		'attributes' => array(
        				'min' => '0',
        				'max' => '999',
        				'step' => '1'
        		)
        ));
        
        $this->add(array(
			'name' => 'submit',
 			'attributes' => array(
				'type'  => 'submit',
				'value' => 'update',
				'id' => 'submit',
			),
		));
        
        // Champs cachés
        $this->add(
            array(
                'name' => 'csrf',
                'type' => 'Csrf',
                'options' => array(
                    'csrf_options' => array(
                        'timeout' => 600
                    )
                )
            )
        );

        $this->add(array(
        		'name' => 'id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
    }
}
