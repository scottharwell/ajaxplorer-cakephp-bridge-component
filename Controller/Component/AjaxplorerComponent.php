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

class AjaxplorerComponent extends Component {
	public function __call($method, $arguments){
		return $this->action(current($arguments), $method);
	}
	
	protected function action($user, $type = "login"){    	
    	//Create glue code between CMS and Ajaxplorer
    	$glueCode =  realpath(WWW_ROOT . "ajaxplorer" . DS . "plugins" . DS . "auth.remote" . DS . "glueCode.php");
    	
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
					'name' => $user['User']['username'],
					'password' => isset($user['User']['password']) ? $user['User']['password'] : "",
					'right' => ($user['User']['role_id'] == 1 ? "admin" : null)
				);
				break;
			case "logout":
				break;
			case "addUser":
				$AJXP_GLUE_GLOBALS["login"] = array(
					'name' => $user['User']['username'],
					'password' => isset($user['User']['password']) ? $user['User']['password'] : "",
					'right' => ($user['Role']['name'] == "administrator" ? "admin" : null)
				);
				break;
			case "delUser":
				$AJXP_GLUE_GLOBALS["login"] = array(
					'userName' => $user['User']['username']
				);
				break;
			case "updateUser":
				$AJXP_GLUE_GLOBALS["plugInAction"] = "addUser";
				$AJXP_GLUE_GLOBALS["login"] = array(
					'name' => $user['User']['username'],
					'password' => isset($user['User']['password']) ? $user['User']['password'] : "",
					'right' => ($user['Role']['name'] == "administrator" ? "admin" : null)
				);
				break;
			
		}
		
	   	ob_start();
	   	include($glueCode);
	   	ob_end_clean();
	   	
	   	CakeLog::write("ajaxplorer_{$type}", $user['User']['username']);
	}
      
}

