<?php
namespace CitOrder\Form;

use Zend\Form\Form;

use CitOrder\Model\Vcard;

class OrderProductUpdateForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('order_product');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }
    
    public function addElements($contacts){

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
    							'label' => 'Hoped delivery',
    					),
    					'attributes' => array(
    							'id' => 'hoped_delivery_date',
    							'min' => '2010-01-01',
    							'max' => '2999-01-01',
    							'step' => '1',
    					)
    			)
    	);
    	
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
    					'placeholder' => 'Comment'
    			),
    			'options' => array(
    					'label' => 'Comment',
    			),
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
    }
}
