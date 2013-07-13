<?php 
include_once(dirname(__FILE__)."/../includes/FancyModel.php");
class Product extends Fancymodel
{
  protected static $childtype = "Product"; 
  protected static $tablename = "product"; 

  protected $members = array(
    "title" => array("type" => "varchar(255)", "value" => NULL),
    "producttype" => array("type" => "varchar(255)", "value" => NULL),
    "vendor" => array("type" => "varchar(255)", "value" => NULL),
    "option1" => array("type" => "varchar(255)", "value" => NULL),
    "option2" => array("type" => "varchar(255)", "value" => NULL),
    "option3" => array("type" => "varchar(255)", "value" => NULL),
  );

  /// @returns The name of the product
  public function getTitle() { return parent::get("title");
  public function setTitle($_val) {   parent::set("title", $_val };

  /// @returns The name of the product type
  public function getProductType() { return parent::get("producttype");
  public function setProductType($_val) {   parent::set("producttype", $_val };

  /// @returns The name of the vendor
  public function getVendor() { return parent::get("vendor");
  public function setVendor($_val) {   parent::set("vendor", $_val };

  /// @returns The name of the first variant option
  public function getOption1() { return parent::get("option1");
  public function setOption1($_val) {   parent::set("option1", $_val };

  /// @returns The name of the second variant option
  public function getOption2() { return parent::get("option2");
  public function setOption2($_val) {   parent::set("option2", $_val };

  /// @returns The name of the third variant option
  public function getOption3() { return parent::get("option3");
  public function setOption3($_val) {   parent::set("option3", $_val };

  function __construct()
  {
    parent::__construct();
  }
}
