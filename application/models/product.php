<?php 
include_once(dirname(__FILE__)."/../includes/FancyModel.php");
class Product
{
  protected $thing = null;
  protected $members = array();
  protected $variants = array();
  protected $options = array();
  protected $images = array();
  protected $image = null;

  public function getThing() { return $this->thing; }
  public function setThing($val) {    $this->thing = $val; }

  public function getMembers() { return $this->members; }
  public function setMembers($val) {    $this->members = $val; }

  public function getVariants() { return $this->variants; }
  public function setVariants($val) {    $this->variants = $val; }

  public function getOptions() { return $this->options; }
  public function setOptions($val) {    $this->options = $val; }

  public function getImages() { return $this->images; }
  public function setImages($val) {    $this->images = $val; }

  //xxx get/set image
  public static function load($thing)
  {
    $product = new Product();
    $product->thing = $thing;
    
    //members
    $members_raw = $product->thing->getData();
    $tmpMembers = array();
    foreach($members_raw as $member)
      $tmpMembers[$member->getKey()] = $member;
    $product->members = $tmpMembers;

    //variants
    $thingVariants = $product->thing->getChildThings("variant");
    $variants = array();
    foreach($thingVariants as $thingVariant)
      $variants[] = Variant::load($thingVariant);
    $product->variants = $variants;

    //options
    $thingOptions = $product->thing->getChildThings("option");
    $options = array();
    foreach($thingOptions as $thingOptions)
      $options[] = Option::load($thingOptions);
    $product->options = $options;
    return $product;
  }
}
