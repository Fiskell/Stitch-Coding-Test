<?php 
include_once(dirname(__FILE__)."/../includes/FancyModel.php");
class Data extends Fancymodel
{
  protected static $childtype = "Data"; 
  protected static $tablename = "data"; 

  protected $members = array(
    "key" => array("type" => "text", "value" => NULL),
    "value" => array("type" => "text", "value" => NULL),
  );

  
  /// @returns The name of the product
  public function getKey() { return parent::get("key"); }
  public function setKey($_key) {   parent::set("key", $_key ); }

  /// @returns The type of thing
  public function getValue() { return parent::get("value"); }
  public function setValue($_val) {   parent::set("value", $_val ); }

  protected static $belongsTo = array("Thing");
  /// @returns The thing that this data is related to
  public function getThing() { return parent::getOneRelated("Thing"); }
  public function setThing($_val) {   parent::setOneRelated("Thing", $_val); }
}
