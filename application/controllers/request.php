<?php
class Request extends CI_Controller
{
/*------------------------*/
/*GET Methods*/
/*------------------------*/

  /*
  *Products and Variants
  */ 
  public function products()
  {
    $product_id = null;
    $fourth = null;
    $variant_id = null;

    if($this->uri->segment(3) !== false)
      $product_id = $this->uri->segment(3);
    if($this->uri->segment(4) !== false)
      $fourth = $this->uri->segment(4); 
    if($this->uri->segment(5) !== false)
      $variant_id = $this->uri->segment(5);

    //request/products
    if(is_null($product_id))
      $products = Thing::getProducts();

    //request/products/{product_id}
    else if(!is_null($product_id) && is_null($fourth))
      $products = Thing::getProducts($product_id);

    //request/products/{product_id}/variants
    else if(!is_null($fourth) && is_null($variant_id))
      $products = Thing::getVariants($product_id);
    
    //$products = Thing::getProducts($id, $fields);
    else if(!is_null($fourth) && !is_null($variant_id))
      $products = Thing::getVariants($product_id, $variant_id);
  }

/*------------------------*/

  /*
  *Images
  */ 
/*------------------------*/

  /*
  *Combos
  */ 
  public function combos()
  {
    $product_id = null;

    if($this->uri->segment(3) !== false)
      $product_id = $this->uri->segment(3);

    $product = Product::loadByAltId($product_id);

    $options = Option::getByProduct($product);

      
  }
  
/*------------------------*/

/*------------------------*/
/*POST Methods*/
/*------------------------*/

  /*
  *Sync
  */
  public function sync()
  {
    $channel = Channel::Shopify;
    //get all products from shopify
    $products = Shopify_api::execute('admin/products.json', 'GET'); 
    //first go through products  
     
    Fancymodel::transStart();
    foreach($products['products'] as $product)
    {
      $id = $product['id'];
      $thingProduct = null;
      try
      {
        $thingProduct = Thing::loadByAltId($id); 
      }
      catch(InstanceNotFoundException $ex)
      {
        //create a thing for the top level product
        $thingProduct = new Thing();
        $thingProduct->setAltid($id);
        $thingProduct->setName("product");
        $thingProduct->setChannel($channel);
        $thingProduct->save();
      }
      $thingData_raw = $thingProduct->getData(); //indexed numerically
      //make array indexed by key
      $thingDatas = array();//indexed by key
      foreach($thingData_raw as $thingData)
        $thingDatas[$thingData->getKey()] = $thingData;


      //$productMembers = get_object_vars($product); 
      foreach($product as $key => $value)
      {
        //store the things and data for a variant
        if($key === "variants")
        {
          $this->syncChildren("variant", "variants", $product);
        }
        //store the things and data for options
        else if($key === "options")
        {
          $this->syncChildren("option", "options", $product);
        }
        //store the things and data for images 
        else if($key === "images" || $key === "image")
        {
        }
        else//not a special type of member
        {
          //create a new data if not exist in thingData
          if(!isset($thingDatas[$key]))
          {
            $data = new Data();
            $data->setKey($key);
            $data->setValue($value);
            $data->setThing($thingProduct);
            $data->save();
          }
          else//update the data in db
          {
            $dbData = $thingDatas[$key];
            if($dbData->getValue() != $value)
            {
              $dbData->setValue($value);
              $dbData->save();
            }
          }
        }

      }
    }
    Fancymodel::transCommit();
    echo "sync complete";
  }

  protected function syncChildren($thingName, $member, $parent)
  {
    if(!isset($parent[$member]))
      throw new DumbassDeveloperException($member ." is not a child member of given parent");

    $channel = Channel::Shopify;
    $children = $parent[$member];
    $parentThing = Thing::loadByAltId($parent["id"]);
    foreach($children as $child)
    {
      $id = $child['id'];
      $thingChild = null;
      try
      {
        $thingChild = Thing::loadByAltId($id); 
      }
      catch(InstanceNotFoundException $ex)
      {
        //create a thing for the top level product
        $thingChild = new Thing();
        $thingChild->setAltid($id);
        $thingChild->setName($thingName);
        $thingChild->setChannel($channel);
        $thingChild->setThing($parentThing);//set the parent of the child thing
        $thingChild->save();
      }     
      //all data associated to the child
      $thingData_raw = $thingChild->getData(); //indexed numerically
      //make array indexed by key
      $thingDatas = array();//indexed by key
      foreach($thingData_raw as $thingData)
        $thingDatas[$thingData->getKey()] = $thingData;


      foreach($child as $key => $value)
      {
        //create a new data if not exist in thingData
        if(!isset($thingDatas[$key]))
        {
          $data = new Data();
          $data->setKey($key);
          $data->setValue($value);
          $data->setThing($thingChild);
          $data->save();
        }
        else//update the data in db
        {
          $dbData = $thingDatas[$key];
          if($dbData->getValue() != $value)
          {
            $dbData->setValue($value);
            $dbData->save();
          }
        }     
      }
    }
  }

}
