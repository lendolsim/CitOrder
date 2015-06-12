<?php
namespace CitOrder\Form;

use Zend\Form\Form;

use CitOrder\Model\Vcard;

class OrderWithdrawalIndexForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('order_withdrawal');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
    }
    
    public function addElements($stocks, $order_id){

    	foreach ($stocks as $stock) {
			if (!$stock->order_id || $stock->order_id == $order_id) {
	    		
		        $this->add(array(
		        		'name' => 'stock'.$stock->id,
		        		'type' => 'Checkbox',
		        		'attributes' => array(
		        				'id' => 'stock'.$stock->id,
		        		),
		        		'options' => array(
		        				'label' => NULL,
		        				'use_hidden_element' => true,
		        				'checked_value' => '1',
		        				'unchecked_value' => '0'
		        		)
		        ));
		        if ($stock->order_id == $order_id) $this->get('stock'.$stock->id)->setValue(1);
			}
       	}

       	$this->add(array(
       			'name' => 'check_all',
       			'type' => 'Checkbox',
       			'attributes' => array(
       					'id' => 'check_all',
       					'onchange' => 'checkAll()'
       			),
       			'options' => array(
       					'label' => NULL,
       					'use_hidden_element' => true,
       					'checked_value' => '1',
       					'unchecked_value' => '0'
       			)
       	));
       	
        $this->add(array(
			'name' => 'withdraw',
 			'attributes' => array(
				'type'  => 'submit',
				'value' => 'Update',
				'id' => 'withdraw',
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
    }
}
