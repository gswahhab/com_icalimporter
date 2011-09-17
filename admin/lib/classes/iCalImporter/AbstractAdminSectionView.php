<?php

abstract class iCalImporter_AbstractAdminSectionView extends Savvy_AbstractAdminSectionView
{
	/**
	 * @return string
	 */
	abstract protected function getMenuName();
	
	abstract protected function displayToolbarButtons();
	
	/**
	 * @Override
	 */
	final protected function displayToolbar()
	{
		// Add Section View Title
		JToolbarHelper::title($this->getMenuName());
		
		// Add Section View Specific buttons
		$this->displayToolbarButtons();
		
		// Add "Parameters" button
		JToolbarHelper::preferences('com_icalimporter');
	}
	
	/**
	 * @Override
	 */
	final protected function displaySubmenu()
	{
		$label = 'Import';
		JSubMenuHelper::addEntry($label, JRoute::_('index.php?option=com_icalimporter&view=default'), true);
	}
	
	/**
	 * @param string $menu
	 * @return bool
	 */
	final private function _isActiveMenu($menu)
	{
		return $this->getMenuName() === $menu;
	}
}