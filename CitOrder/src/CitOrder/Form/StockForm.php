<?php
namespace CitOrder\Form;

use Zend\Form\Form;

use CitOrder\Model\Vcard;

class StockForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('stock');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }
    
    public function addElements($products, $sites){
       
		$productList = array();
		foreach ($products as $product) $productList[$product->id] = $product->caption;
    	$this->add(
        		array(
        				'name' => 'product_id',
        				'type' => 'Select',
        				'attributes' => array(
        						'id'    => 'product_id',
        						'onchange' => 'displayBrandModel()'
        				),
        				'options' => array(
        						'label' => 'Product',
        						'value_options' => $productList,
        						'empty_option'  => 'Hors catalogue'
        				),
        		)
        );

    	$this->add(array(
    			'name' => 'caption',
    			'attributes' => array(
    					'type'  => 'text',
    					'size'  => '255',
    					'id' => 'caption'
    			),
    			'options' => array(
    					'label' => '* Caption',
    			),
    	));
    	 
        $this->add(array(
        		'name' => 'brand',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        				'id' => 'brand'
        		),
        		'options' => array(
        				'label' => '* Brand',
        		),
        ));
        
        $this->add(array(
        		'name' => 'model',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        				'id' => 'model'
        		),
        		'options' => array(
        				'label' => '* Model',
        		),
        ));

        $this->add(array(
        		'name' => 'identifier',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        				'id' => 'identifier'
        		),
        		'options' => array(
        				'label' => '* Identifier',
        		),
        ));

        $this->add(array(
        		'name' => 'serial_number',
        		'attributes' => array(
        				'type'  => 'text',
        				'size'  => '255',
        				'id' => 'serial_number'
        		),
        		'options' => array(
        				'label' => '* Serial number',
        		),
        ));
        
        $this->add(array(
        		'type' => 'Zend\Form\Element\Number',
        		'name' => 'nb_black_white_print',
        		'options' => array(
        				'label' => 'Number of black & white prints'
        		),
        		'attributes' => array(
        				'min' => '0',
        				'max' => '999999999',
        				'step' => '1'
        		)
        ));

        $this->add(array(
        		'type' => 'Zend\Form\Element\Number',
        		'name' => 'nb_color_print',
        		'options' => array(
        				'label' => 'Number of color prints'
        		),
        		'attributes' => array(
        				'min' => '0',
        				'max' => '999999999',
        				'step' => '1'
        		)
        ));

        $this->add(
        		array(
        				'type' => 'Zend\Form\Element\Date',
        				'name' => 'exploitation_date',
        				'options' => array(
        						'label' => '* Exploitation date'
        				),
        				'attributes' => array(
        						'min' => '1990-01-01',
        						'max' => '2099-12-31',
        						'step' => '1'
        				)
        		)
        );

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
    }
}
