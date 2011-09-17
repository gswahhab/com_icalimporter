<?php defined('_JEXEC') or die('Restriced access.');

class iCalImporterModelDefault extends JModel
{
	/**
	 * @param string $fileName name or key in request
	 * @param string $timeFormat
	 * @param string $adminEmail
	 */
	public function import($fileName, $timeFormat, $adminEmail)
	{
		if (!$file = $this->_getUploadFile($fileName)) {
			return false;
		}
		
		if (!$data = $this->_getDataFromFile($file, $timeFormat, $adminEmail)) {
			return false;
		}
		
		if (!$this->_saveToDTRegister($data)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * @param string $fileName name or key in request
	 */
	private function _getUploadFile($fileName)
	{
		jimport('joomla.filesystem.file');
		
		// Make sure that file uploads are enabled in php
		if (!(bool) ini_get('file_uploads'))
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('WARNINSTALLFILE'));
			return false;
		}
		
		// Get the uploaded file information
		$file = JRequest::getVar($fileName, null, 'files', 'array');
		
		if (empty($file))
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('No file selected'));
			return false;
		}
		
		// Check if there was a problem uploading the file.
		if ($file['error'] || $file['size'] < 1)
		{
			JError::raiseWarning('SOME_ERROR_CODE', JText::_('WARNINSTALLUPLOADERROR'));
			return false;
		}
		
		// check that it is a valid iCal file
		switch (JFile::getExt($file['name']))
		{
			case 'ical':
			case 'ics':
			case 'ifb':
			case 'icalendar':
				break;
				
			default:
				JError::raiseWarning('SOME_ERROR_CODE', 'Invalid File Extension');
				return false;
		}
		
		// Build the appropriate paths
		$config = JFactory::getConfig();
		$tmp_dest = $config->getValue('config.tmp_path').DS.$file['name'];
		$tmp_src = $file['tmp_name'];
		
		// Move the file to the joomla tmp folder
		if (!JFile::upload($tmp_src, $tmp_dest))
		{
			JError::raiseWarning('SOME_ERROR_CODE', 'Could not move file to tmp');
			return false;
		}
		
		$contents = file_get_contents($tmp_dest);
		
		if ($contents === false)
		{
			JError::raiseWarning('SOME_ERROR_CODE', 'Could not open file');
			return false;
		}
		
		return $contents;
	}
	
	/**
	 * This function prepares data for DTRegister
	 * @param string $file file contents
	 * @param string $timeFormat
	 * @param string $adminEmail
	 * @return array|bool false on failure, array on success
	 */
	private function _getDataFromFile($file, $timeFormat, $adminEmail)
	{
		require_once JPATH_COMPONENT.DS.'vendor'.DS.'iCalcreator'.DS.'iCalcreator.class.php';
		
		$vcalendar = new vcalendar;
		
		if (!$vcalendar->parse($file)) {
			JError::raiseWarning('SOME_ERROR_CODE', 'Could not parse ical file');
			return false;
		}
		
		// Get all VEVENT
		$vevents = array();
		while ($event = $vcalendar->getComponent('vevent')) {
			$vevents[] = $event;
		}
		
		if (empty($vevents)) {
			JError::raiseWarning('SOME_ERROR_CODE', 'Could not find any events in file');
			return false;
		}
		
		$data = array();
		
		foreach ($vevents as $vevent)
		{
			$event = array();
			
			$event['user_id'] = null; // event owner user id
			$event['eventId'] = null; // event id, should always be null
			$event['title'] = $vevent->getProperty('summary'); // event title
			$event['category'] = null; // event category id
			$event['location_id'] = $this->_getLocationId($location); // event location
			$event['publish'] = 0; // dont immediately publish
			
			$event['timeformat'] = (int) $timeFormat; // time display format
			
			list($dtstart, $dtstarttime) = $this->_timeFormat($timeFormat, $vevent->getProperty('dtstart'));
			$event['dtstart'] = $dtstart;
			$event['dtstarttime'] = $dtstarttime;
			
			list($dtend, $dtendtime) = $this->_timeFormat($timeFormat, $vevent->getProperty('dtend'));
			$event['dtend'] = $dtend;
			$event['dtendtime'] = $dtendtime;
			
			$event['email'] = $adminEmail;
			$event['repeatType'] = 'norepeat';
			$event['rpinterval'] = 1;
			$event['rpcount'] = null;
			$event['rpuntil'] = null;
			$event['monthdays'] = null;
			$event['monthdayselector'] = 'monthweekdays';
			$event['registration_type'] = 'individual';
			$event['group_registration_type'] = 'detail';
			$event['public'] = 1;
			$event['max_registrations'] = null;
			$event['min_group_size'] = 2;
			$event['max_group_size'] = null;
			$event['startdate'] = null;
			$event['enddate'] = null;
			$event['cut_off_date'] = null;
			$event['cut_off_time'] = null;
			$event['waiting_list'] = 0;
			$event['article_id'] = null;
			$event['detail_itemid'] = null;
            $event['detail_link_show'] = 0;
            $event['show_registrant'] = 0;
            $event['usercreation'] = 0;
            $event['imagepath'] = null; 
            $event['payment_id'] = 0;
            $event['partial_payment'] = 0;
            $event['partial_amount'] = 0; 
            $event['partial_minimum_amount'] = 0; 
            $event['tax_enable'] = 0;
            $event['tax_amount'] = 0;
            $event['discount_type'] = 0;
            $event['discount_amount'] = 0; 
            $event['bird_discount_type'] = 0;
            $event['bird_discount_amount'] = 0; 
            $event['bird_discount_date'] = 0; 
            $event['bird_discount_time'] = 0; 
            $event['latefee'] = null; 
            $event['latefeedate'] = null;
            $event['latefeetime'] = null;
            $event['use_discountcode'] = 0;
            $event['topmsg'] = null;
			$event['event_describe_set'] = 0;
            $event['event_describe'] = null; 
            $event['thanksmsg_set'] = 0;
            $event['thanksmsg'] = null;
            $event['pay_later_thk_msg_set'] = 0;
            $event['pay_later_thk_msg'] = null;
            $event['terms_conditions_set'] = 0;
            $event['terms_conditions_msg'] = null;
            $event['thksmsg_set'] = 0;
            $event['thksmsg'] = null;
            $event['admin_notification_set'] = 0;
            $event['admin_notification'] = null; 
            $event['partial_payment_enable'] = 0;
            $event['cancel_refund_status'] = 0;
            $event['edit_fee'] = 0;
            $event['change_date'] = null; 
            $event['change_time'] = null;
            $event['cancel_enable'] = 0;
            $event['cancel_date'] = null; 
            $event['cancel_time'] = null;
            $event['changefee_enable'] = 0;
            $event['changefee_type'] = 1;
            $event['changefee'] = null; 
            $event['cancelfee_enable'] = 0;
            $event['cancelfee_type'] = 1;
            $event['cancelfee'] = null; 
            $event['slabId'] = null; 
			
			$data[] = array('event' => $event);
		}
		
		return $data;
	}
	
	/**
	 * @param int $format
	 * @param array $time
	 * @return array
	 */
	private function _timeFormat($format, $time)
	{
		if ($format === 1)
		{
			$date = $time['year'].'-'.$time['month'].'-'.$time['day'];
			
			$datetime = strtotime($date);
			$datetime = strtotime("+{$time['hour']} hours", $datetime);
			$datetime = strtotime("+{$time['min']} minutes", $datetime);
			$datetime = date('h:i A', $datetime);
		}
		elseif ($format === 2)
		{
			$date = $time['year'].'-'.$time['month'].'-'.$time['day'];
			$datetime = $time['hour'].':'.$time['min'];
		}
		else
		{
			$date = null;
			$datetime = null;
		}
		
		return array($date, $datetime);
	}
	
	/**
	 * Save data to DTRegister
	 * @param array $data
	 * @return bool
	 */
	private function _saveToDTRegister($data)
	{
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_dtregister'.DS.'models'.DS.'event.php';
		
		// these are all dependencies of event model
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_dtregister'.DS.'models'.DS.'feeorder.php';
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_dtregister'.DS.'models'.DS.'discountcode.php';
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_dtregister'.DS.'models'.DS.'field.php';
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_dtregister'.DS.'models'.DS.'file.php';
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_dtregister'.DS.'models'.DS.'payoption.php';
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_dtregister'.DS.'models'.DS.'location.php';
		require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_dtregister'.DS.'models'.DS.'paylater.php';

		$success = true;
		
		// need to save
		if (!function_exists('pr'))
		{
			function pr($d = array()) {}
		}
		
		foreach ($data as $d)
		{
			$eventModel = new DtregisterModelEvent;
			$eventTable = $eventModel->table;
			
			if (!$eventTable->save($d))
			{
				JError::raiseWarning('SOME_ERROR_CODE', 'Could not save');
				$success = false;
			}
		}
		
		return $success;
	}
	
	/**
	 * Creates a new location on the fly if needed
	 * This is memoized
	 * @return int location id
	 */
	private function _getLocationId($location)
	{
		static $locations = array();
		
		if (!isset($locations[$location]))
		{
			$db = $this->getDBO();
			
			$quotedLocation = $db->quote($location);
		
			$query = "SELECT id FROM #__dtregister_locations WHERE name = {$quotedLocation} LIMIT 1";
		
			$db->setQuery($query);
		
			$result = $db->loadResult();
			
			if ($result === null)
			{
				$query = "INSERT INTO #__dtregister_locations (name) VALUES ({$quotedLocation})";
				
				$db->setQuery($query);
				
				$db->query();
				
				if ($db->getAffectedRows() > 0 && ($id = $db->insertid()) > 0)
				{
					$result = $id;
				}
				else
				{
					$result = null;
				}
			}
			else
			{
				$result = (int) $result['id'];
			}
			
			$locations[$location] = $result;
		}
		
		return $locations[$location];
	}
}