<?php
/**
 *  @Copyright   Copyright (C) 2012 by Nicolas OGIER www.nicolas-ogier.fr
 *  @package     Unpublishfront -  unpublish articles from frontpage for Joomla 2.5
 *  @author      Nicolas OGIER {@link http://www.nicolas-ogier.fr}
 *  				based on Dmitry V. Smirnov (http://www.joomlatune.ru) plugin for Joomla 1.5
 *  @version     2.5-1.0 - 06-April-2012
 *  @link        http://www.nicolas-ogier.fr/download/unpublishfront.html
 *
 *  @license GNU/GPL
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
// protection de la page
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
class plgSystemUnpublishfront extends JPlugin
{
	/**
	* @param	object		$subject The object to observe
	* @param 	array  		$config  An array that holds the plugin configuration
	* @since	1.0
	*/
	function plgSystemUnpublishfront(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}
	public function OnAfterInitialise()
	{
		$days 		= $this->params->get('days',30);
		$startpoint = $this->params->get('startpoint','created');
		$excludes 	= $this->params->get('excludes',0);
		
		// Get a database object
		$db = JFactory::getDBO();
		// default query
		$query = "SELECT id FROM #__content WHERE TO_DAYS( NOW( ) ) - TO_DAYS( `created` ) >=".$days.' AND id NOT IN('.$excludes.')';
		// query addapted
		if($startpoint=='created') {
			$query = "SELECT id FROM #__content WHERE TO_DAYS( NOW( ) ) - TO_DAYS( `created` ) >=".$days.' AND id NOT IN('.$excludes.')';
			}
		else {
			if ($startpoint=='modified') {
				$query = "SELECT id FROM #__content WHERE TO_DAYS( NOW( ) ) - TO_DAYS( `modified` ) >=".$days.' AND id NOT IN('.$excludes.')';
			}
			 else {
			 	if($startpoint=="publish_up") {
			 		$query = "SELECT id FROM #__content WHERE TO_DAYS( NOW( ) ) - TO_DAYS(  `publish_up` ) >=".$days.' AND id NOT IN('.$excludes.')';
			 	}
			 }
		}
		$db->setQuery($query);
		$rows = $db->loadColumn();
		//print_r($rows);	
		$daystring = implode(',' , $rows);
	
		$query = "DELETE FROM #__content_frontpage WHERE content_id IN($daystring)";
		$db->setQuery($query);
		$db->Query();
		$query = "UPDATE  #__content SET  `featured` =  0 WHERE  id IN($daystring)";
		$db->setQuery($query);
		$db->Query();
		
	}
}