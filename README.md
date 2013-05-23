ShibbolethBundle
================

This bundle adds a Shibboleth user provider to the authentication provider provided by [Thomas Peeters's fork](https://github.com/thomaspeeters/ShibbolethBundle) of the original bundle (by Ronny Moreas) for your Symfony2 project.

Requirements
------------
* [PHP][@php] 5.3.3 and up.
* [Symfony 2.1][@symfony]
* A doctrine entity manager to manage your user class

Installation
------------

ShibbolethBundle is composer-friendly.

### 1. Add ShibbolethBundle in your composer.json

```js
    "require": {
        ...
        "kuleuven/shibboleth-bundle": "dev-master"
        ...
    },
   "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:PrinceAnsong/ShibbolethBundle.git"
        }
    ],	
```
Now tell composer to download the bundle by running the command:

```bash
    php composer.phar update kuleuven/shibboleth-bundle
```

Composer will install the bundle to your project's vendor/kuleuven directory..

### 2. Enable the bundle

Instantiate the bundle in your kernel:

```php
// app/AppKernel.php
<?php
    // ...
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new KULeuven\ShibbolethBundle\ShibbolethBundle(),
        );
    }
```

Configuration
-------------

### 1. Enable lazy shibboleth autentication in Apache

Add following lines to the .htaccess file in your projects web folder

```apache
    # web/.htaccess
	AuthType shibboleth
	ShibRequireSession Off
	ShibUseHeaders On
	require shibboleth
```

### 2. Setup authentication firewall 

```yml
	# app/config/security.yml
	security:
		firewalls:
			secured_area:
				pattern:    ^/secured
				shibboleth: ~
                logout:
                    path: /secured/logout
                    target: /
                    success_handler: security.logout.handler.shibboleth
```

### 3. Shibboleth configuration

Possible configuration parameters are:

```yml
	# app/config/config.yml
	shibboleth:
		handler_path: /Shibboleth.sso
		secured_handler: true
		session_initiator_path: /Login			
```

The above listed configuration values are the default values. To use the defaults, simply use the following line in your config:

```yml
	# app/config/config.yml
	shibboleth: ~
```

### 4. Shibboleth user provider configuration

Include the shibboleth user provider:

```yml
	# app/config/security.yml
    providers:
        shibboleth:
            id: shibboleth_user_provider
```

The shibboleth user provider needs the following parameters to be configured:

```yml
	# app/config/config.yml
	shibboleth_user_provider:
		entity_manager: @doctrine.orm.entity_manager
		user_class: \Path\To\My\User\Class
		unique_user_property: eduPersonPrincipalName
```

The user class
-------------

The shibboleth user provider extracts all custom user management logic to your user class. Your user class must implement the `KULeuven\ShibbolethBundle\Model\ShibbolethUserInterface` interface.

This means overriding two methods:

```php
    setupWithShibbolethCredentials(KULeuven\ShibbolethBundle\Security\ShibbolethUserToken)
```

and

```php
    processNewShibbolethToken(KULeuven\ShibbolethBundle\Security\ShibbolethUserToken)
```

The first is called when a non-existing user in your application is authenticated. It is important to have a constructor that accepts a call with no parameters. After the shibboleth user provider creates the empty user object, it will call your `setupWithShibbolethCredentials(KULeuven\ShibbolethBundle\Security\ShibbolethUserToken)` function. You can implement that function to populate your user object with the necessary data retrieved from the ShibbolethUserToken object then.

The second is called each time an existing user is logged in in your application. You can use this method to update login timestamps, update possible changed properties of the user, ...

```php
	<?php

	namespace Path\To\Your\User\Class;

	// ...

	class MyUserClass implements \KULeuven\ShibbolethBundle\Model\ShibbolethUserInterface{

	    // ...

        private $eduPersonPrincipalName;

	    function setupWithShibbolethCredentials(\KULeuven\ShibbolethBundle\Security\ShibbolethUserToken $token){

	        $this->eduPersonPrincipalName = $token->getEppn();
	        // ...

	    }

	    function processNewShibbolethToken(\KULeuven\ShibbolethBundle\Security\ShibbolethUserToken $token){

	        // update $eduPersonPrincipalName if changed

	        if($token->getEppn() != $this->eduPersonPrincipalName){
	            $this->eduPersonPrincipalName = $token->getEppn();
	        }
	        // ...

	    }

	}

```

The `ShibbolethUserInterface` interface extends Symfony's `Symfony\Component\Security\Core\User\UserInterface` interface, meaning you will have to implement its functions as well (getPassword(), getSalt(), ...).

To make things easier, an abstract `KULeuven\ShibbolethBundle\Model\ShibbolethUser` class is provided in the bundle which implements defaults for some of the interface's functions. It is mostly useful when you have no other authentication methods other than Shibboleth authentication, because it overrides the unnecessary functions of Symfony's `UserInterface`
interface with dummy implementations.

Additionally, it implements the `processNewShibbolethToken(\KULeuven\ShibbolethBundle\Security\ShibbolethUserToken)` function to do nothing just so you don't have to implement that
function if you don't need to do anything after an existing user is logged in. Of course, you can override it to fit your needs.

You will have to implement the `setupWithShibbolethCredentials(\KULeuven\ShibbolethBundle\Security\ShibbolethUserToken)` function since you at least want to store some of the
user's credentials in the `ShibbolethUserToken` object. There is no default implementation for this since this differs per application.

That's it! This should hopefully provide a quick way to bootstrap your applications. Kudos to the previous authors for the core authentication functionality.