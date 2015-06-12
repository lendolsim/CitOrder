<?php
namespace CitOrder\Form;

use Zend\Form\Form;

use CitOrder\Model\Vcard;

class OrderWithdrawalForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('order_withdrawal');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }
    
    public function addElements($siteStocks){

		if ($siteStocks) {
	    	$captions = array();
	    	foreach ($siteStocks as $siteStock) $captions[$siteStock->stock_id] = $siteStock->caption;
	    	$this->add(
	    			array(
	    					'name' => 'site_stock_id',
	    					'type' => 'Select',
	    					'attributes' => array(
	    							'id'    => 'site_stock_id',
	    							'onchange' => 'displayIdentifier()'
	    					),
	    					'options' => array(
	    							'label' => '* Stock',
	    							'value_options' => $captions,
	    							'empty_option'  => '--- Selectionnez  ---'
	    					),
	    			)
	    	);
		}
		else {
			$this->add(array(
					'name' => 'site_stock_id',
					'attributes' => array(
							'type'  => 'hidden',
					),
			));
		}
    	
    	$this->add(array(
    			'name' => 'brand',
    			'attributes' => array(
    					'type'  => 'text',
    					'size'  => '255',
    					'id' => 'brand'
    			),
    			'options' => array(
    					'label' => 'Brand',
    			),
    	));
    	$this->get('brand')->setAttribute('disabled', 'disabled');
    	
    	$this->add(array(
    			'name' => 'model',
    			'attributes' => array(
    					'type'  => 'text',
    					'size'  => '255',
    					'id' => 'model'
    			),
    			'options' => array(
    					'label' => 'Model',
    			),
    	));
    	$this->get('model')->setAttribute('disabled', 'disabled');

    	$this->add(array(
    			'name' => 'identifier',
    			'attributes' => array(
    					'type'  => 'text',
    					'size'  => '255',
    					'id' => 'identifier'
    			),
    			'options' => array(
    					'label' => 'Identifier',
    			),
    	));
    	$this->get('identifier')->setAttribute('disabled', 'disabled');

    	$this->add(array(
    			'name' => 'serial_number',
    			'attributes' => array(
    					'type'  => 'text',
    					'size'  => '255',
    					'id' => 'serial_number'
    			),
    			'options' => array(
    					'label' => 'Serial number',
    			),
    	));
    	$this->get('serial_number')->setAttribute('disabled', 'disabled');
        
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
        		'name' => 'order_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
    }
}
