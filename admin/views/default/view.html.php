<?php defined('_JEXEC') or die('Restricted access');

class iCalImporterViewDefault extends iCalImporter_AbstractAdminSectionView
{
	/**
	 * @Override
	 */
	protected function getMenuName() { return 'iCal Importer'; }
	
	/**
	 * @Override
	 */
	protected function displayToolbarButtons() {}
}