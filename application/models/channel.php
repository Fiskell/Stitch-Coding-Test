<?php 
class Channel
{
  const Shopify = 1;
  const Amazon  = 2;
  const Ebay    = 3;

  private static $channels = array(
    "Shopify",
    "Amazon",
    "Ebay",
  );

  private $id = -1;
  private $desc;
  
  /// @returns The name of the distribution channel
  public function getName() { return $this->name; }

  /// @returns The ID of the distribution channel
  public function getId() { return $this->id; }

  /*
  * @returns An instance for the corresponding ID
  * @param $_id the id of the distribution channel
  * @throws DumbassDeveloperException The ID is invalid
  */
  public static function load($_id)
  {
    if(($_id < 1) || ($_id > count(static::$channels)))
      throw new DumbassDeveloperException("Channel id " .$_id." is not valid.");
    $channel = new Channel();
    $channel->id = $_id;
    $channel->name = static::$channels[$_id -1];
    return $state;
  }

  /// @returns An array of all the possible distribution channels
  public static function getAll()
  {
    $numChannels = count(static::$channels);
    $insts = array();
    for($i = 1; $i <= $channels; $i++)
    {
      $insts[] = Channel::load($i);
    }
    return $insts;
  }
}
