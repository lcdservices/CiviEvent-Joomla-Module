<?php
/**
* CiviEvent Joomla Module
*
* @copyright    Copyright (C) 2005-2011 Open Source Matters. All rights reserved.
* @license      http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
 
// No direct access
defined('_JEXEC') or die;

// Include the helper
require_once( dirname(__FILE__).'/helper.php' );

$eventtitles   = modCiviEventHelper::getEventTitles( $params );
$displayParams = modCiviEventHelper::sendParam( $params );

require( JModuleHelper::getLayoutPath( 'mod_civievent' ) );
