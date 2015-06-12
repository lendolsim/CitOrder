<?php
namespace CitOrder\Form;

use Zend\Form\Form;

class OrderProductOptionForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('order_product_option');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }
    
    public function addElements($productOptions)
    {
        foreach($productOptions as $productOption) {

	        $this->add(array(
	        		'name' => 'option'.$productOption->id,
	        		'type' => 'Checkbox',
	        		'attributes' => array(
	        				'id' => 'option'.$productOption->id,
			        		'onchange' => 'check'.$productOption->id.'()'
	        		),
	        		'options' => array(
	        				'label' => NULL,
	        				'use_hidden_element' => true,
	        				'checked_value' => '1',
	        				'unchecked_value' => '0'
	        		)
	        ));
        }
        
        $this->add(array(
			'name' => 'submit',
 			'attributes' => array(
				'type'  => 'submit',
				'value' => 'Update',
				'id' => 'submitbutton',
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

        $this->add(array(
        		'name' => 'order_product_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'product_option_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
    }
}
