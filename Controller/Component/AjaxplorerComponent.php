<?php
/*
 * AjaxPlorer CakePHP Component
 * Copyright (C) 2012 Scott Harwell
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with this program.  If not, see http://www.gnu.org/licenses/.
*/

App::uses('ComponentCollection', 'Controller');

class AjaxplorerComponent extends Component {
/**
 * Path to the auth.remote $glueCode file used by AjaxPlorer
 *
 * @var glueCode
 */
	public $glueCode;
	
/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
 * @param array $settings Array of configuration settings.
 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		
		//Create glue code between CMS and Ajaxplorer
    	$this->glueCode = realpath(WWW_ROOT . "ajaxplorer" . DS . "plugins" . DS . "auth.remote" . DS . "glueCode.php");
	}

/**
 * Call magic function
 *
 * @param string $method The method name to call.
 * @param array $arguments Array of arguments to pass to the function.
 */
	public function __call($method, $arguments){
		return $this->action(current($arguments), $method);
	}

/**
 * Action to call in Ajaxplorer
 *
 * @param array $data The array containing user data.
 * @param string $type The AjaxPlorer function to call.
 */	
	protected function action($data, $type = "login"){    	
    	//Get glueCode secret that will be defined in the application and AjaxPlorer glueCode.php   	
    	$secret = Configure::read('Ajaxplorer.secret');
    	define('AJXP_EXEC', true);

		global $AJXP_GLUE_GLOBALS;
		$AJXP_GLUE_GLOBALS = array();
		$AJXP_GLUE_GLOBALS["secret"] = $secret;
		$AJXP_GLUE_GLOBALS["plugInAction"] = $type;
		
		switch($type){
			case "login":
				$AJXP_GLUE_GLOBALS["autoCreate"] = true;
				$AJXP_GLUE_GLOBALS["login"] = array(
					'name' => $data['User']['username'],
					'password' => isset($data['User']['password']) ? $data['User']['password'] : "",
					'right' => ($data['User']['role_id'] == 1 ? "admin" : null)
				);
				break;
			case "logout":
				break;
			case "addUser":
				$AJXP_GLUE_GLOBALS["login"] = array(
					'name' => $data['User']['username'],
					'password' => isset($data['User']['password']) ? $data['User']['password'] : "",
					'right' => ($data['Role']['name'] == "administrator" ? "admin" : null)
				);
				break;
			case "delUser":
				$AJXP_GLUE_GLOBALS["login"] = array(
					'userName' => $data['User']['username']
				);
				break;
			case "updateUser":
				$AJXP_GLUE_GLOBALS["plugInAction"] = "addUser";
				$AJXP_GLUE_GLOBALS["login"] = array(
					'name' => $data['User']['username'],
					'password' => isset($data['User']['password']) ? $data['User']['password'] : "",
					'right' => ($data['Role']['name'] == "administrator" ? "admin" : null)
				);
				break;
			
		}
		
		//Hide any output from AjaxPlorer as it will interrupt this app
		//If $result has a problem, then this function will return false
		ob_start();
		include_once($this->glueCode);
		ob_end_clean();
		
		//Result var supplied by glueCode.php
		if($result) {
			CakeLog::write("ajaxplorer", ucfirst($type) . ": " . $data['User']['username']);
			return $this; //return this for chaining
		}
	   	
	   	return false;
	}
      
}