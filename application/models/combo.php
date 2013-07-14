<?php 
class Combo
{
  protected $type_a = null;
  protected $type_b = null;
  protected $exists = false;
  
  public static function load($type_a, $type_b, $exists=false)
  {
    $combo = new Combo();
    $combo->type_a = $type_a;
    $combo->type_b = $type_b;
    $combo->exists = $exists;
    return $combo;
  }

  public function toJson()
  {
    $json = array();
    $json["option1"] = $this->type_a;
    $json["option2"] = $this->type_b;
    $json["exists"] = $this->exists;
    return $json;
  }
}
