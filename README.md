# CakePHP-Stateless-Auth

<!-- @TODO: Enable these once the project is public, published on Packagist, and auto-tested by Travis.
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/loadsys/CakePHP-Stateless-Auth.svg?branch=master&style=flat-square)](https://travis-ci.org/loadsys/CakePHP-Stateless-Auth)
[![Total Downloads](https://img.shields.io/packagist/dt/loadsys/cakephp-statelessauth.svg?style=flat-square)](https://packagist.org/packages/loadsys/cakephp-statelessauth)
-->

A replacement CakePHP Authentication/Authorization Component that is fully and strictly stateless. Designed to be used with Cake apps that are only accessed RESTfully.

The provided component is intended to replace Cake's stock `AuthCompnent` (which has trouble not creating Sessions even when you explicitly tell it not to.) This replacement `StatelessAuthComponent` is a stripped down and simplified version that by default looks for an `Authorization` header in the HTTP request and populates `Auth->User()` using the `Bearer [token]` value from that header. It supports plug-able Authenticate and Authorize objects, and the package includes a few that may be of use.

:warning: This is still alpha-quality software and probably not suitable for public use yet.



## Requirements

* PHP >= 5.4.0
* CakePHP >= 2.3



## Installation

### Composer

* Run this shell command

```bash
php composer.phar require loadsys/cakephp-statelessauth "dev-master"
```

### Git

```bash
git clone https://github.com/loadsys/CakePHP-Stateless-Auth.git Plugin/Stateless-Auth
```

### Setup

Load the plugin and be sure that bootstrap is set to true:

```php
// Config/boostrap.php
CakePlugin::load('Stateless-Auth', array('bootstrap' => true));
// or
CakePlugin::loadAll(array(
	'Stateless-Auth' => array('bootstrap' => true),
));
```

The [CakePHP book has more information on doing REST APIs](http://book.cakephp.org/2.0/en/development/rest.html) with CakePHP and this feature.


## Sample Usage

In your project's `AppController`, change your `$components` array to use the plugin's Component, but alias it to allow access by the common name:


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


### Errors and Exceptions

Some classes in this plugin throw custom exceptions instead of the typical `return false;`. This is meant to aid in communicating failures and make rendering errors in a specific format (such as json) easier to handle.



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