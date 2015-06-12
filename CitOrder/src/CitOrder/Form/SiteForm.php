<?php
namespace CitOrder\Form;

use Zend\Form\Form;

use CitOrder\Model\Vcard;

class SiteForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('site');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }
    
    public function addElements($regions)
    {
    	$this->add(array(
    			'name' => 'caption',
    			'attributes' => array(
    					'type'  => 'text',
    					'size'  => '255',
    			),
    			'options' => array(
    					'label' => '* Caption',
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
/*
        $this->add(array(
        		'name' => 'region',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        		),
        		'options' => array(
        				'label' => 'Region',
        		),
        ));*/

      $captions = array();
      foreach ($regions as $region) $captions[$region->id] = $region->caption;
      $this->add(
      		array(
      				'name' => 'region_id',
      				'type' => 'Select',
      				'attributes' => array(
      						'id'    => 'region_id',
      				),
      				'options' => array(
      						'label' => '* Region',
      						'value_options' => $captions,
      						'empty_option'  => '--- Please choose ---'
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
        				'min' => '0',
        				'max' => '99999',
        				'step' => '1', // default step interval is 1
        		)
        ));

        
        
        $this->add(array(
        		'name' => 'address_street',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        		),
           		'options' => array(
        				'label' => 'Address - street',
        		),
        ));

        $this->add(array(
        		'name' => 'address_complt',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        		),
           		'options' => array(
        				'label' => 'Address - extended',
        		),
        ));

        $this->add(array(
        		'name' => 'address_post_office_box',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        		),
        		'options' => array(
        				'label' => 'Address - post office box',
        		),
        ));
        
        $this->add(array(
        		'name' => 'address_zip',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        		),
          		'options' => array(
        				'label' => 'Address - zip',
        		),
        ));

        $this->add(array(
        		'name' => 'address_city',
        		'attributes' => array(
        				'type'  => 'text',
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
        				'type'  => 'address_state',
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
        				'label' => 'Availability',
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

        $this->add(array(
        		'name' => 'contact_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
    }
}
