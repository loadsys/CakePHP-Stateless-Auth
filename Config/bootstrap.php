<?php

// Ensure we loaded the SerializersErrors Plugin
CakePlugin::load('SerializersErrors', array('bootstrap' => true));

// Load CakePHP Stateless Auth Exceptions
App::import('Lib/Error', 'StatelessAuth.StatelessAuthException');
