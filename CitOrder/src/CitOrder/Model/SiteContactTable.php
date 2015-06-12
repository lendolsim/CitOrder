<?php
namespace CitOrder\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Session\Container;

class SiteContactTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function getAdapter()
    {
    	return $this->tableGateway->getAdapter();
    }
    
    public function getSelect()
    {
		$select = new \Zend\Db\Sql\Select();
	    $select->from($this->tableGateway->getTable());
    	return $select;
    }

    public function selectWith($select)
    {
//    	throw new \Exception($select->getSqlString($this->getAdapter()->getPlatform()));
    	return $this->tableGateway->selectWith($select);
    }
    
    public function selectWithAsArray($select)
    {
    	$statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($select);
    	$resultSet = $statement->execute();
    	return $resultSet;
    }

    public function fetchDistinct($column)
    {
		$select = new \Zend\Db\Sql\Select();
    	$select->from($this->tableGateway->getTable())
			   ->columns(array($column))
    		   ->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT);
		return $this->tableGateway->selectWith($select);
    }
    
    public function get($id, $column = 'id')
    {
    	$id  = (int) $id;
    	$rowset = $this->tableGateway->select(array($column => $id));
    	$row = $rowset->current();
    	if (!$row) {
    		throw new \Exception("Could not find row $id");
    	}
    	return $row;
    }

    public function save($entity, $user)
    {
    	$data = array();

    	// Specific
    	$data['site_id'] = $entity->site_id;
		$data['contact_id'] = $entity->contact_id;
		$data['is_main_contact'] = $entity->is_main_contact;
		
		$data['instance_id'] = $user->instance_id;
		$data['update_time'] = date("Y-m-d H:i:s");
		$data['update_user'] = $user->user_id;
        $id = (int)$entity->id;
        if ($id == 0) {
        	$data['creation_time'] = date("Y-m-d H:i:s");
        	$data['creation_user'] = $user->user_id;
        	$this->tableGateway->insert($data);
        	return $this->getAdapter()->getDriver()->getLastGeneratedValue();
        } else {
            if ($this->get($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function update($data, $where, $user)
    {
    	$data['instance_id'] = $user->instance_id;
    	$data['update_time'] = date("Y-m-d H:i:s");
    	$data['update_user'] = $user->user_id;
    	$this->tableGateway->update($data, $where);
    }
    
    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }

    public function multipleDelete($where)
    {
        $this->tableGateway->delete($where);
    }
}
