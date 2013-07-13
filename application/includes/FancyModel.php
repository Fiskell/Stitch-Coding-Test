<?php
include_once(dirname(__FILE__)."/../includes/exceptions.php");

/**
 * Base class for all CI app models
 *
 * This class collects all common functionality for dealing with the database
 * in one place. To use it, create a new class that extends this one. You must
 * create a constructor, a varaibles containing the class and table name, an
 * array of the model's table columns, and getters and setters. If your model
 * allows deleting from its table, override the delete() method too.
 *
 * The array $members must contain keys for each database column the model uses.
 * (Columns will be automatically created for relationship foreign keys.) The
 * array key should be the column name, and the value is another array
 * containing the keys:
 *
 * - "type"
 * The SQL type of the column.
 *
 * - "default"
 * Optional. This is the SQL default for the column. If this is not present,
 * NULL is assumed and used by FancyModel when creating or modifying the
 * database.
 *
 * - "value"
 * This is a placeholder for the child class to store the actual value of the
 * member variable/column. Typically it should be null in the class definition.
 *
 * - "constraints"
 * Optional. This is an array of the constraints that should be checked at
 * save() on this member variable. For more info, see checkValue().
 *
 * To handle one-to-many relationships, use the $belongsTo and $hasMany static
 * members. Use $belongsTo on the side of relationship that should store the
 * foreign key in its database table. Use $hasMany in the related model to
 * allow that model to traverse the relationship and remove relationships on
 * delete. Add methods as needed to provide access to the relationship to
 * calling code.
 *
 * To create many-to-many relationships, add the other model's name to the
 * $manyMany array. Use setManyManyRelated(), getManyManyRelated(),
 * addManyManyRelated(), and removeManyManyRelated() to automatically
 * update the relationship.
 *
 * @code
 * class Sample extends FancyModel
 * {
 * 	protected static $childtype = "Sample";
 * 	protected static $tablename = "sample";
 * 
 * 	protected $members = array(
 * 		"name" => array("type" => "text", "value" => NULL),
 * 		"description" => array("type" => "text", "default" => "a new instance", "value" => NULL)
 * 	);
 * 	
 * 	public function getName() { return parent::get("name"); }
 * 	public function setName($_name) { parent::set("name", $_name); }
 * 	public function getDescription() { return parent::get("description"); }
 * 	public function setDescription($_name) { parent::set("description", $_name); }
 * 
 * 	// relationships
 * 	protected static $belongsTo = array("RelatedModel");
 * 	public function setRelatedModel($_related) { parent::setOneRelated("RelatedModel", $_related); }
 * 	public function getRelatedModel() { return parent::getOneRelated("RelatedModel"); }
 * 	public static function getAllRelatedToRelatedModel($related) { return parent::getAllRelatedTo($related); }
 * 
 * 	protected static $manyMany = array("OtherModel");
 * 	public function getOtherModels() { return parent::getManyManyRelated("OtherModel"); }
 * 	public function setOtherModels($otherModels) { parent::setManyManyRelated("OtherModel", $otherModels); }
 * 
 * 	function __construct()
 * 	{
 * 		parent::__construct();
 * 	}
 * 
 * 	public function delete() { parent::delete(); }
 * }
 * 
 * class RelatedModel extends FancyModel
 * {
 * 	protected static $childtype = "RelatedModel";
 * 	protected static $tablename = "relatedmodel";
 * 
 * 	protected $members = array(
 * 		"name" => array("type" => "text", "value" => NULL)
 * 	);
 * 
 * 	public function getName() { return parent::get("name"); }
 * 	public function setName($_name) { parent::set("name", $_name); }
 * 
 * 	// relationships
 * 	protected static $hasMany = array("Sample");
 * 	public function getSamples() { return Sample::getAllRelatedTo($this); }
 * 	public function setSamples($samples) { parent::setManyRelated("Sample", $samples); }
 * 
 * 	function __construct()
 * 	{
 * 		parent::__construct();
 * 	}
 * 
 * 	public function delete() { parent::delete(); }
 * }
 * @endcode
 *
 * To load a particular Sample in a controller:
 * @code
 * $sample = Sample::load($id);
 * $name = $sample->getName();
 * $desc = $sample->getDescription();
 * @endcode
 *
 * To create a new Sample from your controller, set its members, and save it to
 * the database:
 * @code
 * $sample = new Sample();
 * FancyModel::transStart();
 * $sample->setName("One sample");
 * $sample->setDescription("A sample of how to use FancyModel");
 * $sample->save();
 * FancyModel::transCommit();
 * @endcode
 *
 * Note that making any change to the database requires using transactions. This
 * class handles rollbacks automatically in save() and delete(), but commits
 * must obviously be done by the calling controller. See transStart(),
 * transCommit(), save(), and delete() for details.
 *
 * To modify an existing Sample row/instance with row id of $id:
 * @code
 * $sample = Sample::load($id);
 * FancyModel::transStart();
 * $sample->setName("New name sample");
 * $sample->setDescription("A sample of how to modify a FancyModel");
 * $sample->save();
 * FancyModel::transCommit();
 * @endcode
 *
 * To delete an existing Sample row/instance with row id of $id (assuming the
 * delete() method is overridden in the Sample class):
 * @code
 * $sample = Sample::load($id);
 * FancyModel::transStart();
 * $sample->delete();
 * FancyModel::transCommit();
 * @endcode
 *
 * The class only supports a single relationship of each type between any two
 * models. If two distinct relationships (or named relationships) are needed, a
 * third model class must be used to name and manage the additional
 * relationships. In most cases, it's good design to use a third class for
 * complex relationships anyway.
 *
 * Fixed naming conventions are used for foreign key database columns and
 * many-to-many relationship tables. See getFkColumnName() and
 * getManyManyTableName() for details.
 */
class FancyModel extends CI_Model
{
	protected static $childtype;	///< The type of the child class
	protected static $tablename;	///< The database table name
	/// @returns The database table for the child class
	public static function getTablename() { return static::$tablename; }

	/**
	 * Data members of this instance
	 *
	 * This is an array of db column names, their SQL type, and any constraints on
	 * their value.
	 * @code
	 * protected $members = array(
	 * 	"name" => array("type" => "VARCHAR(64)", "value" => NULL,
	 * 		"constraints" => array("required" => "true", "maxlength" => 64)),
	 * 	"description" => array("type" => "TEXT", "value" => NULL,
	 * 		"constraints" => array("maxlength" => 5000)),
	 * );
	 * @endcode
	 * 
	 * Constraints are checked when save() is called. For a list of defined constraints, see checkValue(). 
	 */
	protected $members;
	protected $id = -1;	///< The row id of this instance
	const idType = "bigint(20)"; ///< The SQL type for id
	
	protected static $belongsTo = array();	///< Array of one-to-many related models (where this model is the many side and stores the fk)
	protected static $hasMany = array();		///< Array of one-to-many related models (where this model is the one side)
	protected static $manyMany = array();		///< Array of many-to-many related models
	
	/// @returns An array of one-to-many related model names (where this model is the many side and stores the fk)
	public static function getBelongsToClasses() { return static::$belongsTo; }
	/// @returns An array of one-to-many related model names (where this model is the one side)
	public static function getHasManyClasses() { return static::$hasMany; }
	/// @returns An array of many-to-many related model names
	public static function getManyManyClasses() { return static::$manyMany; }
	
	private static $transactive = false;	///< true if a transaction is active
	
	protected $constraintErrorMessages = array();	///< Array of error messages from failed constraint checking
	
	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();

		// add relationship fk's to the members array - this makes the save()
		// method handle relationships automatically
		if(static::$belongsTo != NULL)
		{
			foreach(static::$belongsTo as $relatedClass)
			{
				$columnName = self::getFkColumnName($relatedClass::$tablename);
				$this->members[$columnName] = array("type" => self::idType, "value" => NULL);
			}
		}
		
		$this->load->database();
	}

	/// @returns the db id of this instance
	public function getId() { return $this->id; }

	/**
	 * Checks this model's schema against the database
	 *
	 * Generated SQL is valid for MySQL 5.5 and may need to be changed for other
	 * versions and databases.
	 * 
	 * This method does not check for many-to-many relationship tables that are
	 * no longer needed.
	 *
	 * @returns An array of SQL statements required to make the database schema match this class
	 * @throws DumbassDeveloperException The method was called on FancyModel, not a child class
	 */
	public function diffDb()
	{
		if(!isset(static::$childtype))
		{
			throw new DumbassDeveloperException("FancyModel.diffDb() must be called on a concrete child class.");
		}
		
		$sql = array();
		
		// check our table
		if(!$this->db->table_exists(static::$tablename))
		{
			// table doesn't exist; build sql to create table
			$cmd = "CREATE TABLE `".static::$tablename."` (`id` ".self::idType." NOT NULL AUTO_INCREMENT, ";
			
			// this foreach catches the one-to-many fk columns because they are added to $members in the ctor
			foreach($this->members as $colname => $colinfo)
			{
				// generate sql for this table column
				$cmd .= "`".$colname."` ".$colinfo["type"]." DEFAULT ";
				
				if(isset($colinfo["default"]))
					$cmd .= $colinfo["default"].", ";
				else
					$cmd .= "NULL, ";
			}
			
			$cmd .= "PRIMARY KEY (`id`)";

			// add indexes to all the foreign key columns
			if(static::$belongsTo != NULL)
			{
				foreach(static::$belongsTo as $relatedClass)
				{
					$fkColumnName = self::getFkColumnName($relatedClass::$tablename);
					$cmd .= ", INDEX(`".$fkColumnName."`)";
				}
			}
			
			$cmd .= " );";
			$sql[] = $cmd;
		}
		else // our table exists, make sure it matches
		{
			// check the id column
			if(!$this->db->field_exists("id", static::$tablename))
			{
				// the id column doesn't exist, this is really bad...
				throw new DbHosedException("Table '".static::$tablename."' exists, but doesn't have a id column. Check the database and fix it manually.");
			}
			else
			{
				$query = $this->db->query("DESCRIBE `".static::$tablename."` `id`");
				
				// check the column type
/*				print("*** id default: '".$query->row()->Default."'\n");
				print("*** id null: '".$query->row()->Null."'\n");
				print("*** id key: '".$query->row()->Key."'\n");
				print("*** id extra: '".$query->row()->Extra."'\n");
*/
				if($query->row()->Key != "PRI")
				{
					// the id column doesn't have a primary index, this is really bad...
					throw new DbHosedException("Table '".static::$tablename."' doesn't have a primary index on the id column. Check the database and fix it manually.");
				}
				
				$typeOk = true;
				if($query->row()->Type != self::idType)
					$typeOk = false;
				if($query->row()->Default != "")
					$typeOk = false;
				if($query->row()->Null != "NO")
					$typeOk = false;
				if($query->row()->Extra != "auto_increment")
					$typeOk = false;
				if(!$typeOk)
					$sql[] = "ALTER TABLE `".static::$tablename."` MODIFY COLUMN `id` ".self::idType." NOT NULL AUTO_INCREMENT;";
			}
			
			// check all the other columns
			foreach($this->members as $colname => $colinfo)
			{
				if(!$this->db->field_exists($colname, static::$tablename))
				{
					// the column doesn't exist, so create sql to add it
					$cmd = "ALTER TABLE `".static::$tablename."` ADD COLUMN `".$colname."` ".$colinfo["type"]." DEFAULT ";
					
					if(isset($colinfo["default"]))
						$cmd .= $colinfo["default"].";";
					else
						$cmd .= "NULL;";

					$sql[] = $cmd;
					
					// if this is a foreign key, create an index
					if(static::$belongsTo != NULL)
					{
						foreach(static::$belongsTo as $relatedClass)
						{
							$fkColumnName = self::getFkColumnName($relatedClass::$tablename);
							if($fkColumnName == $colname)
							{
								$sql[] = "CREATE INDEX `".$colname."` ON `".static::$tablename."` (`".$colname."`);";
							}
						}
					}
				}
				else
				{
					$query = $this->db->query("DESCRIBE `".static::$tablename."` `".$colname."`");
					
					// update the column type if necessary
					$type = $query->row()->Type;
					if($type != $colinfo["type"])
						$sql[] = "ALTER TABLE `".static::$tablename."` MODIFY COLUMN `".$colname."` ".$colinfo["type"].";";
					
					// update the column default if necessary 
					$default = $query->row()->Default;
					if(isset($colinfo["default"]))
					{
						if($default != $colinfo["default"])
							$sql[] = "ALTER TABLE `".static::$tablename."` ALTER COLUMN `".$colname."` SET DEFAULT ".$colinfo["default"].";";
					}
					else
					{
						if($default != NULL)
							$sql[] = "ALTER TABLE `".static::$tablename."` ALTER COLUMN `".$colname."` SET DEFAULT NULL;";
					}
					
					// if this is a foreign key, make sure it has an index
					if(static::$belongsTo != NULL)
					{
						foreach(static::$belongsTo as $relatedClass)
						{
							$fkColumnName = self::getFkColumnName($relatedClass::$tablename);
							if(($fkColumnName == $colname) && ($query->row()->Key != "MUL"))
							{
								$sql[] = "CREATE INDEX `".$colname."` ON `".static::$tablename."` (`".$colname."`);";
							}
						}
					}
				}
			}
			
			// check existing table columns to see if we need to drop any
			$columns = $this->db->field_data(static::$tablename);
			foreach($columns as $col)
			{
				if(($col->name != "id") && !array_key_exists($col->name, $this->members))
				{
			  	// this column doesn't have a corresponding class member, create sql to remove the column
			  	$sql[] =  "ALTER TABLE `".static::$tablename."` DROP COLUMN `".$col->name."`;";
				}
			} 
		}
		
		// check for and create rel_ tables this class is responsible for
		foreach(static::$manyMany as $relatedModel)
		{
			if($this->isOwnerManyManyTable($relatedModel))
			{
				$relTablename = self::getManyManyTableName(static::$tablename, $relatedModel::$tablename);
				$ourColumnName = self::getFkColumnName(static::$tablename);
				$theirColumnName = self::getFkColumnName($relatedModel::$tablename);
				if(!$this->db->table_exists($relTablename))
				{
					$cmd = "CREATE TABLE `".$relTablename."` (";
					$cmd .= "`".$ourColumnName."` ".self::idType." NOT NULL, ";
					$cmd .= "`".$theirColumnName."` ".self::idType." NOT NULL, ";
					$cmd .= "PRIMARY KEY (`".$ourColumnName."`,`".$theirColumnName."`) );";
					$sql[] = $cmd;
				}
				else
				{
					$columnNames = array($ourColumnName, $theirColumnName);
					foreach($columnNames as $colname)
					{
						if(!$this->db->field_exists($colname, $relTablename))
						{
							throw new DbHosedException("Table '".$relTablename."' exists but is missing column '".$colname."'. Check the database and fix it manually.");
						}
						else
						{
							$query = $this->db->query("DESCRIBE `".$relTablename."` `".$colname."`");

							if($query->row()->Key != "PRI")
								throw new DbHosedException("Column '".$colname."' in '".$relTablename."' doesn't have a primary index. Check the database and fix it manually.");

							$typeOk = true;
							if($query->row()->Type != self::idType)
								$typeOk = false;
							if($query->row()->Default != "")
								$typeOk = false;
							if($query->row()->Null != "NO")
								$typeOk = false;
							if($query->row()->Extra != "")
								$typeOk = false;
							if(!$typeOk)
								$sql[] = "ALTER TABLE `".$relTablename."` MODIFY COLUMN `".$colname."` ".self::idType." NOT NULL;";
						}
					}

					// check existing table columns to see if we need to drop any
					$columns = $this->db->field_data($relTablename);
					foreach($columns as $col)
					{
						if(!in_array($col->name, $columnNames))
							throw new DbHosedException("Extra column '".$col->name."' found in '".$relTablename."'. Check the database and fix it manually.");
					} 
				}
			}
		}	
		return $sql;
	}

	/**
	 * @returns The value of a class member
	 * @throws MemberNotFoundException $_member is not part of the class
	 */
	protected function get($_member)
	{
		if(!array_key_exists($_member, $this->members))
		{
			throw new MemberNotFoundException("Member ".$_member." is not present in ".static::$childtype.".");
		}
		
		return $this->members[$_member]["value"];
	}
	
	/**
	 * Get a raw copy of the model's member values and relationship keys
	 *
	 * This method returns the raw members as they are stored in the object. Any
	 * translation, checking, etc that might be performed by the child class is
	 * not applied. This method is mainly used for generic REST access to models.
	 *
	 * @returns An array of the instance's members
	 */
	public function getMembers()
	{
		$temp = array("id" => $this->getId());
		foreach($this->members as $member => $val)
		{
			$temp[$member] = $val["value"];
		}
		return $temp;
	}
	
	/**
	 * Sets a member variable
	 *
	 * Use this method to set member values.
	 *
	 * @param $_member The name of the member
	 * @param $newvalue The new value
	 * @throws MemberNotFoundException $_member is not part of the class
	 */
	protected function set($_member, $newvalue)
	{	
		if(!array_key_exists($_member, $this->members))
		{
			throw new MemberNotFoundException("Member ".$_member." is not present in class.");
		}
		
		$this->members[$_member]["value"] = $newvalue;
	}

	/**
	 * Checks a member variable against its constraints
	 *
	 * Valid constraints are:
	 * - required - A value is required. If this key exists in the constraints array with any value, it is enforced.
	 * - maxlength - Maximum length allowed.
	 * - minlength - Minimum length allowed.
	 * - length - The value must be exactly the length specified.
	 * - gt - The numeric value must be greater than the specified parameter.
	 * - lt - The numeric value must be less than the specified parameter.
	 * - alpha - Only alphabetical characters allowed.
	 * - numeric - Only numeric characters allowed (0-9).
	 * - alphanumeric - Only alphanumeric characters allowed.
	 * - regex - Value must match the specified regex.
	 * - custom - Call the specified child class method to check the value. The method must take a single argument and throw InvalidParameterException if the value is invalid.
	 *
	 * No constraints imply "required" - they all allow NULL. If NULL is not a
	 * valid value, add "required" to the value's constraint list.
	 *
	 * If the value meets all the constraints, this function simply returns.
	 *
	 * @param $_member The name of the member
	 * @param $newvalue The new value
	 * @throws InvalidParameterException The value was out of range or not valid
	 */
	protected function checkValue($_member, $newvalue)
	{
		// enforce constraints
		if(isset($this->members[$_member]["constraints"]))
		{
			if((!isset($newvalue)) && array_key_exists("required", $this->members[$_member]["constraints"]))
			{
				throw new InvalidParameterException("Parameter \"".$_member."\" is required", $_member, $newvalue);
			}
			
			if(isset($newvalue))
			{
				foreach($this->members[$_member]["constraints"] as $constraint => $limit)
				{
					if($constraint == "required")
					{
						if(strlen($newvalue) == 0)
						{
							throw new InvalidParameterException("Parameter \"".$_member."\" is required.", $_member, $newvalue);
						}
					}
					else if($constraint == "maxlength")
					{
						if(strlen($newvalue) > $limit)
						{
							throw new InvalidParameterException("Parameter \"".$_member."\" must be no more than ".$limit." characters long.", $_member, $newvalue);
						}
					}
					else if($constraint == "minlength")
					{
						if(strlen($newvalue) < $limit)
						{
							throw new InvalidParameterException("Parameter \"".$_member."\" must be at least ".$limit." characters long.", $_member, $newvalue);
						}
					}
					else if($constraint == "length")
					{
						if(strlen($newvalue) != $limit)
						{
							throw new InvalidParameterException("Parameter \"".$_member."\" must be exactly ".$limit." characters long.", $_member, $newvalue);
						}
					}
					else if($constraint == "gt")
					{
						if(!($newvalue > $limit))
						{
							throw new InvalidParameterException("Parameter \"".$_member."\" must be greater than ".$limit.".", $_member, $newvalue);
						}
					}
					else if($constraint == "lt")
					{
						if(!($newvalue < $limit))
						{
							throw new InvalidParameterException("Parameter \"".$_member."\" must be less than ".$limit.".", $_member, $newvalue);
						}
					}
					else if($constraint == "alpha")
					{
						if(preg_match("/^[[:alpha:]]*$/", $newvalue) == 0)
						{
							throw new InvalidParameterException("Parameter \"".$_member."\" must contain only alphabetical (a-z, A-Z) characters.", $_member, $newvalue);
						}
					}
					else if($constraint == "numeric")
					{
						if(preg_match("/^[[:digit:]]*$/", $newvalue) == 0)
						{
							throw new InvalidParameterException("Parameter \"".$_member."\" must contain only numeric (0-9) characters.", $_member, $newvalue);
						}
					}
					else if($constraint == "alphanumeric")
					{
						if(preg_match("/^[[:alnum:]]*$/", $newvalue) == 0)
						{
							throw new InvalidParameterException("Parameter \"".$_member."\" must contain only alphanumeric (a-z, A-Z, 0-9) characters.", $_member, $newvalue);
						}
					}
					else if($constraint == "regex")
					{
						if(preg_match($limit, $newvalue) == 0)
						{
							throw new InvalidParameterException("Parameter \"".$_member."\" must match the pattern ".$limit.".", $_member, $newvalue);
						}
					}
					else if($constraint == "custom")
					{
						$this->$limit($newvalue);
					}
				} // end foreach constraint
			}
		}
	}

	/**
	 * Retrieve all instances from the db
	 * @returns An array of child instances
	 */
	public static function getAll($fields=NULL)
	{
		$all = array();
		$class = new static::$childtype;
		$class->db->from(static::$tablename);
		//order by if applicable
		if($fields != NULL)
		{
			foreach($fields as $key => $value)
			{
				if(!array_key_exists($key, $class->members))
				{
					throw new DumbassDeveloperException($key . " is not a member of " . static::$childtype);
				}
				if(($value != "asc") && ($value != "desc"))
				{
					throw new DumbassDeveloperException($value . " is not a valid ordering parameter(asc, desc)");
				}
				$class->db->order_by($key, $value);
			}
		}
		$query = $class->db->get();
		foreach($query->result() as $row)
		{
			$inst = new static::$childtype;
			$inst->id = $row->id;
			foreach($inst->members as $key => $value)
			{
				if(!property_exists($row, $key))
				{
					throw new DbHosedException("Column " . $key . " does not exist in table " . static::$tablename);
				}
				$inst->members[$key]["value"] = $row->$key;
			}
			
			array_push($all, $inst);
		}
		return $all;
	}

	/**
	 * Load an instance from the db
	 * @param $_id The id of the instance
	 * @returns The instance, or NULL if $_id is NULL
	 * @throws InstanceNotFoundException The instance with the given id is not in the db
	 * @throws DbHosedException A serious db error occurred
	 */
	public static function load($_id)
	{
		if($_id == NULL)
		{
			return NULL;
		}
		
		$inst = new static::$childtype();
		$inst->db->from(static::$tablename);
		$inst->db->where('id', $_id);
		$query = $inst->db->get();
		if($query->num_rows() == 0)
		{
			throw new InstanceNotFoundException("No ".static::$childtype." instance found with id " . $_id);
		}
		if($query->num_rows() > 1)
		{
			throw new DbHosedException("More than one ".static::$childtype." instance found with id " . $_id);
		}

		$inst->id = $query->row()->id;
		foreach($inst->members as $key => $value)
		{
			if(!property_exists($query->row(), $key))
			{
				throw new DbHosedException("Column " . $key . " does not exist in table " . static::$tablename);
			}
			$inst->members[$key]["value"] = $query->row()->$key;
		}
		
		return $inst;
	}

	/**
	 * Load an instance from an existing db query row
	 * @param $_row The query row containing all the class members
	 * @returns The instance
	 * @throws DumbassDeveloperException The class contains members that do not exist in the row
	 */
	public static function loadFromRow($_row)
	{
		$inst = new static::$childtype();
		$inst->id = $_row->id;
		foreach($inst->members as $key => $value)
		{
			if(!property_exists($_row, $key))
			{
				throw new DumbassDeveloperException("Column " . $key . " does not exist in row");
			}
			$inst->members[$key]["value"] = $_row->$key;
		}
		
		return $inst;
	}

	/**
	 * Start a db transaction - required for all db changes
	 *
	 * @throws DbHosedException A serious db error occurred
	 */
	public static function transStart()
	{
		// use CI's manual transaction mode so we can control rollbacks in save() and delete()
		$inst = new FancyModel();
		$inst->db->trans_begin();
		self::$transactive = true;
	}

	/**
	 * Commit a db transaction
	 *
	 * @throws DbHosedException A serious db error occurred
	 */
	public static function transCommit()
	{
		$inst = new FancyModel();
		$inst->db->trans_commit();
		self::$transactive = false;

		if($inst->db->trans_status() === FALSE)
		{
			throw new DbHosedException("transCommit() failed due to a db error: ".$this->db->_error_message());
		}
	}

	/**
	 * Rollback a db transaction
	 *
	 * This method will typically not be required - FancyModel will automatically rollback
	 * transactions when save(), delete(), and other methods that modify the database fail.
	 *
	 * @throws DbHosedException A serious db error occurred
	 */
	public static function transRollback()
	{
		$inst = new FancyModel();
		$inst->db->trans_rollback();
		self::$transactive = false;

		if($inst->db->trans_status() === FALSE)
		{
			throw new DbHosedException("transCommit() failed due to a db error: ".$this->db->_error_message());
		}
	}

	/**
	 * @returns true if a transaction is active
	 */
	public static function isTransActive()
	{
		return self::$transactive;
	}

	/**
	 * Save this instance to the db
	 * 
	 * An active transaction is required before calling this method. If calls
	 * to the database fail, the transaction is rolled back automatically.
	 *
	 * @throws DumbassDeveloperException Method called at an incorrect time or state
	 * @throws ConstraintFailedException A member value did not meet its constraints
	 * @throws DbHosedException A serious db error occurred
	 */
	public function save()
	{
		if(!self::$transactive)
		{
			throw new DumbassDeveloperException("save() called without an active transaction");
		}
		
		$data = array();
		$this->constraintErrorMessages = array();
		foreach($this->members as $column => $stuff)
		{
			try
			{
				$this->checkValue($column, $stuff["value"]);
			}
			catch(InvalidParameterException $ex)
			{
				$this->constraintErrorMessages[$column] = $ex->getMessage();
			}
			$data[$column] = $stuff["value"];
		}
		if(!empty($this->constraintErrorMessages))
		{
			throw new ConstraintFailedException("The values of ".implode(", ", array_keys($this->constraintErrorMessages))." are not valid.");
		}
		
		if($this->id == -1)
		{
			$this->db->insert(static::$tablename, $data);
			$this->id = $this->db->insert_id();
		}
		else
		{
			$this->db->where("id", $this->id);
			$this->db->update(static::$tablename, $data);
		}
		
		if($this->db->_error_number() != 0)
		{
			$this->db->trans_rollback();
			self::$transactive = false;
			throw new DbHosedException("save() failed due to a db error: ".$this->db->_error_message());
		}
	}

	/**
	 * Get the error message for a failed constraint test.
	 *
	 * @param $_membername Member variable that failed
	 * @returns The error message. An empty string is returned if the member didn't have an error.
	 */
	public function getConstraintError($_membername)
	{
		if(empty($this->constraintErrorMessages[$_membername]))
		{
			return "";
		}
		return $this->constraintErrorMessages[$_membername];
	}

	/**
	 * Get all error messages for a failed constraint tests.
	 *
	 * @returns The error messages as a array. Keys are the member name. An empty array is returned if there were no errors.
	 */
	public function getAllConstraintErrors()
	{
		if(empty($this->constraintErrorMessages))
		{
			return array();
		}
		return $this->constraintErrorMessages;
	}
	/**
	 * Removes this instance from the db
	 * 
	 * An active transaction is required before calling this method. If calls
	 * to the database fail, the transaction is rolled back automatically.
	 *
	 * This method is protected so child classes can control if their rows can be
	 * deleted. Override this method as public to give delete() access to calling
	 * classes.
	 *
	 * @throws DumbassDeveloperException Method called at an incorrect time or state
	 * @throws DbHosedException A serious db error occurred
	 */
	protected function delete()
	{
		if(!self::$transactive)
		{
			throw new DumbassDeveloperException("delete() called without a transaction");
		}
		
		if($this->id != -1)
		{
			// remove any existing one-to-many relationships
			if(static::$hasMany != NULL)
			{
				foreach(static::$hasMany as $relatedClass)
				{
					$relatedInstances = $relatedClass::getAllRelatedTo($this);
					foreach($relatedInstances as $inst)
					{
						$inst->setOneRelated(static::$childtype, NULL);
						$inst->save();
					}
				}
			}

			// remove any existing many-to-many relationships
			if(static::$manyMany != NULL)
			{
				foreach(static::$manyMany as $relatedClass)
				{
					$relTableName = self::getManyManyTableName(static::$tablename, $relatedClass::$tablename);
					$ourColumnName = self::getFkColumnName(static::$tablename);

					$this->db->where($ourColumnName, $this->id);
					$this->db->delete($relTableName);
				}
			}
			
			// remove this instance's database row
			$this->db->where('id', $this->id);
			$this->db->delete(static::$tablename);
			$this->id = -1;
		}
			
		if($this->db->_error_number() != 0)
		{
			$this->db->trans_rollback();
			self::$transactive = false;
			throw new DbHosedException("delete() failed due to a db error: ".$this->db->_error_message());
		}
	}

	/**
	 * @param $_tablename A database table name
	 * @returns The column name for a foreign key to the given table
	 */
	public static function getFkColumnName($_tablename)
	{
		return $_tablename."_id";
	}
	
	/**
	 * @param $_tablename1 One database table name
	 * @param $_tablename2 The other database table name
	 * @returns The database table name that holds many-to-many relationships between the two tables
	 */
	public static function getManyManyTableName($_tablename1, $_tablename2)
	{
		$tables = array($_tablename1, $_tablename2);
		sort($tables, SORT_STRING);
		return "rel_".$tables[0]."_".$tables[1];
	}
	
	/**
	 * @param $otherModel The other class
	 * @returns true if this class is responsible for the many-many relationship table with the specified class
	 */
	public static function isOwnerManyManyTable($otherModel)
	{
		if(!isset(static::$childtype))
		{
			throw new DumbassDeveloperException("FancyModel.isOwnerManyManyTable() must be called on a concrete child class.");
		}
		
		$tables = array(static::$tablename, $otherModel::$tablename);
		sort($tables, SORT_STRING);
		return (static::$tablename == $tables[0]);
	}
	
	/**
	 * Gets the related one-to-many instance of a given related model
	 *
	 * Only valid if this class is the "belongsTo" side of the relationship
	 *
	 * @param $_type The class name of the related model
	 * @returns The related instance
	 * @throws DumbassDeveloperException $_type doesn't have the right type of relationship with this class
	 * @throws DbHosedException Relationship refers to a database row that does not exist
	 */
	protected function getOneRelated($_type)
	{
		if(!in_array($_type, static::$belongsTo))
		{
			throw new DumbassDeveloperException(static::$childtype." does not have a one-to-many relationship with ".$_type);
		}
		
		$columnName = self::getFkColumnName($_type::$tablename);
		$relatedId = $this->get($columnName);
		try
		{
			$inst = $_type::load($relatedId);
			return $inst;
		}
		catch(InstanceNotFoundException $ex)
		{
			throw new DbHosedException(static::$childtype." instance ".$this->getId()." related to a non-existent ".$_type." id ".$relatedId);
		}
	}

	/**
	 * Sets this side of a one-to-many relationship
	 *
	 * Only valid if this class is the "belongsTo" side of the relationship
	 *
	 * @param $_type The class name of the related model
	 * @param $_inst The instance this object should be related to, or NULL if the relationship should be removed
	 * @throws DumbassDeveloperException $_type doesn't have the right type of relationship with this class
	 * @throws DumbassDeveloperException A transaction is not active
	 * @throws DumbassDeveloperException $_inst isn't stored in the database
	 */
	protected function setOneRelated($_type, $_inst)
	{
		if(!self::$transactive)
		{
			throw new DumbassDeveloperException("Method called to modify database without an active transaction");
		}
		
		if(!in_array($_type, static::$belongsTo))
		{
			$this->db->trans_rollback();
			self::$transactive = false;
			throw new DumbassDeveloperException(static::$childtype." is not on the many side of a relationship with ".$_type);
		}
		
		if(($_inst != NULL) && ($_inst->getId() == -1))
		{
			$this->db->trans_rollback();
			self::$transactive = false;
			throw new DumbassDeveloperException("Tried to set a relationship to an object not stored in the database");
		}
		
		$columnName = self::getFkColumnName($_type::$tablename);
		if($_inst != NULL)
		{
			$this->members[$columnName]["value"] = $_inst->getId();
		}
		else
		{
			$this->members[$columnName]["value"] = NULL;
		}
	}

	/**
	 * Sets the other side of a one-to-many relationship
	 *
	 * Only valid if this class is the "hasMany" side of the relationship
	 *
	 * @param $_type The class name of the related model
	 * @param $_insts An array of instances this object should be related to, or NULL if all relationships should be removed
	 * @throws DumbassDeveloperException $_type doesn't have the right type of relationship with this class
	 * @throws DumbassDeveloperException A transaction is not active
	 * @throws DumbassDeveloperException An object in $_insts isn't stored in the database
	 */
	protected function setManyRelated($_type, $_insts)
	{
		if(!self::$transactive)
		{
			throw new DumbassDeveloperException("Method called to modify database without an active transaction");
		}
		
		if(!in_array($_type, static::$hasMany))
		{
			$this->db->trans_rollback();
			self::$transactive = false;
			throw new DumbassDeveloperException(static::$childtype." is not on the many side of a relationship with ".$_type);
		}
			
		foreach($_insts as $inst)
		{
			if($inst->getId() == -1)
			{
				$this->db->trans_rollback();
				self::$transactive = false;
				throw new DumbassDeveloperException("Tried to set a relationship to an object not stored in the database");
			}
		}
	
		// unset all the existing relationships
		$currentRelated = $_type::getAllRelatedTo($this);
		foreach($currentRelated as $related)
		{
			$related->setOneRelated(static::$childtype, NULL);
			$related->save();
		}
		
		// set the ones passed in
		foreach($_insts as $inst)
		{
			$inst->setOneRelated(static::$childtype, $this);
			$inst->save();
		}
	}

	/**
	 * Gets all instances related to an instance of another model
	 *
	 * Only valid if this class is the "belongsTo" side of the relationship
	 *
	 * @param $_inst Instance of the other model
	 * @returns An array of objects related to $_inst
	 * @throws DumbassDeveloperException $_inst doesn't have the right type of relationship with this class
	 */
	protected static function getAllRelatedTo($_inst, $fields=NULL)
	{
		if(!in_array($_inst::$childtype, static::$belongsTo))
		{
			throw new DumbassDeveloperException(static::$childtype." does not have a one-to-many relationship with ".$_inst::$childtype);
		}
		
		$columnName = self::getFkColumnName($_inst::$tablename);
		
		$related = array();
		$child = new static::$childtype();
		$child->db->from(static::$tablename);
		$child->db->where($columnName, $_inst->getId());
		if($fields != NULL)
		{
			foreach($fields as $key => $value)
			{
				if(!array_key_exists($key, $child->members))
				{
					throw new DumbassDeveloperException($key . " is not a member of " . static::$childtype);
				}
				if(($value != "asc") && ($value != "desc"))
				{
					throw new DumbassDeveloperException($value . " is not a valid ordering parameter(asc, desc)");
				}
				$child->db->order_by($key, $value);
			}
		}
		$query = $child->db->get();
		foreach($query->result() as $row)
		{
			$relatedInst = new static::$childtype();
			$relatedInst->id = $row->id;
			foreach($relatedInst->members as $key => $value)
			{
				$relatedInst->members[$key]["value"] = $row->$key;
			}
			
			array_push($related, $relatedInst);
		}
		return $related;
	}

	/**
	 * Sets related instances to $this in a many-to-many relationship
	 *
	 * Only valid if this class and the other class have a "manyMany" relationship
	 *
	 * @param $_type The class name of the related model
	 * @param $_insts An array of instances this object should be related to, or NULL if all relationships to $this should be removed
	 * @throws DumbassDeveloperException $_type doesn't have the right type of relationship with this class
	 * @throws DumbassDeveloperException A transaction is not active
	 */
	protected function setManyManyRelated($_type, $_insts)
	{
		if(!self::$transactive)
		{
			throw new DumbassDeveloperException("Method called to modify database without an active transaction");
		}
		
		if(!in_array($_type, static::$manyMany))
		{
			$this->db->trans_rollback();
			self::$transactive = false;
			throw new DumbassDeveloperException(static::$childtype." does not have a many-to-many relationship with ".$_type);
		}
		
		foreach($_insts as $inst)
		{
			if($inst->getId() == -1)
			{
				$this->db->trans_rollback();
				self::$transactive = false;
				throw new DumbassDeveloperException("Tried to set a relationship to an object not stored in the database");
			}
		}

		$relTableName = self::getManyManyTableName(static::$tablename, $_type::$tablename);
		$ourColumnName = self::getFkColumnName(static::$tablename);
		$theirColumnName = self::getFkColumnName($_type::$tablename);

		// unset all the existing relationships to this instance
		$this->db->where($ourColumnName, $this->id);
		$this->db->delete($relTableName);

		// set the relationships to the passed-in instances
		$data[$ourColumnName] = $this->id;
		foreach($_insts as $inst)
		{
			$data[$theirColumnName] = $inst->getId();
			// do not insert if the relationship already exists
			$this->db->from($relTableName);
			$this->db->where($data);
			$query = $this->db->get();
			if($query->num_rows() == 0)
			{
				$this->db->insert($relTableName, $data);
			}
		}
	}

	/**
	 * Adds an instance related to $this in a many-to-many relationship
	 *
	 * Only valid if this class and the other class have a "manyMany" relationship
	 *
	 * This method has no effect if the relationship already exists
	 *
	 * @param $_type The class name of the related model
	 * @param $_inst An instance this object should be related to
	 * @throws DumbassDeveloperException $_type doesn't have the right type of relationship with this class
	 * @throws DumbassDeveloperException A transaction is not active
	 * @throws DumbassDeveloperException $_inst isn't stored in the database
	 */
	protected function addManyManyRelated($_type, $_inst)
	{
		if(!self::$transactive)
		{
			throw new DumbassDeveloperException("Method called to modify database without an active transaction");
		}
		
		if(!in_array($_type, static::$manyMany))
		{
			$this->db->trans_rollback();
			self::$transactive = false;
			throw new DumbassDeveloperException(static::$childtype." does not have a many-to-many relationship with ".$_type);
		}
		
		if($_inst->getId() == -1)
		{
			$this->db->trans_rollback();
			self::$transactive = false;
			throw new DumbassDeveloperException("Tried to set a relationship to an object not stored in the database");
		}
		
		$relTableName = self::getManyManyTableName(static::$tablename, $_type::$tablename);
		$ourColumnName = self::getFkColumnName(static::$tablename);
		$theirColumnName = self::getFkColumnName($_type::$tablename);

		// see if the relationship exists
		$this->db->from($relTableName);
		$this->db->where($ourColumnName, $this->getId());
		$this->db->where($theirColumnName, $_inst->getId());
		$query = $this->db->get();
		if($query->num_rows() == 0)
		{
			// set the relationship to the passed-in instance
			$data[$ourColumnName] = $this->id;
			$data[$theirColumnName] = $_inst->getId();
			$this->db->insert($relTableName, $data);
		}
	}

	/**
	 * Removes an instance related to $this in a many-to-many relationship
	 *
	 * Only valid if this class and the other class have a "manyMany" relationship
	 *
	 * This method has no effect if the relationship does not exist
	 *
	 * @param $_type The class name of the related model
	 * @param $_inst An instance this object should not be related to
	 * @throws DumbassDeveloperException $_type doesn't have the right type of relationship with this class
	 * @throws DumbassDeveloperException A transaction is not active
	 */
	protected function removeManyManyRelated($_type, $_inst)
	{
		if(!self::$transactive)
		{
			throw new DumbassDeveloperException("Method called to modify database without an active transaction");
		}
		
		if(!in_array($_type, static::$manyMany))
		{
			$this->db->trans_rollback();
			self::$transactive = false;
			throw new DumbassDeveloperException(static::$childtype." does not have a many-to-many relationship with ".$_type);
		}

		if($_inst->getId() == -1)
		{
			$this->db->trans_rollback();
			self::$transactive = false;
			throw new DumbassDeveloperException("Tried to set a relationship to an object not stored in the database");
		}

		$relTableName = self::getManyManyTableName(static::$tablename, $_type::$tablename);
		$ourColumnName = self::getFkColumnName(static::$tablename);
		$theirColumnName = self::getFkColumnName($_type::$tablename);

		// see if the relationship exists
		$this->db->from($relTableName);
		$this->db->where($ourColumnName, $this->getId());
		$this->db->where($theirColumnName, $_inst->getId());
		$query = $this->db->get();
		if($query->num_rows() != 0)
		{
			// remove the relationship
			$this->db->where($ourColumnName, $this->id);
			$this->db->where($theirColumnName, $_inst->getId());
			$this->db->delete($relTableName);
		}
	}

	/**
	 * Gets all instances related to this object through a many-to-many relationship
	 *
	 * Only valid if this class and the other class have a "manyMany" relationship
	 *
	 * @param $_type The class name of the related model
	 * @returns An array of instances related to $this
	 * @throws DumbassDeveloperException $_type doesn't have the right type of relationship with this class
	 */
	protected function getManyManyRelated($_type)
	{
		if(!in_array($_type, static::$manyMany))
		{
			throw new DumbassDeveloperException(static::$childtype." does not have a many-to-many relationship with ".$_type);
		}
		
		$relTableName = self::getManyManyTableName(static::$tablename, $_type::$tablename);
		$ourColumnName = self::getFkColumnName(static::$tablename);
		$theirColumnName = self::getFkColumnName($_type::$tablename);

		$related = array();
		$this->db->from($relTableName);
		$this->db->where($ourColumnName, $this->getId());
		$query = $this->db->get();
		foreach($query->result() as $row)
		{
			$inst = $_type::load($row->$theirColumnName);
			array_push($related, $inst);
		}
		return $related;
	}
}
