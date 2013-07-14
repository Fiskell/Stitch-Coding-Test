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

  public function toJson()
  {
    $json = array();
    //add 
    foreach($this->fields as $field)
      $json[$field->getKey()] = $field->getValue();
    return $json;
  }

  /// @returns array of options related to product
  public function getByProduct($product)
  {
    $this->db->select();
    $this->db->from('thing');
    $this->db->where('thing_id', $product->getThing()->getId());
    $query = $this->db->get();
    
    $thingOptions = array();
    foreach($query->result() as $row)
      $thingOptions[] = Thing::loadFromRow($row);
  
    //get options
    $options = array(); 
    foreach($thingOptions as $thingOptions)
      $options[] = Option::load($thingOptions);
   
    return $options; 
  }
}
