<?php
namespace CitOrder\Form;

use Zend\Form\Form;

use CitOrder\Model\Vcard;

class SiteFormResp extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('site');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }
    
    public function addElements($contacts){

    	$this->add(array(
    			'name' => 'caption',
    			'attributes' => array(
    					'type'  => 'hidden',
    					'size'  => '255',
    					
    			),
    			'options' => array(
    					'label' => ' Caption',
    			),
    	));
    	 
      $this->add(array(
        		'name' => 'description',
        		'type'  => 'textarea',
        		'attributes' => array(
        				'rows' => 5,
        				'cols' => 100,
        				'type'  => 'hidden',
        		),
        		'options' => array(
        				'label' => 'Description',
        		),
        )); 

        $this->add(array(
        		'name' => 'region',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        				'type'  => 'hidden',
        		),
        		'options' => array(
        				'label' => 'Region',
        		),
        ));
       
        $this->add(
        		array(
        				'name' => 'contact_id',
        				'type' => 'Select',
        				'attributes' => array(
        						'id'    => 'contact_id',
        						'type'  => 'hidden',
        				),
        				'options' => array(
        						'label' => '* Main contact',
        						'value_options' => $contacts,
        						'empty_option'  => '--- Selectionnez un contact ---'
        				),
        		)
        );
        
        $this->add(
        		array(
        				'name' => 'type',
        				'type' => 'Select',
        				'attributes' => array(
        						'id'    => 'type' ,
        						'type'  => 'hidden',
        				),
        				'options' => array(
        						'label' => ' Type',
        						'value_options' => array ('1'=>'type 1 ','2'=>'type 2'),
        						'empty_option'  => '--- Veuillez choisir ---'
        				),
        		)
        );

        $this->add(array(
        		'type' => 'Zend\Form\Element\Number',
        		'name' => 'nb_people',
        		'options' => array(
        				'label' => 'Number of people'
        		),
        		'attributes' => array(
        				'type'  => 'hidden',
        				'min' => '0',
        				'max' => '99999',
        				'step' => '1', // default step interval is 1
        		)
        ));
        
        $this->add(array(
        		'type' => 'Zend\Form\Element\Number',
        		'name' => 'surface',
        		'options' => array(
        				'label' => 'Surface'
        		),
        		'attributes' => array(
        				'type'  => 'hidden',
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
        				'type'  => 'hidden',
        				'min' => '0',
        				'max' => '99999',
        				'step' => '1', // default step interval is 1
        		)
        ));

        
        
        $this->add(array(
        		'name' => 'address_street',
        		'attributes' => array(
        				'type'  => 'hidden',
        				'size'  => '255',
        		),
           		'options' => array(
        				'label' => 'Address - street',
        		),
        ));

        $this->add(array(
        		'name' => 'address_complt',
        		'attributes' => array(
        				'type'  => 'hidden',
        				'size'  => '255',
        		),
           		'options' => array(
        				'label' => 'Address - extended',
        		),
        ));

        $this->add(array(
        		'name' => 'address_zip',
        		'attributes' => array(
        				'type'  => 'hidden',
        				'size'  => '255',
        		),
          		'options' => array(
        				'label' => 'Address - zip',
        		),
        ));

        $this->add(array(
        		'name' => 'address_city',
        		'attributes' => array(
        				'type'  => 'hidden',
        				'size'  => '255',
        		),
           		'options' => array(
        				'label' => 'Address - city',
        		),
        ));
/*
        $this->add(array(
        		'name' => 'address_state',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        		),
           		'options' => array(
        				'label' => 'State',
        		),
        ));*/
        
        $this->add(array(
        		'name' => 'address_country',
        		'attributes' => array(
        				'type'  => 'hidden',
        				'size'  => '255',
        		),
        		'options' => array(
        				'label' => 'Address - country',
        		),
        ));
        
        $this->add(array(
        		'name' => 'disabled_workers',
        		'attributes' => array(
        				'type'  => 'disabled_workers',
        				'size'  => '255',
        		),
        		'options' => array(
        				'label' => 'Disabled workers',
        		),
        ));
        
        $this->add(array(
        		'name' => 'availability',
        		'attributes' => array(
        				'type'  => 'availability',
        				'size'  => '255',
        		),
        		'options' => array(
        				'label' => 'availability',
        		),
        ));
        
        $this->add(array(
        		'name' => 'security',
        		'attributes' => array(
        				'type'  => 'security',
        				'size'  => '255',
        		),
        		'options' => array(
        				'label' => 'Security',
        		),
        ));
        
        $this->add(array(
        		'name' => 'lift',
        		'attributes' => array(
        				'type'  => 'lift',
        				'size'  => '255',
        		),
        		'options' => array(
        				'label' => 'Lift',
        		),
        ));
        
        $this->add(array(
        		'name' => 'parking',
        		'attributes' => array(
        				'type'  => 'parking',
        				'size'  => '255',
        		),
        		'options' => array(
        				'label' => 'Parking',
        		),
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
