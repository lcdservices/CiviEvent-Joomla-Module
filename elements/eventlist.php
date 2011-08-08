<?php
class JFormFieldEventlist extends JFormField
{
    /**
     * The form field type
     *
     * @var     string
    **/
    public $type = 'Eventlist';
    
    private function def( $val, $default = '' )
    {
        return ( isset( $this->params[$val] ) && (string) $this->params[$val] != '' ) ? (string) $this->params[$val] : $default;
    }
    
    protected function getInput()
    {
        $_class = $this->def( 'class' );
        $_size  = $this->def( 'size', 10 );
        
        $db     =& JFactory::getDBO();
        $query  = 'SELECT id, title FROM civicrm_event WHERE is_active = 1 AND is_public = 1 AND is_template != 1 ORDER BY title';
        $db->setQuery($query);
        $options = $db->loadObjectList();
        return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.'][]', ' multiple="multiple" style="width: 250px;" size="' . $_size . '" class="'.$class.'"', 'id', 'title', $value, $control_name.$name );  
    }
}
