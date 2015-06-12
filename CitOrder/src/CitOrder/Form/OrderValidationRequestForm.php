<?php
namespace CitOrder\Form;

use Zend\Form\Form;

use CitOrder\Model\Vcard;

class OrderValidationRequestForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('order');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
/*    }
    
    public function addElements($approvers){

    	$this->add(
    			array(
    					'name' => 'approver_id',
    					'type' => 'Select',
    					'attributes' => array(
    							'id'    => 'approver_id'
    					),
    					'options' => array(
    							'label' => 'Notify an approver',
    							'value_options' => $approvers,
    							'empty_option'  => '--- Please choose ---'
    					),
    			)
    	);*/

    	$this->add(
    			array(
    					'type' => 'Zend\Form\Element\Date',
    					'name' => 'retraction_limit',
    					'options' => array(
    							'label' => 'Retraction limit',
    					),
    					'attributes' => array(
    							'id' => 'retraction_limit',
    							'min' => '2010-01-01',
    							'max' => '2999-01-01',
    							'step' => '1',
    					)
    			));

    	$this->add(array(
    			'name' => 'new_comment',
    			'type'  => 'textarea',
    			'attributes' => array(
    					'rows' => 5,
    					'cols' => 100,
    			),
    			'options' => array(
    					'label' => 'Your comment',
    			),
    	));
    	 
        $this->add(array(
			'name' => 'validation_request',
 			'attributes' => array(
				'type'  => 'submit',
				'value' => 'Request validation',
				'id' => 'validation_request',
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
        		'name' => 'instance_id',
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
        		'name' => 'order_date',
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
        		'name' => 'caption',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'comment',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        $this->add(array(
        		'name' => 'description',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'nb_people',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'surface',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'nb_floors',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));

        $this->add(array(
        		'name' => 'initial_hoped_delivery_date',
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
