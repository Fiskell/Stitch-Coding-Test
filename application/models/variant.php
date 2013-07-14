<?php 
include_once(dirname(__FILE__)."/../includes/FancyModel.php");
class Variant extends Fancymodel
{
  protected static $childtype = "Variant"; 
  protected static $tablename = "variant"; 

  protected $members = array(
    "sku" => array("type" => "varchar(255)", "value" => NULL),
    "price" => array("type" => "varchar(255)", "value" => NULL),
    "productnumber" => array("type" => "int(11)", "value" => NULL),//product id
    "shopifynumber" => array("type" => "int(11)", "value" => NULL),//id of the variant
    "option1" => array("type" => "varchar(255)", "value" => NULL),
    "option2" => array("type" => "varchar(255)", "value" => NULL),
    "option3" => array("type" => "varchar(255)", "value" => NULL),
    "quantity" => array("type" => "int(11)", "value" => NULL),
  );

  /// @returns The sku of the variant
  public function getSku() { return parent::get("sku");}
  public function setSku($_val) {   parent::set("sku", $_val ); }

  /// @returns The price of the variant
  public function getVariant() { return parent::get("variant"); }
  public function setVariant($_val) {   parent::set("variant", $_val ); }

  /// @returns The shopify product_id of the variant
  public function getProductnumber() { return parent::get("productnumber"); }
  public function setProductnumber($_val) {   parent::set("productnumber", $_val ); }

  /// @returns The shopify id for the variant
  public function getShopifynumber() { return parent::get("Shopifynumber"); }
  public function setShopifynumber($_val) {   parent::set("Shopifynumber", $_val ); }

  /// @returns The name of the first variant option
  public function getOption1() { return parent::get("option1"); }
  public function setOption1($_val) {   parent::set("option1", $_val ); }

  /// @returns The name of the second variant option
  public function getOption2() { return parent::get("option2"); }
  public function setOption2($_val) {   parent::set("option2", $_val ); }

  /// @returns The name of the third variant option
  public function getOption3() { return parent::get("option3"); }
  public function setOption3($_val) {   parent::set("option3", $_val ); }

  /// @returns The quantity
  public function getQuantity() { return parent::get("quantity");}
  public function setQuantity($_val) {   parent::set("quantity", $_val ); }

  protected static $belongsTo = array("Product");
  /// @returns The product this variant is related to 
  public function getProduct() { return parent::getOneRelated("Product"); }
  //set would need to update shopify
  //public function setProduct($_val) {   parent::setOneRelated("Product"); }
}
