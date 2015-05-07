# CakePHP Stateless AuthComponent

[![Latest Version](https://img.shields.io/github/release/loadsys/CakePHP-Stateless-Auth.svg?style=flat-square)](https://github.com/loadsys/CakePHP-Stateless-Auth/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/loadsys/CakePHP-Stateless-Auth.svg?branch=master&style=flat-square)](https://travis-ci.org/loadsys/CakePHP-Stateless-Auth)
[![Coverage Status](https://coveralls.io/repos/loadsys/CakePHP-Stateless-Auth/badge.svg)](https://coveralls.io/r/loadsys/CakePHP-Stateless-Auth)
[![Total Downloads](https://img.shields.io/packagist/dt/loadsys/cakephp-stateless-auth.svg?style=flat-square)](https://packagist.org/packages/loadsys/cakephp-stateless-auth)

A replacement CakePHP Authentication/Authorization Component that is fully and strictly stateless. Designed to be used with Cake apps that are only accessed RESTfully.

The provided component is intended to replace Cake's stock `AuthCompnent`. This replacement `StatelessAuthComponent` is a stripped down and simplified version that by default looks for an `Authorization` header in the HTTP request and populates `Auth->User()` using the `Bearer [token]` value from that header. (This is instead of the stock AuthComponent's default operation of looking up data from an active `$_SESSION` on repeat connections using the cookie provided by the browser.) It supports plug-able Authenticate and Authorize objects, and the package includes a few that may be of use.

:warning: This is still unstable software and probably not suitable for public use yet.


## Requirements

* PHP >= 5.4.0
* CakePHP >= 2.6


## Installation

### Composer

* Run this shell command

````bash
$ composer require loadsys/cakephp-stateless-auth:dev-master
````

### Setup

Load the plugin and be sure that bootstrap is set to true:

```php
// Config/boostrap.php
CakePlugin::load('StatelessAuth', array('bootstrap' => true));
// or
CakePlugin::loadAll(array(
	'StatelessAuth' => array('bootstrap' => true),
));
```

The [CakePHP book has more information on doing REST APIs](http://book.cakephp.org/2.0/en/development/rest.html) with CakePHP and this feature.


## Sample Usage

In your project's `AppController`, change your `$components` array to use this plugin's StatelessAuthComponent, but alias it to allow access by the common name:


```php
	public $components = array(
		'Auth' => array(
			'className' => 'StatelessAuth.StatelessAuth',
			'authenticate' => array(
				'className' => 'StatelessAuth.Token',

				// Additional examples:

				// 'userModel' => 'User',
				// 'tokenField' => 'token',
				// 'recursive' => -1,
				// 'contain' => array('Permission'),
				// 'conditions' => array('User.is_active' => true),
				// 'passwordHasher' => 'Blowfish',
			),
		),
		'Paginator',
		'DebugKit.Toolbar',
	);
```

How you authenticate your requests to your Cake app is up to you. If you use the bundled `TokenAuthenticate` object as demonstrated above, you must include an `Authorization` header in your request that includes a `Bearer [token]` that matches a valid token in your User table. The token represents the User's login session, in effect replacing `$_SESSION`. A sample HTTP request might look like the following:

```
GET /users/view HTTP/1.1
Host: vagrant.dev:80
Authorization: Bearer 0193d044dd2034bfdeb1ffa33c5fff9b
```

:warning: Just like normal Auth, the token will be sent in the clear and could be intercepted and re-used, so be sure to secure your connections using SSL.

`TokenAuthenticate` will attempt to look up a User record using the provided token. You can define the name of your User model to query and the name of the token field to check in the component configuration as shown above.

The StatelessAuthComponent uses this authenticate object by default.


You will still access the Component as usual In your controllers:

```php
	/**
	 * Allow the logged-in User to view their own record.
	 *
	 * @return void
	 * @throws NotFoundException If the passed id record does not exist
	 */
	public function view() {
		$id = $this->Auth->user('id'); // <-- Populated by the stateless auth component.
		if (!$id) {
			throw new NotFoundException(__('Please log in to view your User record.'));
		}
		$options = array(
			'conditions' => array(
				'User.' . $this->User->primaryKey => $id,
			),
		);
		$user = $this->User->find('first', $options);
		$this->set(compact('user'));
	}
```

You must define an `::isAuthorized($user)` method either in each controller or your `AppController` that returns true or false based on whether the current `Auth->user()` should be allowed to access the current controller action.

If you wish for all authenticated Users to have access to all methods, you can place the following in your project's AppController:

```php
	public function isAuthorized($user) {
		return true;
	}
```

Alternatively, you can supply your own authorization object to perform the appropriate checks yourself. See Cake's cookbook section on [Authorization](http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#authorization) for details.

## Error and Exception Handling Setup

Errors and Exceptions are handled via a separate CakePHP plugin, 
included via Composer: [SerializersErrors](https://github.com/loadsys/CakePHP-Serializers-Errors)

Please read the documentation there for more information on the specifics.

Modify your `app/Config/core.php` file to use the Custom Exceptions/Error
handling in SerializersErrors.

``` php
Configure::write('Exception', array(
	'handler' => 'ErrorHandler::handleException',
	'renderer' => 'SerializersErrors.SerializerExceptionRenderer',
	'log' => true,
));
```

This does two things:

* Errors and Exceptions get output as correctly formatted JSON API, JSON or HTML 
depending on the request type
* Allows the use of Custom Exceptions that match Ember Data exceptions for error cases
* The classes in this plugin use this format to enable easier use for API Authentication Handling

### Swapping authentication and authorization objects

The project comes with additional Auth objects that can be used to extend the functionality surrounding HTTP header authentication. The `TokenLoginLogoutAuthenticate` object, for example, allows you to hook callback behavior into the `Auth->login()` and `Auth->logout()` processes to perform additional Model operations.

See `Controller/Component/Auth/TokenLoginLogoutAuthenticate.php`, specifically `::requireUserModelMethods()` for details and expected method signatures.

@TODO: Write up proper documentation on the callback methods needed.



## Contributing

### Reporting Issues

Please use [GitHub Isuses](https://github.com/loadsys/CakePHP-Stateless-Auth/issues) for listing any known defects or issues.

### Development

When developing this plugin, please fork and issue a PR for any new development.

The Complete Test Suite for the Plugin can be run via this command:

`./lib/Cake/Console/cake test StatelessAuth AllStatelessAuth`

## License

[MIT](https://github.com/loadsys/CakePHP-Stateless-Auth/blob/master/LICENSE.md)


## Copyright

[Loadsys Web Strategies](http://www.loadsys.com) 2015
