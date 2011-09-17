<?php

class Savvy_Bootstrapper
{
	/** @var string */
	public $defaultController;
	
	/** @var string */
	public $controllerPath;
	
	/** @var string */
	public $classPrefix;
	
	/**
	 * Bootstrap component
	 */
	public function bootstrap()
	{
		$controller = JRequest::getWord('view');
		
		if (!$controller)
		{
			JRequest::setVar('view', $this->defaultController);
			
			$controller = $this->defaultController;
		}
		
		$controllerPath = $this->controllerPath . DS . $controller . '.php';
		
		if (file_exists($controllerPath))
		{
			require $controllerPath;
			
			$className = $this->classPrefix . 'Controller' . ucfirst($controller);
			
			$controller = new $className;
			$controller->execute(JRequest::getWord('task'));
			$controller->redirect();
		}
		else
		{
			JError::raiseError(404, JText::_('Page Not Found'));
		}
	}
}