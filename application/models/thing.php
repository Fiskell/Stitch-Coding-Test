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
  /*
  * @param id of the product to search for, returns all products if not set
  * @returns variants based off of id filter
  */
  public static function getVariants($product_id, $variant_id=null)
  {
    $obj = new Thing();
    $obj->db->select('thing.id');
    $obj->db->from('thing');
    $obj->db->where('name = "product"');
    $obj->db->where('altid', $product_id);
    $query = $obj->db->get();
    $prodId = -1;
    if(count($query->result()) == 1) 
    {
      foreach($query->result() as $row)
        $prodId = $row->id;
    }
    else
    {
      throw new InvalidParameterException($product_id . " is not a valid product id");
    }
   
    //get all variants now that we have right prod id 
    $obj->db->select('thing.id');
    $obj->db->from('thing');
    $obj->db->where('name = "variant"');
    $obj->db->where('thing_id', $prodId);
    if(!is_null($variant_id))
      $obj->db->where('altid', $variant_id);
    $query = $obj->db->get();
    //get things for products
    $thingVariants = array();
    foreach($query->result() as $row)
      array_push($thingVariants, Thing::load($row->id));
    
    //get products
    $variants = array(); 
    foreach($thingVariants as $thingVariants)
      $variants[] = Variant::load($thingVariants);
    return $variants;

    //encode json
    //$json = array();
    //foreach($variants as $variant)
      //$json[] = $variant->toJson();

    //$json = json_encode($json, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_QUOT);

    //echo "<pre>";
    //print_r($json);
    //echo "</pre>";
    //return $products;
  }
  /*
  * @param id of the product to search for, returns all products if not set
  * @returns products based off of id filter
  */
  public static function getProducts($id=null)
  {
    $obj = new Thing();
    $obj->db->select();
    $obj->db->from('thing');
    $obj->db->where('name = "product"');
    if(!is_null($id))
      $obj->db->where('altid', $id);
    $query = $obj->db->get();

    //get things for products
    $thingProducts = array();
    foreach($query->result() as $row)
      $thingProducts[] = Thing::loadFromRow($row);
    
    //get products
    $products = array(); 
    foreach($thingProducts as $thingProduct)
      $products[] = Product::load($thingProduct);
    return $products;

/*

    //$json = json_encode($json, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_QUOT);

    echo "<pre>";
    print_r($json);
    echo "</pre>";
*/
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
