<?php
namespace CitOrder\Form;

use Zend\Form\Form;

class OrderProductIndexForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('order_product_index');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }
    
    public function addElements($orderProducts, $contacts)
    {
        foreach($orderProducts as $orderProduct) {

	        $this->add(array(
	        		'name' => 'product'.$orderProduct->id,
	        		'type' => 'Checkbox',
	        		'attributes' => array(
	        				'id' => 'product'.$orderProduct->id,
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
        		'name' => 'check_all',
        		'type' => 'Checkbox',
        		'attributes' => array(
        				'id' => 'check_all',
        				'onchange' => 'checkAll()'
        		),
        		'options' => array(
        				'label' => NULL,
        				'use_hidden_element' => true,
        				'checked_value' => '1',
        				'unchecked_value' => '0'
        		)
        ));
        
        $this->add(
        		array(
        				'name' => 'contact_id',
        				'type' => 'Select',
        				'attributes' => array(
        						'id'    => 'contact_id'
        				),
        				'options' => array(
        						'label' => 'Contact',
        						'value_options' => $contacts,
        						'empty_option'  => '--- Please choose ---'
        				),
        		)
        );

        $this->add(
        		array(
        				'type' => 'Zend\Form\Element\Date',
        				'name' => 'hoped_delivery_date',
        				'options' => array(
        						'label' => 'Hoped delivery date',
        				),
        				'attributes' => array(
        						'id' => 'hoped_delivery_date',
        						'min' => '2010-01-01',
        						'max' => '2999-01-01',
        						'step' => '1',
        				)
        		)
        );
        $this->get('hoped_delivery_date')->setValue(date('Y-m-d'));

        $this->add(array(
        		'name' => 'building',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '20',
        				'placeholder' => 'Building'
        		),
        		'options' => array(
        				'label' => 'Building',
        		),
        ));

        $this->add(array(
        		'name' => 'floor',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '20',
        				'placeholder' => 'Floor'
        		),
        		'options' => array(
        				'label' => 'Floor',
        		),
        ));

        $this->add(array(
        		'name' => 'department',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '20',
        				'placeholder' => 'Department'
        		),
        		'options' => array(
        				'label' => 'Department',
        		),
        ));

        $this->add(array(
        		'name' => 'comment',
        		'attributes' => array(
        				'type'  => 'textarea',
        				'rows' => 3,
        				'cols' => 20,
        				'placeholder' => 'Place description'
        		),
        		'options' => array(
        				'label' => 'Comment',
        		),
        ));
        
        $this->add(array(
			'name' => 'update_contact',
 			'attributes' => array(
				'type'  => 'submit',
				'value' => 'Set contact',
				'id' => 'update_contact',
			),
		));

        $this->add(array(
        		'name' => 'update_hoped_delivery_date',
        		'attributes' => array(
        				'type'  => 'submit',
        				'value' => 'Set hoped delivery date',
        				'id' => 'update_hoped_delivery_date',
        		),
        ));

        $this->add(array(
        		'name' => 'update_destination',
        		'attributes' => array(
        				'type'  => 'submit',
        				'value' => 'Set destination',
        				'id' => 'update_destination',
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
        		'name' => 'order_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'product_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'price',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'delivery_date',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'connection_date',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'validation_date',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
    }
}
