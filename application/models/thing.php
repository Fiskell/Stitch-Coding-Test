<?php 
include_once(dirname(__FILE__)."/../includes/FancyModel.php");
class Thing extends Fancymodel
{
  protected static $childtype = "Thing"; 
  protected static $tablename = "thing"; 

  protected $members = array(
    "name" => array("type" => "text", "value" => NULL),
    "channel" => array("type" => "int(11)", "value" => NULL),
    "altid" => array("type" => "int(11)", "value" => NULL),//alternate id 
  );

  /// @returns The name of the product
  public function getName() { return parent::get("name"); }
  public function setName($_val) {   parent::set("name", $_val ); }

  /// @returns The name of the product
  public function getChannel() { return parent::get("channel"); }
  public function setChannel($_val) {   parent::set("channel", $_val ); }

  /// @returns The id specified on the channel side
  public function getAltid() { return parent::get("altid"); }
  public function setAltid($_val) {   parent::set("altid", $_val ); }

  protected static $belongsTo = array("Thing");
  /// @returns The thing that this data is related to
  public function getThing() { return parent::getOneRelated("Thing"); }
  public function setThing($_val) {   parent::setOneRelated("Thing", $_val); }

  public static $hasMany = array("Data");
  /// @returns Array of data objects related to a Thing
  public function getData() { return Data::getAllRelatedTo($this); }

  /*
  * @param The alt id
  * @returns Thing object loaded based on altid
  * @throws DumbassDeveloper Exception if multiple rows with specified altid exist
  * @throws InstanceNotFound Exception if no rows with specified altid exist
  */
  public static function loadByAltId($_id)
  {
    $obj = new Thing();
    $obj->db->select();
    $obj->db->from('thing'); 
    $obj->db->where('altid', $_id);
    $query = $obj->db->get();
    $queryCount = count($query->result());
    if($queryCount > 1)
      throw new DumbassDeveloperException("Multiple things with altid " . $_id ); 
    if($queryCount == 0)
      throw new InstanceNotFoundException("No instance found with altid " . $_id);

    foreach($query->result() as $row)
      return Thing::loadFromRow($row);
  }

  public static function getProducts($id, $fields)
  {
    $obj = new Thing();
    $obj->db->select();
    $obj->db->from('thing');
    $obj->db->where('name = "product"');
    if(!is_null($id))
      $obj->db->where('altid', $id);
    $query = $obj->db->get();

    $thingProducts = array();
    foreach($query->result() as $row)
      $thingProducts[] = Thing::loadFromRow($row);
    
    //echo "<pre>";
    //print_r($thingProducts);
    //echo "</pre>";
    $products = array(); 
    foreach($thingProducts as $thingProduct)
    {
      $products[] = Product::load($thingProduct);
    }
    echo "<pre>";
    print_r($products);
    echo "</pre>";
    //return $products;
  }

  public function getChildThings($thingName)
  {
    $this->db->select();
    $this->db->from('thing');
    $this->db->where('name', $thingName);
    $this->db->where('thing_id', $this->getId());
    $query = $this->db->get();
  
    $children = array();
    foreach($query->result() as $row)
    {
      $children[] = Thing::loadFromRow($row);
    }
    return $children;
  }
}
