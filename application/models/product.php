<?php 
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

  public function getFields() { return $this->fields; }
  public function setFields($val) {    $this->fields = $val; }

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
    $fields_raw = $product->thing->getData();
    $tmpFields = array();
    foreach($fields_raw as $field)
      $tmpFields[$field->getKey()] = $field;
    $product->fields = $tmpFields;

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

  public function toJson()
  {
    $json = array();
    //add 
    foreach($this->fields as $field)
      $json[$field->getKey()] = $field->getValue();

    $jsonVariants = array();
    foreach($this->variants as $variant)
      $jsonVariants[] = $variant->toJson();
    $json["variants"] = $jsonVariants;

    $jsonOptions = array();
    foreach($this->options as $options)
      $jsonOptions[] = $options->toJson();
    $json["options"] = $jsonOptions;

    return $json;
  }

  public function getCombos()
  {
   
  }
}
