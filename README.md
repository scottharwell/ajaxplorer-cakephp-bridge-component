AjaxPlorer / CakePHP Bridge
===================================

A CakePHP component to assist with AjaxPlorer communications. This repo can be included as a CakePHP plugin or you can copy the component file directly to your application's `Controller/Component` directory.

Requirements
------------
* CakePHP 2.0+
* AjaxPlorer 4.0+ installed in `webroot/ajaxplorer` directory.
	* This component relies on AjaxPlorers `auth.remote` plugin to be in its default location. If you need to change the path then use `$this->Ajaxplorer->glueCode = '/path/to/glueCode.php';` to set the proper path before using any functions.

Instructions
------------

* Properly included the component in your CakePHP app and reference it in your controller's `components` array.
* Update this component's variables to match the authentication scheme within your application. The component currently expects a user object to `haveOne` role that it can use to determine and administrator. The current implementation of this component relies on a `User` object with the following variables. If you require other settings, which is probably likely, then change the code in the switch statement to match your `User` data coming from CakePHP.
	* `username`
	* `password`
	* `role_id`
* If AjaxPlorer plugins are not installed in the standard paths, then set the path the `glueCode.php` before calling any functions.
	* `$this->Ajaxplorer->glueCode = '/path/to/glueCode.php';`
* Once you have properly setup the integration between your CakePHP `User` object and the AjaxPlorer user process, you can then call AjaxPlorer functions like `login` with this component as illustrated below.

        public function login() {
    		$this->set("title_for_layout", "Login");
    		
    		if ($this->request->is('post')) {
    	        if ($this->Auth->login()) {
    	        	$user = $this->User->findById($this->Auth->user('id'));
    	        	$this->Ajaxplorer->login($user);
    			   	
    			   	//Log
    			   	CakeLog::write('login', $user['User']['username']);
    	        	
    	        	//Redirect to CMS
    	        	$this->Session->setFlash(__('Successfully logged in!'));
    	            $this->redirect($this->Auth->redirect());
    	        } else {
    			   	CakeLog::write('login failed', $this->request->data['User']['username']);
    	            $this->Session->setFlash(__('Username or password is incorrect'), 'default', array(), 'auth');
    	        }
    	    }
        }

###Note###

Review the [AjaxPlorer](http://ajaxplorer.info) and [CakePHP](http://www.cakephp.net) APIs if you are having difficulty. This component only acts as a bridge between the two; passing information back and forth.  If you are experiencing errors, then the issue is almost certainly with your implementation.


License
=======

AjaxPlorer CakePHP Component
Copyright (C) 2012 Scott Harwell

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program.  If not, see http://www.gnu.org/licenses/.