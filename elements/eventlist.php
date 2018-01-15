<?php
class JFormFieldEventlist extends JFormField
{
  /**
   * The form field type
   *
   * @var  string
  **/
  public $type = 'Eventlist';

  private function def($val, $default = '') {
    return (isset( $this->params[$val]) && (string) $this->params[$val] != '') ?
      (string) $this->params[$val] : $default;
  }

  protected function getInput() {
    //initialize CiviCRM
    require_once JPATH_ROOT.'/administrator/components/com_civicrm/civicrm.settings.php';
    require_once 'CRM/Core/Config.php';
    $config = CRM_Core_Config::singleton();

    $_class = $this->def('class');
    $_size = $this->def('size', 10);

    $query = "
      SELECT id, title
      FROM civicrm_event
      WHERE is_active = 1
        AND is_public = 1
        AND is_template != 1
      ORDER BY title
    ";
    $dao = CRM_Core_DAO::executeQuery($query);
    $options = array();
    while ($dao->fetch()) {
      $options[$dao->id] = $dao->title;
    }

    return JHTML::_('select.genericlist',
      $options,
      $this->name,
      'multiple="multiple" style="width: 250px;" size="'.$_size.'" class="'.$_class.'"',
      'id',
      'title',
      $this->value,
      $this->id
    );
  }
}
