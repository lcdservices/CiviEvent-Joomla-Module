<?php
class JElementEventlist extends JElement
{
   var   $_name = 'Eventlist';

   function fetchElement($name, $value, &$node, $control_name)
   {
      $db =& JFactory::getDBO();
	  $class		= $node->attributes('class');
      $size = ( $node->attributes('size') ? $node->attributes('size') : 10 );
      $query = 'SELECT id, title FROM civicrm_event WHERE is_active = 1 AND is_public = 1 AND is_template != 1 ORDER BY title';
      $db->setQuery($query);
      $options = $db->loadObjectList();
	     return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.'][]', ' multiple="multiple" style="width: 300px;" size="' . $size . '" class="'.$class.'"', 'id', 'title', $value, $control_name.$name );  
       }  
}
