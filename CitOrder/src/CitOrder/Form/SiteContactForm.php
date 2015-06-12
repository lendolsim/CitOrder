<?php
namespace CitOrder\Form;

use Zend\Form\Form;

use CitOrder\Model\Vcard;


class SiteContactForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('site');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }
    
    public function addElements($contacts)
    {
        $this->add(
        		array(
        				'name' => 'contact_id',
        				'type' => 'Select',
        				'attributes' => array(
        						'id'    => 'contact_id'
        				),
        				'options' => array(
        						'label' => 'contact',
        						'value_options' => $contacts,
        						'empty_option'  => '--- Please choose ---'
        				),
        		)
        );

        $this->add(array(
        		'name' => 'is_main_contact',
        		'type' => 'Checkbox',
        		'attributes' => array(
        				'id'    => 'is_main_contact'
        		),
        		'options' => array(
        				'label' => 'Main contact ?',
        				'use_hidden_element' => true,
        				'checked_value' => '1',
        				'unchecked_value' => '0'
        		)
        ));
        
        $this->add(array(
			'name' => 'submit',
 			'attributes' => array(
				'type'  => 'submit',
				'value' => 'OK',
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
        		'type' => 'Zend\Form\Element\Number',
        		'name' => 'site_id',
        		'options' => array(
        				'label' => 'site_id'
        		),
        		'attributes' => array(
        				'type'  => 'hidden',
        				'id' => 'site_id' ,
        		)
        ));
    }
}
