<?php
/**
 * GCalendar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GCalendar is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GCalendar.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Allon Moritz
 * @copyright 2007-2011 Allon Moritz
 * @since 2.2.0
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgSearchGCalendar extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	function onContentSearchAreas()
	{
		static $areas = array(
		'gcalendar' => 'GCalendar'
		);
		return $areas;
	}

	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{
		require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_gcalendar'.DS.'util.php');
		GCalendarUtil::ensureSPIsLoaded();

		$user	=& JFactory::getUser();

		$text = trim( $text );
		if ($text == '') {
			return array();
		}
		if($phrase == 'exact')
		$text = "\"".$text."\"";

		switch ( $ordering )
		{
			case 'oldest':
				$orderasc = TRUE;
				break;

			case 'newest':
			default:
				$orderasc = FALSE;
		}

		if (is_array($areas)) {
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas()))) {
				return array();
			}
		}

		$pluginParams = $this->params;

		$limit = $pluginParams->def( 'search_limit', 50 );

		$calendarids = $pluginParams->get( 'calendarids', NULL );
		$pastevents = $pluginParams->get( 'pastevents', 1 ) == 1;
		$results = GCalendarDBUtil::getCalendars($calendarids);
		if(empty($results))
		return array();

		$events = array();
		foreach ($results as $result) {
			$feed = new SimplePie_GCalendar();
			$feed->set_show_past_events($pastevents);
			$feed->set_sort_ascending($orderasc);
			$feed->set_orderby_by_start_date(TRUE);
			$feed->set_expand_single_events(TRUE);
			$feed->enable_order_by_date(FALSE);
			$feed->enable_cache(FALSE);
			$feed->set_cache_duration(1);
			$feed->set_cal_query($text);
			$feed->set_max_events($limit);
			$feed->put('gcid',$result->id);
			$feed->set_cal_language(GCalendarUtil::getFrLanguage());
			$feed->set_timezone(GCalendarUtil::getComponentParameter('timezone'));

			$url = SimplePie_GCalendar::create_feed_url($result->calendar_id, $result->magic_cookie);
			$feed->set_feed_url($url);
			$feed->init();
				
			if ($feed->error()){
				JError::raiseWarning( 500, 'Simplepie detected an error for the calendar '.$result->calendar_id.'. Please run the <a href="administrator/components/com_gcalendar/libraries/sp-gcalendar/sp_compatibility_test.php">compatibility utility</a>.<br>The following Simplepie error occurred:<br>'.$feed->error());
			}

			$feed->handle_content_type();
			$events = array_merge($events, $feed->get_items());
		}

		usort($events, array("SimplePie_Item_GCalendar", "compare"));
		array_splice($events, $limit);

		$return = array();
		foreach($events as $event){
			$feed = $event->get_feed();

			// the date formats from http://php.net/date
			$dateformat = 'd.m.Y';
			$timeformat = 'H:i';

			// These are the dates we'll display
			$startDate = GCalendarUtil::formatDate($dateformat, $event->get_start_date());
			$startTime = GCalendarUtil::formatDate($timeformat, $event->get_start_date());

			$timeString = $startTime.' '.$startDate;
			switch($event->get_day_type()){
				case $event->SINGLE_WHOLE_DAY:
					$timeString = $startDate;
					break;
				case $event->SINGLE_PART_DAY:
					$timeString = $startTime.' '.$startDate;
					break;
				case $event->MULTIPLE_WHOLE_DAY:
					$timeString = $startDate;
					break;
				case $event->MULTIPLE_PART_DAY:
					$timeString = $startTime.' '.$startDate;
					break;
			}

			$itemID = GCalendarUtil::getItemId($feed->get('gcid'));
			if(!empty($itemID))$itemID = '&Itemid='.$itemID;
			$row->href = JRoute::_('index.php?option=com_gcalendar&view=event&eventID='.$event->get_id().'&start='.$event->get_start_date().'&end='.$event->get_end_date().'&gcid='.$feed->get('gcid').$itemID);
			$row->title = $timeString.' '.$event->get_title();
			$row->text = $event->get_description();
			$row->section = JText::_('PLG_SEARCH_GCALENDAR_OUTPUT_CATEGORY');
			$row->category = $feed->get('gcid');
			$row->created = $event->get_start_date();
			$row->browsernav = '';
			$return[] = $row;
			$row = null;
		}
		return $return;
	}
}
?>