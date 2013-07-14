<?php 
include_once(dirname(__FILE__)."/../includes/FancyModel.php");
class Variant
{
  protected $thing = null;
  protected $fields = array();

  public function getThing() { return $this->thing; }
  public function setThing($val) {    $this->thing = $val; }

  public function getFields() { return $this->fields; }
  public function setFields($val) {    $this->fields = $val; }

  public static function load($thing)
  {
    $variant = new Variant();
    $variant->thing = $thing;
    //members
    $field_raw = $variant->thing->getData();
    $tmpFields = array();
    foreach($field_raw as $field)
      $tmpFields[$field->getKey()] = $field;

    $variant->fields = $tmpFields;

    return $variant;
  }

  public function toJson()
  {
    $json = array();
    //add 
    foreach($this->fields as $field)
      $json[$field->getKey()] = $field->getValue();
    return $json;
  }

  function __construct()
  {
  } 
}

