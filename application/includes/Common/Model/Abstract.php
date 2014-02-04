<?php
class Model_Abstract extends Zend_Db_Table_Abstract
{
	protected $_requiredCols 		= array();
	
	
	protected function _setupTableName()
	{
		if (!$this->_name)
		{
			$this->_name = strtolower(get_class($this));
		}
		
		parent::_setupTableName();
	}
	
	/**
	 * @return Zend_Db_Select
	 */
	public function all()
	{
		$zds = $this->_db
		->select()
		->from($this->_name);
		
		return $zds;
	}
	
	/**
     * Fetches all SQL result rows as a sequential array.
     *
     * @param Zend_Db_Select $zds  An SQL SELECT statement.
     * @return array
     */
	public function getAll(Zend_Db_Select $zds = null)
	{
		if (!$zds)
		{
			$zds = $this->all();
		}
			
		return $this->_db->fetchAll($zds);
	}
	
	/**
     * Fetches all SQL result rows as an array of key-value pairs.
     *
     * The first column is the key, the second column is the
     * value.
     *
     * @param Zend_Db_Select $zds An SQL SELECT statement.
     * @return array
     */
	public function getPairs(Zend_Db_Select $zds = null)
	{
		if (!$zds)
		{
			$zds = $this->all();
		}
		
		return $this->_db->fetchPairs($zds);
	}
	
	/**
     * Fetches all SQL result rows as an associative array.
     *
     * The first column is the key, the entire row array is the
     * value.  You should construct the query to be sure that
     * the first column contains unique values, or else
     * rows with duplicate values in the first column will
     * overwrite previous data.
     *
     * @param Zend_Db_Select $zds An SQL SELECT statement.
     * @return array
     */
	public function getAssoc(Zend_Db_Select $zds = null)
	{
		if (!$zds)
		{
			$zds = $this->all();
		}
		
		return $this->_db->fetchAssoc($zds);
	}
	
	/**
     * Fetches the first column of all SQL result rows as an array.
     *
     * @param Zend_Db_Select $zds An SQL SELECT statement.
     * @return array
     */
	public function getCol(Zend_Db_Select $zds = null)
	{
		if (!$zds)
		{
			$zds = $this->all();
		}
		
		return $this->_db->fetchCol($zds);
	}
	
	/**
     * Fetches the first row of the SQL result.
     *
     * @param Zend_Db_Select $zds An SQL SELECT statement.
     * @return array
     */
	public function getRow(Zend_Db_Select $zds)
	{
		return $this->_db->fetchRow($zds);
	}
	
	 /**
     * Fetches the first column of the first row of the SQL result.
     *
     * @param Zend_Db_Select $zds An SQL SELECT statement.
     * @return string
     */
	public function getOne(Zend_Db_Select $zds)
	{
		return $this->_db->fetchOne($zds);
	}
	
	/**
     * Fetches rows by primary key.  The argument specifies one or more primary
     * key value(s). To find multiple rows by primary key, the argument must
     * be an array.
     *
     * @param int|array $id.
     * @return array
     */
	public function getById($id)
	{
		$rows = $this->find($id)->toArray();
		
		if (is_array($id))
		{
			return $rows;
		}
			
		return $rows[0];
	}
	
	/**
     * Fetches the count rows of the SQL result.
     *
     * @param Zend_Db_Select $zds An SQL SELECT statement.
     * @return string
     */
	public function getCount(Zend_Db_Select $zds = null)
	{
		if (!$zds)
		{
			$zds = $this->all();
		}
		
		if ($zds->getPart(Zend_Db_Select::GROUP))
		{
			$zds = $this->_db
			->select()
			->from($zds, new Zend_Db_Expr('COUNT(*)'));
		}
		else
		{
			$zds
			->reset(Zend_Db_Select::COLUMNS)
			->columns(new Zend_Db_Expr('COUNT(*)'));
		}
		
		return (int)$this->getOne($zds);
	}
	
	public function getPageList($page, $ipp, Zend_Db_Select $zds = null, $sort = array())
	{
		if (!$zds)
		{
			$zds = $this->all();
		}
		
		if ($sort = (array)$sort)
		{
			$by = key($sort);
			$dir = (reset($sort) == 'asc') ? 'asc' : 'desc';
			$zds->reset(Zend_Db_Select::ORDER)
			->order($by . ' ' . $dir);
		}
		
		$zdsCnt = clone $zds;
		$totalCnt = $this->getCount($zdsCnt->reset(Zend_Db_Select::LIMIT_COUNT)->reset(Zend_Db_Select::LIMIT_OFFSET)->reset(Zend_Db_Select::ORDER));
		$pageList = $this->getAll($zds->limitPage($page, $ipp));

		return array($totalCnt, $pageList);
	}
	
	public function deleteById($id = 0)
    {
    	if (!$id)
    	{
    		return false;
    	}
    	
    	$where = (is_array($id) ? 'id IN ('. implode(',', $id) . ')' : 'id = ' . (int)$id);
        return parent::delete($where);
    }

	public function insert(array $values = array())
	{
		if (!($values = $this->prepareValues($values)))
		{
			throw new Exception_Model('$values is empty', Exception_Model::ERROR_SELF);
		}
		
		parent::insert($values);
		
		return $this->_db->lastInsertId();
	}

	public function update($id, array $values = array())
	{
		if (!($values = $this->prepareValues($values, true)))
		{
			return false;
		}	
			
		return parent::update($values, $this->_db->quoteInto('id = ?', $id));
	}
	

	private function prepareValues(array $values, $update = false)
	{
		$info = $this->info();
		$data = array();
		
		for ($i = 0, $sz = count($info['cols']); $i < $sz; $i++)
		{
			$colName = $info['cols'][$i];
			$metadata = $info['metadata'][$colName];
			$required = false;
			
			if (!$update)
			{
				if ($metadata['PRIMARY'])
				{
					if (!$metadata['IDENTITY'])
					{
						$required = true;
					}
				}
				else
				{
					if (in_array($colName, $this->_requiredCols))
					{
						$required = true;
					}
				}
			}
			
			if (isset($values[$colName]))
			{
				$value = $values[$colName];
				
				if (is_object($value) && @get_class($value) == 'Zend_Db_Expr')
				{
					$data[$colName] = $value;
				}
				else
				{
					switch (strtolower($metadata['DATA_TYPE']))
					{
						case 'varchar':
						case 'text':
						case 'tinytext':
						case 'mediumtext':
						case 'longtext':
						case 'enum':
						case 'set':
							$data[$colName] = trim($value);
							break;
							
						case 'date':
						case 'datetime':
						case 'time':
						case 'year':
							$data[$colName] = trim($value);
							break;
						
						case 'tynyint':
						case 'smallint':
						case 'mediumint':
						case 'int':
						case 'bigint':
						case 'timestamp':
						case 'timestamp':
							$data[$colName] = (int)$value;
							break;
						
						case 'decimal':
						case 'float':
						case 'real':
						case 'double':
							$data[$colName] = (float)$value;
							break;
						
						default:
							$data[$colName] = $value;
							break;
					}
					
					if($data[$colName] === null)
					{
						throw new Exception_Model($colName, Exception_Model::ERROR_INVALID);
					}
				}
			}
			else if ($required)
			{
				throw new Exception_Model($colName, Exception_Model::ERROR_SPECIFIED);
			}
			else if (in_array(strtolower($metadata['DATA_TYPE']), array('date', 'datetime', 'time','year')) && !$update)
			{
				$data[$colName] = new Zend_Db_Expr('NOW()');
			}
		}

		return $data;
	}

}