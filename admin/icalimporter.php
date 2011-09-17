<?php defined('_JEXEC') or die('Restriced access.');

// Add Joomla Core Libraries
require_once JPATH_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'model.php';
require_once JPATH_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'view.php';
require_once JPATH_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'controller.php';

// Add Joomla Html Libraries
require_once JPATH_LIBRARIES.DS.'joomla'.DS.'html'.DS.'html'.DS.'form.php';

// Add Savvy Class Loader
require_once JPATH_COMPONENT.DS.'vendor'.DS.'classes'.DS.'Savvy'.DS.'ClassLoader.php';

$loader = new Savvy_ClassLoader(array(
	JPATH_COMPONENT.DS.'lib'.DS.'classes',
	JPATH_COMPONENT.DS.'vendor'.DS.'classes'
));
$loader->register();

$bootstrapper = new Savvy_Bootstrapper;
$bootstrapper->defaultController = 'default';
$bootstrapper->controllerPath = JPATH_COMPONENT . DS . 'controllers';
$bootstrapper->classPrefix = 'iCalImporter';
$bootstrapper->bootstrap();