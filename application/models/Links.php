<?php
class Links extends Model_Abstract
{
	static private $inst = null;
	
	static public function getInstance() {
		if (self::$inst == null) {
			self::$inst = new Links();
		}
		
		return self::$inst;
	}
	
	public function getLinksName()
	{
		$zds = $this->_db
			->select()
			->from($this->_name, array('link'));
			
		return $this->getCol($zds);
	}
	
	public function getLinkByName($name)
	{
		$zds = $this->_db
			->select()
			->from($this->_name)
			->where('link = ?', $name);
			
		return $this->getRow($zds);
	}
	
	public function getNotParseLink()
	{
		$zds = $this->_db
			->select()
			->from($this->_name)
			->where('parse = ?', 0);
			
		return $this->getRow($zds);
	}
	
	public function getAllSortByFreq($dir = 'asc')
	{
		$zds = $this->_db
			->select()
			->from($this->_name, array('id', 'url' => 'link', 'count', 'freq'))
			->order('freq ' . $dir);
			
		return $this->getAll($zds);
	}
	
	public function deleteAll()
	{
		$where = 'id IS NOT NULL';
		
		return parent::delete($where);
	}
}
