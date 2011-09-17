<?php defined('_JEXEC') or die('Restricted access');

class iCalImporterControllerDefault extends JController
{
	public function import()
	{
		list($valid, $data) = $this->_validateRequestData();
		
		if ($valid)
		{
			$import = $this->getModel('Default')->import('ical_file', $data['timeFormat'], $data['adminEmail']);
		
			$this->setMessage($import ? 'Import Successful' : 'Import Failed');
		}
		
		$this->setRedirect(JRoute::_('index.php?option=com_icalimporter'));
	}
	
	/**
	 * @return array
	 */
	private function _validateRequestData()
	{
		JRequest::checkTone() or jexit('Invalid Token');
		
		$timeFormat = JRequest::getVar('ical_time_format');
		$adminEmail = JRequest::getVar('ical_admin_email');
		
		$valid = true;
		
		if (!$timeFormat) {
			JError::raiseWarning('SOME_ERROR_CODE', 'Please provide a time format');
			$valid = false;
		}
		
		if (!$adminEmail) {
			JError::raiseWarning('SOME_ERROR_CODE', 'Please provide an email');
			$valid = false;
		}
		
		return array($valid, array('timeFormat' => $timeFormat, 'adminEmail' => $adminEmail));
	}
}