<?php
/**
* @package   com_zoo Component
* @file      item.php
* @version   2.4.9 May 2011
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) 2007 - 2011 YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*/

/*
	Class: TableApplication
		The table class for items.
*/
class ItemTable extends AppTable {

	public function __construct($app) {
		parent::__construct($app, ZOO_TABLE_ITEM);
	}

	protected function _initObject($object) {

		parent::_initObject($object);

		// workaround for php bug, which calls constructor before filling values
		if (is_string($object->params) || is_null($object->params)) {
			// decorate data as object
			$object->params = $this->app->parameter->create($object->params);
		}

		// add to cache
		$key_name = $this->key;

		if ($object->$key_name && !key_exists($object->$key_name, $this->_objects)) {
			$this->_objects[$object->$key_name] = $object;
		}

		// trigger init event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'item:init'));

		return $object;
	}

	/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {

		if (!is_string($object->type) || empty($object->type)) {
			throw new ItemTableException('Invalid type id');
		}
		
		if ($object->name == '') {
			throw new ItemTableException('Invalid name');
		}

		if ($object->alias == '' || $object->alias != $this->app->string->sluggify($object->alias)) {
			throw new ItemTableException('Invalid slug');
		}

		if ($this->app->item->checkAliasExists($object->alias, $object->id)) {
			throw new ItemTableException('Alias already exists, please choose a unique alias');
		}

		$new = !(bool) $object->id;

		// first save to get id
		if (empty($object->id)) {
			parent::save($object);
		}
		
		// init vars
		$db           = $this->database;
		$search_data  = array();
		$element_data = array();

		foreach ($object->getElements() as $id => $element) {

			// get element data
			$element_data[] = $element->toXML();
			
			// get search data
			if ($data = $element->getSearchData()) {
				$search_data[] = "(".$object->id.", ".$db->quote($id).", ".$db->quote($data).")";
			}
		}
		
		// set element data
		$object->elements = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<elements>\n".implode("\n", $element_data)."\n</elements>";

		// delete old search data
		$query = "DELETE FROM ".ZOO_TABLE_SEARCH
				." WHERE item_id = ".(int) $object->id;
		$db->query($query);	

		// insert new search data
		if (count($search_data)) {
			$query = "INSERT INTO ".ZOO_TABLE_SEARCH
					." VALUES ".implode(", ", $search_data);
			$db->query($query);	
		}
		
		// save tags
		$this->app->table->tag->save($object->id, $object->getTags());

		$result = parent::save($object);

		// trigger save event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'item:saved', compact('new')));

		return $result;
	}

	/*
		Function: delete
			Override. Delete object from database table.

		Returns:
			Boolean.
	*/
	public function delete($object) {

		// get database
		$db = $this->database;

		// delete item to category relations
		$query = "DELETE FROM ".ZOO_TABLE_CATEGORY_ITEM
			    ." WHERE item_id = ".(int) $object->id;
		$db->query($query);

		// delete related comments
		$query = "DELETE FROM ".ZOO_TABLE_COMMENT
			    ." WHERE item_id = ".(int) $object->id;
		$db->query($query);		

		// delete related search data rows
		$query = "DELETE FROM ".ZOO_TABLE_SEARCH
				." WHERE item_id = ". (int) $object->id;
		$db->query($query);
		
		// delete related rating data rows
		$query = "DELETE FROM ".ZOO_TABLE_RATING
				." WHERE item_id = ". (int) $object->id;
		$db->query($query);		

		$result = parent::delete($object);

		// trigger deleted event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'item:deleted'));

		return $result;
	}

	/*
		Function: hit
			Increment item hits.

		Returns:
			Boolean.
	*/
	public function hit($object) {

		// get database
		$db  = $this->database;
		$key = $this->key;		

		// increment hits
		if ($object->$key) {
			$query = "UPDATE ".$this->name
				." SET hits = (hits + 1)"
				." WHERE $key = ".(int) $object->$key;
			$db->query($query);	
			$object->hits++;
			return true;
		}

		return false;
	}

	/*
		Function: getByType
			Method to get types related items.

		Parameters:
			$type_id - Type

		Returns:
			Array - Items
	*/
	public function getByType($type_id, $application_id = false){
		
		// get database
		$db = $this->database;		
		
 		$query = "SELECT a.*"
		        ." FROM ".$this->name." AS a"
		        ." WHERE a.type = ".$db->Quote($type_id)
		        .($application_id !== false ? " AND a.application_id = ".(int) $application_id : "");

		return $this->_queryObjectList($query);		
	}

	/*
		Function: getApplicationItemCount
			Method to get application related item count.

		Parameters:
			$application_id - Application id

		Returns:
			Int
	*/
	public function getApplicationItemCount($application_id) {
 		$query = "SELECT count(a.id) AS item_count"
		        ." FROM ".$this->name." AS a"
		        ." WHERE a.application_id = ".(int) $application_id;
		
		return (int) $this->_queryResult($query);
	}
	
	/*
		Function: getTypeItemCount
			Method to get types related item count.

		Parameters:
			$type - Type

		Returns:
			Int
	*/
	public function getTypeItemCount($type){

		// get database
		$db = $this->database;			
		
		$group = $type->getApplication()->getGroup();
 		$query = "SELECT count(a.id) AS item_count"
		        ." FROM ".$this->name." AS a"
		        ." JOIN ".ZOO_TABLE_APPLICATION." AS b ON a.application_id = b.id"
		        ." WHERE a.type = ".$db->Quote($type->id)
       			." AND b.application_group = ".$db->Quote($group);
		
		return (int) $this->_queryResult($query);

	}
	
	/*
		Function: findAll
			Method to retrieve all items.

		Parameters:
			$application_id - Application id
			$published - Get published items only
			$user - check access level of this user, else current user is used
			$options - additional options

		Returns:
			Array - Array of items
	*/
	public function findAll($application_id = false, $published = false, $user = null, $options = array()) {

		// get database
		$db = $this->database;

		// get date
		$date = $this->app->date->create();
		$now  = $db->Quote($date->toMySQL());
		$null = $db->Quote($db->getNullDate());
		
		// set query options
		$conditions = 
		     ($application_id !== false ? "application_id = ".(int) $application_id : "")
			." AND ".$this->app->user->getDBAccessString()
			.($published == true ? " AND state = 1"		
			." AND (publish_up = ".$null." OR publish_up <= ".$now.")"
			." AND (publish_down = ".$null." OR publish_down >= ".$now.")": "");
		
		return $this->find('all', array_merge(compact('conditions'), $options));
	}

	/*
		Function: getByCharacter
			Method to retrieve all items starting with a certain character.

		Parameters:
			$application_id - Application id
			$char - Character(s)
			$not_in - Use not in for matching multiple characters

		Returns:
			Array - Array of items
	*/
	public function getByCharacter($application_id, $char, $not_in = false, $published = false, $user = null, $orderby = "", $offset = 0, $limit = 0){

		// get database
		$db = $this->database;

		$date = $this->app->date->create();
		$now  = $db->Quote($date->toMySQL());
		$null = $db->Quote($db->getNullDate());

		// escape and quote character array
		if (is_array($char)) {
			foreach ($char as $key => $val) {
				$char[$key] = "'".$db->getEscaped($val)."'";
			}
		}
		
		$query = "SELECT a.*"
			." FROM ".ZOO_TABLE_CATEGORY_ITEM." AS ci"
			." JOIN ".$this->name." AS a ON a.id = ci.item_id"
			." WHERE a.application_id = ".(int) $application_id
			." AND ".$this->app->user->getDBAccessString()
			.($published == true ? " AND a.state = 1"		
			." AND (a.publish_up = ".$null." OR a.publish_up <= ".$now.")"
			." AND (a.publish_down = ".$null." OR a.publish_down >= ".$now.")": "")
			." AND BINARY LOWER(LEFT(a.name, 1)) ".(is_array($char) ? ($not_in ? "NOT" : null)." IN (".implode(",", $char).")" : " = '".$db->getEscaped($char)."'")
			." ORDER BY a.priority DESC ".($orderby != "" ? ", ".$orderby : "")
			.(($limit ? " LIMIT ".(int)$offset.",".(int)$limit : ""));

		return $this->_queryObjectList($query);
	}

	/*
		Function: getByTag
			Method to retrieve all items matching a certain tag.

		Parameters:
			$tag - Tag name
			$application_id - Application id

		Returns:
			Array - Array of items
	*/
	public function getByTag($application_id, $tag, $published = false, $user = null, $orderby = "", $offset = 0, $limit = 0){
		
		// get database
		$db = $this->database;

		// get dates
		$date = $this->app->date->create();
		$now  = $db->Quote($date->toMySQL());
		$null = $db->Quote($db->getNullDate());

		$query = "SELECT a.*"
				." FROM ".$this->name." AS a "
				." LEFT JOIN ".ZOO_TABLE_TAG." AS b ON a.id = b.item_id"
				." WHERE a.application_id = ".(int) $application_id
				." AND b.name = '".$db->getEscaped($tag)."'"
				." AND ".$this->app->user->getDBAccessString()
				.($published == true ? " AND a.state = 1"		
				." AND (a.publish_up = ".$null." OR a.publish_up <= ".$now.")"
				." AND (a.publish_down = ".$null." OR a.publish_down >= ".$now.")": "")
				." GROUP BY a.id"
				." ORDER BY a.priority DESC ".($orderby != "" ? ", ".$orderby : "")
				.(($limit ? " LIMIT ".(int)$offset.",".(int)$limit : ""));

		return $this->_queryObjectList($query);
	}
	
	/*
		Function: getFromCategory
			Method to retrieve all items of a category.

		Parameters:
			$category_id - Category id(s)

		Returns:
			Array - Array of items
	*/
	public function getFromCategory($application_id, $category_id, $published = false, $user = null, $orderby = "", $offset = 0, $limit = 0){

		// get database
		$db = $this->database;

		// get dates
		$date = $this->app->date->create();
		$now  = $db->Quote($date->toMySQL());
		$null = $db->Quote($db->getNullDate());

		$query = "SELECT a.*"
			." FROM ".$this->name." AS a"
			." LEFT JOIN ".ZOO_TABLE_CATEGORY_ITEM." AS b ON a.id = b.item_id"
			." WHERE a.application_id = ".(int) $application_id
			." AND b.category_id ".(is_array($category_id) ? " IN (".implode(",", $category_id).")" : " = ".(int) $category_id)
			." AND ".$this->app->user->getDBAccessString()
			.($published == true ? " AND a.state = 1"		
			." AND (a.publish_up = ".$null." OR a.publish_up <= ".$now.")"
			." AND (a.publish_down = ".$null." OR a.publish_down >= ".$now.")": "")
			." GROUP BY a.id"
			." ORDER BY a.priority DESC ".($orderby != "" ? ", ".$orderby : "")
			.(($limit ? " LIMIT ".(int)$offset.",".(int)$limit : ""));

		return $this->_queryObjectList($query);
	}

	/*
		@deprecated 2.4	Use the categories own itemCount() method instead
	*/
	public function getItemCountFromCategory($application_id, $category_id, $published = false, $user = null){

		// get database
		$db = $this->database;

		// get dates
		$date = $this->app->date->create();
		$now  = $db->Quote($date->toMySQL());
		$null = $db->Quote($db->getNullDate());

		$query = "SELECT a.*"
			." FROM ".$this->name." AS a"
			." LEFT JOIN ".ZOO_TABLE_CATEGORY_ITEM." AS b ON a.id = b.item_id"
			." WHERE a.application_id = ".(int) $application_id
			." AND b.category_id ".(is_array($category_id) ? " IN (".implode(",", $category_id).")" : " = ".(int) $category_id)
			." AND ".$this->app->user->getDBAccessString()
			.($published == true ? " AND a.state = 1"
			." AND (a.publish_up = ".$null." OR a.publish_up <= ".$now.")"
			." AND (a.publish_down = ".$null." OR a.publish_down >= ".$now.")": "")
			." GROUP BY a.id";

		$db->query($query);

		return $db->getNumRows();

	}

	/*
		Function: search
			Method to retrieve all items matching search data.

		Parameters:
			$search_string - the search string
			$app_id - specify an application id to limit the search scope

		Returns:
			Array - Array of items
	*/
	public function search($search_string, $app_id = 0) {
		
		// get database
		$db = $this->database;
		$db_search = $db->Quote('%'.$db->getEscaped( $search_string, true ).'%', false);

		$query = "SELECT a.*"
				." FROM ".$this->name." AS a"
				." LEFT JOIN ".ZOO_TABLE_SEARCH." AS b ON a.id = b.item_id"
				." WHERE (LOWER(b.value) LIKE LOWER(" . $db_search . ")"
				." OR LOWER(a.name) LIKE LOWER(" . $db_search . "))"
				. (empty($app_id) ? "" : " AND application_id = " . $app_id)
				." AND a.searchable=1"
				." GROUP BY a.id";

		return $this->_queryObjectList($query);
	}

	/*
		Function: searchElements
			Method to retrieve all items matching search data.

		Parameters:
			$elements_array - key = element_name, value = search string
			$app_id - specify an application id to limit the search scope

		Returns:
			Array - Array of items
	*/
	public function searchElements($elements_array, $app_id = 0) {
		
		// get database
		$db = $this->database;
				
		$i = 0;
		$join = array();
		$where = array();
		foreach ($elements_array as $name => $search_string) {
			$as = "table" . $i;
			$db_name = $db->Quote($db->getEscaped( $name, true ), false);
			$db_search = $db->Quote('%'.$db->getEscaped( $search_string, true ).'%', false);
			$join[] = " LEFT JOIN ".ZOO_TABLE_SEARCH." AS " . $as . " ON a.id = " . $as . ".item_id";
			$where[] = $as.".element_id = ".$db_name." AND LOWER(".$as.".value) LIKE LOWER(".$db_search.")";			
			$i++;
		}

		$query = "SELECT a.*"
				." FROM ".$this->name." AS a "
				. implode(" ", $join)
				." WHERE "
				. implode(" AND ", $where)
				. (empty($app_id) ? "" : " AND application_id = " . $app_id)
				." AND a.searchable=1"
				." GROUP BY a.id";

		return $this->_queryObjectList($query);
	}

	/*
		Function: getUsers
			Method to get users of items

		Parameters:
			$app_id - Application id

		Returns:
			Array - Array of items
	*/	
	public function getUsers($app_id) {
		$query = 'SELECT DISTINCT u.id, u.name'
			    .' FROM '.$this->name.' AS i'
			    .' JOIN #__users AS u ON i.created_by = u.id'
			    . ((empty($app_id)) ? "" : " WHERE i.application_id = ".$app_id);

		return $this->database->queryObjectList($query, 'id');
	}

	/*
		Function: isInitialized
			Has the object already been retrieved from the database.

		Parameters:
			$key - object key

		Returns:
			bool - true if object is found
	*/
	public function isInitialized($key) {
		return isset($this->_objects[$key]);
	}

}

/*
	Class: ItemTableException
*/
class ItemTableException extends AppException {}