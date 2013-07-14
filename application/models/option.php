<?php 
include_once(dirname(__FILE__)."/../includes/FancyModel.php");
class Option
{
  protected $thing = null;
  protected $fields = array();

  public function getThing() { return $this->thing; }
  public function setThing($val) {    $this->thing = $val; }

  public function getFields() { return $this->fields; }
  public function setFields($val) {    $this->fields = $val; }
 
  public static function load($thing)
  {
    $option = new Option();
    $option->thing = $thing;
    $field_raw = $option->thing->getData();
    $tmpFields = array();
    foreach($field_raw as $field)
      $tmpFields[$field->getKey()] = $field;

    $option->fields = $tmpFields;
    return $option;
  } 
}
