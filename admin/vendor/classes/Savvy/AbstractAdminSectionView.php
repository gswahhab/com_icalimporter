<?php
/**
 * @package Savvy
 * @uses JView
 * @abstract
 */
abstract class Savvy_AbstractAdminSectionView extends JView
{
	/**
	 * @param string $template optional
	 */
	public function display($template = null)
	{
		$this->beforeDisplay();
		
		$this->displayToolbar();
		
		$this->displaySubmenu();
		
		$data = $this->createTemplateData();
		
		if (!empty($data)) {
			$this->assign($data);
		}
		
		parent::display($template);
	}
	
	abstract protected function displayToolbar();
	
	abstract protected function displaySubmenu();
	
	/**
	 * @return array|null
	 */
	protected function createTemplateData() {}
	
	protected function beforeDisplay() {}
}