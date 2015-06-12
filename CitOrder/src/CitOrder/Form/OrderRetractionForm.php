<?php
namespace CitOrder\Form;

use Zend\Form\Form;

use CitOrder\Model\Vcard;

class OrderRetractionForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('order');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');

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
			'name' => 'retraction',
 			'attributes' => array(
				'type'  => 'submit',
				'value' => 'Retraction',
				'id' => 'retraction',
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
        ));

        $this->add(array(
        		'name' => 'approver_id',
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
        		'name' => 'description',
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
        		'name' => 'issue_date',
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
        		'name' => 'retraction_date',
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
