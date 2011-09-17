<?php defined('_JEXEC') or die('Restriced access.'); ?>

<form id="adminForm" enctype="multipart/form-data" action="index.php?option=com_icalimporter" method="post" name="adminForm">
	<?php echo JHTMLForm::token(); ?>
	<input type="hidden" name="task" value="import" />
	<input type="hidden" name="option" value="com_icalimporter" />
	<table class="adminform">
		<tbody>
			<tr>
				<th colspan="2">Upload iCal File</th>
			</tr>
			<tr>
				<td><label for="ical_time_format">Time Format</label></td>
				<td>
					<select id="ical_time_format" name="ical_time_format">
						<option value="1">12 hour</option>
						<option value="2">24 hour</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="ical_admin_email">Send New Registration Notification Emails To</label></td>
				<td>
					<input class="input_box" id="ical_admin_email" name="ical_admin_email" type="text" />
				</td>
			</tr>
			<tr>
				<td width="120"><label for="ical_file">File</label></td>
				<td>
					<input class="input_box" id="ical_file" name="ical_file" type="file" />
					<input class="button" type="button" value="<?php echo JText::_('Upload File'); ?> &amp; Import" onclick="submitbutton()" />
				</td>
			</tr>
		</tbody>
	</table>
</form>