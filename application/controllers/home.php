<?php
class Home
{
  public function index()
  {
    $this->getProducts();
  }
  public function getProducts()
  {
      $service_url = 'https://9b40a3db75dedb2ea5cb299a0d859a5d:0ee7c6b9ab983b9e733d837ef2797e12@manhood-supplies.myshopify.com/admin/products.json';
      //$service_url = 'https://9b40a3db75dedb2ea5cb299a0d859a5d:0ee7c6b9ab983b9e733d837ef2797e12@manhood-supplies.myshopify.com/admin/products/count.json';
      $curl = curl_init($service_url);
/*
       $curl_post_data = array(
            "user_id" => 42,
            "emailaddress" => 'lorna@example.com',
            );
*/
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      //curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
      //curl_setopt($curl, CURLOPT_POSTFIELDS,http_build_query($data));

      $response = curl_exec($curl);
      if(!$response) {
          return false;
      }
      curl_close($curl);
      $decoded = json_decode($response);
      echo "<pre>";
      print_r($decoded);
      echo "</pre>";
       //$xml = new SimpleXMLElement($curl_response);
  }
}
