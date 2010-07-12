<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Menu Navigation model
 * 
 * @author		Liran Tal <liran.tal@gmail.com>
 * @package		daloRADIUS
 * @subpackage	Menu Navigation module for CodeIgniter
 * @copyright	GPLv2
 *
 */
class Menu_m extends Model {
	
	
	public function __construct()
	{
		parent::__construct();
		
	}
	
	
	/**
	 * Recursively loops through the menu navigation entries in the database
	 * to return the navigation tree 
	 * 
	 * @return		array		array representation of the navigation tree
	 */
	public function getMenu()
	{
		$tree = array();
		
		$query = $this->db->get_where('menu', array('parent_id' => 0));
		foreach($query->result_array() as $child) {
			$tree[$child['title']] = $this->getChildsRecurs($child['id']);			
		}
		
		return $tree;
		
	}
	
	
	/**
	 * Add a menu entry
	 * 
	 * @param		array		associative array of fields and their values to insert
	 * @return		int			id of the inserted record or false on error
	 */
	public function addMenuEntry($data)
	{
				
		if ($data) {
			if ($this->db->insert('menu', $data) === false)
				return false;
			else
				return $this->db->insert_id();			
		} else
			return false;
	}
	
	
	/**
	 * Adds a menu entry for the parent_id menu entry.
	 * If parent_id provided is a string it will match against the commonname entry
	 * 
	 * To force the parent entry to be created it's required to pass the parent menu
	 * entry as an array with it's settings (including the parent_id key) and set force
	 * to true
	 * 
	 * @param		array			associative array of the menu entry 
	 * @param		int or array	either int of the parent id for this entry or the
	 * @param		bool			true for forcing the creation of the parent menu entry
	 * 								if it doesn't exist yet	
	 * @return		int 
	 */
	public function addMenuEntryForParent($data, $parent_id, $force = false)
	{
				
		if (!$data)
			return false;
			
		if (is_int($parent_id)) {
			$data['parent_id'] = $parent_id;
		} else if (is_string($parent_id)) {
			$query = $this->getMenuEntry($parent_id);
			$data['parent_id'] = $query['id'];
		} else if (is_array($parent_id) && $force === true) {
			$parent = &$parent_id;
			$existing_parent = $this->getMenuEntry($parent['commonname']);
			if ($existing_parent === false) {
				$data['parent_id'] = $this->addMenuEntry($parent);
			} else {
				$data['parent_id'] = $existing_parent['id'];
			} 
		}
		
		return $this->addMenuEntry($data);
		
	}
	
	
	/**
	 * Returns the menu entry for a given entry id.
	 * If id is not an int but a string, then the string is matched against
	 * the commonname table field.
	 * 
	 * 
	 * @param		int			id of the menu
	 * @param		array		key/value array where the key is the field name and the value is it's field value
	 * 							in the menu entry to match against (instead of the id and commonname by default).
	 * @return		array		array representation of a menu entry or false on error
	 */
	public function getMenuEntry($id = false, $whereClause = NULL)
	{
			
		if (!$id)
			return false;
		
		if (is_int($id))
			$query = $this->db->get_where('menu', (isset($whereClause)) ? $whereClause : array('id' => $id));
		else if (is_string($id))
			$query = $this->db->get_where('menu', (isset($whereClause)) ? $whereClause : array('commonname' => $id));
			
		if ($query->num_rows() == 0)
			return false;
			
		return $query->row_array();
		
	}
	
	
	/**
	 * Get all childs of a parent menu entry (not recursive)
	 * 
	 * @param		int			the id of the parent menu entry to get childs for
	 * @return		array		array representation of the navigation tree
	 */
	public function getChilds($parent_id = false)
	{

		if (!$parent_id)
			return false;
		
		if (is_int($parent_id))
			$query = $this->db->get_where('menu', array('parent_id' => $parent_id));
		else if (is_string($parent_id)) {
			$query = $this->db->join('menu t2', 't1.id = t2.parent_id')->where('t1.commonname', $parent_id)->get('menu t1');
		}
		
		if ($query->num_rows() == 0)
			return false;
		
		return $query->result_array();
		
	}
	
	
	/**
	 * 
	 * @param		int			the id of the parent node
	 * @return		array		array representation of the navigation menu tree for the parent id
	 */
	public function getChildsRecurs($parent_id = false)
	{
		
		if (!$parent_id)
			return false;
		
		$tree = array();
		
		$childs = $this->getChilds($parent_id);
		
		foreach($childs as $child) {
			if ($this->getChilds($child['id']) === false) {
				$tree[$child['title']] = $child;
			} else {
				$tree[$child['title']] = $this->getChildsRecurs($child['id']);
			}
		}		
		
		return $tree;
	}	
	
	
}