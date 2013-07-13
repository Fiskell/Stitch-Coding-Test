<?php 
class Shopify_api
{
  //xxx doc
  public static function execute($method, $methodType, $data=null)
  {
    $baseurl = "https://9b40a3db75dedb2ea5cb299a0d859a5d:0ee7c6b9ab983b9e733d837ef2797e12@manhood-supplies.myshopify.com/";
    $curl = curl_init($baseurl.$method);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    switch ($methodType)
    {
      case "GET":
        break;
      case "PUT":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        break;
      case "POST":
        curl_setopt($curl, CURLOPT_POST, true);  
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
        break;
      case "DELETE":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        break;
      default:
        throw new ConstraintFailedException("Invalid method type" . $methodType);
    }

    $response = curl_exec($curl);

    if ($response === false) 
    {
        $info = curl_getinfo($curl);
        curl_close($curl);
        die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }

    curl_close($curl);

    $decoded = json_decode($response);
    return $decoded;
  }
}
