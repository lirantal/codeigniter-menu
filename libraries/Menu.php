<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Wrapper library to the Menu model which provides implementation of a 
 * dynamic tree-based menu with entries and their parents. 
 * 
 * @author		Liran Tal <liran.tal@gmail.com>
 * @package		daloRADIUS
 * @subpackage	Menu Navigation module for CodeIgniter
 * @copyright	GPLv2
 *
 */
class Menu {
	
	// CodeIgniter's super global referrence
	private $ci;
	
	
	public function __construct()
	{
		// Assigns the CodeIgniter's global reference
		$this->ci =& get_instance();
		
		// Load the menu model
		$this->ci->load->model('menu/menu_m');
		
	}
	
	/**
	 * Utilizing PHP's magic calls to call the model's methods and, rendering
	 * the library to act simply as the middle-man
	 * 
	 * @param		string		method name
	 * @param		mixed		arguments passed to the method
	 * @return		mixed		model's method's returned variable, mostly an array.
	 */
	private function __call($method, $args)
	{
			
		$obj =&  $this->ci->menu_m;
		return call_user_func_array(array(&$obj, $method), $args);
		
	}
	
	/**
	 * Aids in formatting the returned data from the method call
	 * 
	 * @param		string		method name
	 * @param		mixed		arguments passed to the method
	 * @param		string		format type, one of: array, json, serialize. defaults to php's array
	 * @return		mixed		representation of model's data result based on the format provided
	 */
	public function getFormatted($method, $args = NULL, $format = 'array')
	{
		$res = $this->__call($method, $args);
		switch($format) {
			default:
			case "array":
				return $res;
			case "serialize":
				return serialize($res);
			case "json":
				return json_encode($res);
		}
		
	}
	
}