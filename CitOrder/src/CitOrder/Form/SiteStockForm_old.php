<?php
namespace CitOrder\Form;

use Zend\Form\Form;

use CitOrder\Model\Vcard;

class SiteStockForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('site_stock');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }
    
    public function addElements($sites){
       
		$siteList = array();
		foreach ($sites as $site) $siteList[$site->id] = $site->caption;
    	$this->add(
        		array(
        				'name' => 'site_id',
        				'type' => 'Select',
        				'attributes' => array(
        						'id'    => 'site_id'
        				),
        				'options' => array(
        						'label' => '* Site',
        						'value_options' => $siteList,
        						'empty_option'  => '--- Please choose ---'
        				),
        		)
        );

        $this->add(array(
        		'name' => 'building',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        				'id' => 'building'
        		),
        		'options' => array(
        				'label' => 'Building',
        		),
        ));

        $this->add(array(
        		'name' => 'floor',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        				'id' => 'floor'
        		),
        		'options' => array(
        				'label' => 'Floor',
        		),
        ));

        $this->add(array(
        		'name' => 'place',
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
        		'name' => 'stock_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
    }
}
