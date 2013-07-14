<?php 
include_once(dirname(__FILE__)."/../includes/FancyModel.php");
class Product extends Fancymodel
{
  protected static $childtype = "Product"; 
  protected static $tablename = "product"; 

  protected $members = array(
    "shopifyid" => array("type" => "int(11)", "value" => NULL),
    "title" => array("type" => "varchar(255)", "value" => NULL),
    "description" => array("type" => "text", "value" => NULL),
    "producttype" => array("type" => "varchar(255)", "value" => NULL),
    "vendor" => array("type" => "varchar(255)", "value" => NULL),
    "optiontype1" => array("type" => "varchar(255)", "value" => NULL),
    "optiontype2" => array("type" => "varchar(255)", "value" => NULL),
    "optiontype3" => array("type" => "varchar(255)", "value" => NULL),
  );

  /// @returns The name of the product
  public function getShopifyid() { return parent::get("shopifyid"); }
  public function setShopifyid($_val) {   parent::set("shopifyid", $_val ); }

  /// @returns The name of the product
  public function getTitle() { return parent::get("title"); }
  public function setTitle($_val) {   parent::set("title", $_val ); }

  /// @returns The description of the product
  public function getDescription() { return parent::get("description"); }
  public function setDescription($_val) {   parent::set("description", $_val ); }

  /// @returns The name of the product type
  public function getProductType() { return parent::get("producttype"); }
  public function setProductType($_val) {   parent::set("producttype", $_val ); }

  /// @returns The name of the vendor
  public function getVendor() { return parent::get("vendor"); }
  public function setVendor($_val) {   parent::set("vendor", $_val ); }

  /// @returns The name of the first variant option
  public function getOptionType1() { return parent::get("optiontype1"); }
  public function setOptionType1($_val) {   parent::set("optiontype1", $_val ); }

  /// @returns The name of the second variant option
  public function getOptionType2() { return parent::get("optiontype2"); }
  public function setOptionType2($_val) {   parent::set("optiontype2", $_val ); }

  /// @returns The name of the third variant option
  public function getOptionType3() { return parent::get("optiontype3"); }
  public function setOptionType3($_val) {   parent::set("optiontype3", $_val ); }

  function __construct()
  {
    parent::__construct();
  }
}
