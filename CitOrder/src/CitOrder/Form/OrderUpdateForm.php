<?php
namespace CitOrder\Form;

use Zend\Form\Form;

use CitOrder\Model\Vcard;

class OrderUpdateForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('order');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

    	$this->add(
    			array(
    					'type' => 'Zend\Form\Element\Date',
    					'name' => 'order_date',
    					'options' => array(
    							'label' => '* Order date',
    					),
    					'attributes' => array(
    							'id' => 'order_date',
    							'min' => '2010-01-01',
    							'max' => '2999-01-01',
    							'step' => '1',
    					)
    			));
    	$this->get('order_date')->setValue(date('Y-m-d'));
        
        $this->add(array(
        		'name' => 'caption',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        		),
        		'options' => array(
        				'label' => 'Caption',
        		),
        ));
        
        $this->add(array(
        		'name' => 'description',
        		'type'  => 'textarea',
        		'attributes' => array(
        				'rows' => 5,
        				'cols' => 100,
        		),
        		'options' => array(
        				'label' => 'Description',
        		),
        ));
        
        $this->add(array(
        		'type' => 'Zend\Form\Element\Number',
        		'name' => 'nb_people',
        		'options' => array(
        				'label' => 'Number of people'
        		),
        		'attributes' => array(
        				'id'  => 'nb_people',
        				'min' => '0',
        				'max' => '99999',
        				'step' => '1', // default step interval is 1
        		)
        ));
        
        $this->add(array(
        		'type' => 'Zend\Form\Element\Number',
        		'name' => 'surface',
        		'options' => array(
        				'label' => 'Surface (m2)'
        		),
        		'attributes' => array(
        				'id'  => 'surface',
        				'min' => '0',
        				'max' => '99999',
        				'step' => '0.01', // default step interval is 1
        		)
        ));
        
    	$this->add(array(
        		'type' => 'Zend\Form\Element\Number',
        		'name' => 'nb_floors',
        		'options' => array(
        				'label' => 'Number of floors'
        		),
        		'attributes' => array(
        				'id'  => 'nb_floors',
        				'min' => '0',
        				'max' => '99999',
        				'step' => '1', // default step interval is 1
        		)
        ));

    	$this->add(array(
    			'name' => 'comment',
    			'type'  => 'textarea',
    			'attributes' => array(
    					'rows' => 5,
    					'cols' => 100,
    			),
    			'options' => array(
    					'label' => 'Comment',
    			),
    	));
        
        $this->add(
        		array(
        				'type' => 'Zend\Form\Element\Date',
        				'name' => 'initial_hoped_delivery_date',
        				'options' => array(
        						'label' => '* Hoped delivery date',
        				),
        				'attributes' => array(
        						'id' => 'initial_hoped_delivery_date',
        						'min' => '2010-01-01',
        						'max' => '2999-01-01',
        						'step' => '1',
        				)
        		));
        $this->get('initial_hoped_delivery_date')->setValue(date('Y-m-d'));
        
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
/*
        $this->add(array(
        		'name' => 'responsible_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));*/

        $this->add(array(
        		'name' => 'site_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        $this->add(array(
        		'name' => 'identifier',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'accounting_identifier',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        $this->add(array(
        		'name' => 'retraction_limit',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        $this->add(array(
        		'name' => 'instance_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'status',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
    }
}
