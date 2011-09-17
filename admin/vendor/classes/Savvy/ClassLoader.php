<?php
/**
 * @package Savvy
 */
class Savvy_ClassLoader
{
	/** @var array */
	private $_paths;
	
	/**
	 * @param array|string $path
	 */
	public function __construct($path)
	{
		$this->_paths = is_array($path) ? $path : array($path);
	}
	
	/**
	 * @throws Exception when spl_autoload_register fails (done internally) or includepaths fails
	 */
	public function register()
	{
		$this->_includePaths();
		
		if (function_exists('__autoload'))
		{
			$this->_registerToJLoader();
		}
		else
		{
			spl_autoload_register(array($this, '_autoload'));
		}
	}
	
	/**
	 * Add Include Path
	 * @throws Exception when cant get include path
	 */
	private function _includePaths()
	{
		$includePaths = explode(PATH_SEPARATOR, get_include_path());
		
		if ($includePaths === false)
		{
			throw new Exception('Couldn\'t parse include path');
		}
		
		// move new paths into include paths
		$includePaths = array_merge($includePaths, $this->_paths);
		
		$includePath = implode(PATH_SEPARATOR, $includePaths);
		
		set_include_path($includePath);
	}

	/**
	 * @param string $class
	 */
	private function _autoload($class)
	{
		// use PEAR like class names
		$className = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
		
		// since class_exists calls autoload, we need to make sure file exists before require
		if (function_exists('stream_resolve_include_path'))
		{
			if (stream_resolve_include_path($className))
			{
				require $className;
			}
		}
		else
		{
			// mimic stream_resolve_include_path
			$includePaths = explode(PATH_SEPARATOR, get_include_path());
			
			foreach ($includePaths as $includePath)
			{
				$path = $includePath . DIRECTORY_SEPARATOR . $className;
				
				if (file_exists($path))
				{
					require $className;
					
					break;
				}
			}
		}
	}
	
	/**
	 * Adds Classes to JLoader
	 */
	private function _registerToJLoader()
	{
		// register backwards to mimic spl_autoload_register
		for ($i = count($this->_paths) - 1; $i >= 0; --$i) 
		{
			$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->_paths[$i]));
			
			while ($it->valid())
			{
				$file = $it->current();
				
				if ($file->isFile() && pathinfo($realPath = $file->getRealPath(), PATHINFO_EXTENSION) === 'php')
				{
					$dir = $it->getSubIterator();
					
					$prefix = str_replace(DIRECTORY_SEPARATOR, '_', $dir->getSubPath()) and $prefix .= '_';
					
					$className = $prefix . $file->getBasename('.php');
					
					JLoader::register($className, $realPath);
				}
				
				$it->next();
			}
		}
	}
}