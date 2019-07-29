<?php


namespace App\Kernel\Abstracts;

use App\Kernel\BaseServices\Database;


abstract class ModelAbstract
{

    private $db;

    public $data;
    public $isNew = true;
    public $returnType = 'Object';
    private $_with = Array();
    public static $pageLimit = 20;
    public static $totalPages = 0;
    protected $primaryKey = 'id';
    protected $relations = [];
    protected $timestamps = ['created_at', 'updated_at'];
    protected $dbTable;
    protected $dbFields;

    public function __construct($data = null)
    {
        $this->db = Database::getInstance();
        if (empty ($this->dbTable))
            $this->dbTable = get_class($this);

        if ($data)
            $this->data = $data;
    }


    public function __set($name, $value)
    {
        if (property_exists($this, 'hidden') && array_search($name, $this->hidden) !== false)
            return false;
        $this->data[$name] = $value;
        return true;
    }


    public function __get($name)
    {
        if (property_exists($this, 'hidden') && array_search($name, $this->hidden) !== false)
            return null;

        if (isset ($this->data[$name]) && $this->data[$name] instanceof ModelAbstract)
            return $this->data[$name];

        if (property_exists($this, 'relations') && isset ($this->relations[$name])) {
            $relationType = strtolower($this->relations[$name][0]);
            $modelName = $this->relations[$name][1];
            switch ($relationType) {
                case 'hasone':
                    $key = isset ($this->relations[$name][2]) ? $this->relations[$name][2] : $name;
                    $obj = new $modelName;
                    $obj->returnType = $this->returnType;
                    return $this->data[$name] = $obj->byId($this->data[$key]);
                    break;
                case 'hasmany':
                    $key = $this->relations[$name][2];
                    $obj = new $modelName;
                    $obj->returnType = $this->returnType;
                    return $this->data[$name] = $obj->where($key, $this->data[$this->primaryKey])->get();
                    break;
                // ["belongsToMany", UserInterest::class, 'user_id', Interest::class, 'interest_id']
                case 'belongstomany':
                    $key = $this->relations[$name][2];
                    $resKey = $this->relations[$name][4];
                    $pivot = new $modelName;
                    $ids = [];
                    $r_pivot = $pivot->ArrayBuilder()->where($key, $this->data[$this->primaryKey])->get();
                    if (is_array($r_pivot)) {
                        foreach ($r_pivot as $k => $v)
                            $ids[] = $v->{$resKey};
                        $modelName = $this->relations[$name][3];
                        $obj = new $modelName;
                        $obj->returnType = $this->returnType;
                        return $this->data[$name] = $obj->where($obj->primaryKey, $ids, 'IN')->get();
                    }
                    break;
                default:
                    break;
            }
        }

        if (isset ($this->data[$name]))
            return $this->data[$name];

        if (property_exists($this->db, $name))
            return $this->db->$name;
        return null;
    }

    public function __isset($name)
    {
        if (isset ($this->data[$name]))
            return isset ($this->data[$name]);
        if (property_exists($this->db, $name))
            return isset ($this->db->$name);
        return false;
    }

    public function __unset($name)
    {
        unset ($this->data[$name]);
    }

    public static function table($tableName)
    {
        $tableName = preg_replace("/[^-a-z0-9_]+/i", '', $tableName);
        if (!class_exists($tableName))
            eval ("class $tableName extends ModelAbstract {}");
        return new $tableName ();
    }


    public function insert()
    {
        if (!empty ($this->timestamps) && in_array("created_at", $this->timestamps))
            $this->created_at = date("Y-m-d H:i:s");
        if (!empty ($this->timestamps) && in_array("updated_at", $this->timestamps))
            $this->updated_at = date("Y-m-d H:i:s");
        $sqlData = $this->prepareData();

        $id = $this->db->insert($this->dbTable, $sqlData);
        if (!empty ($this->primaryKey) && empty ($this->data[$this->primaryKey]))
            $this->data[$this->primaryKey] = $id;
        $this->isNew = false;
        return $id;
    }


    public function update($data = null)
    {
        if (empty ($this->dbFields))
            return false;

        if (empty ($this->data[$this->primaryKey]))
            return false;
        if ($data)
            foreach ($data as $k => $v)
                $this->$k = $v;

        if (!empty ($this->timestamps) && in_array("updated_at", $this->timestamps))
            $this->updated_at = date("Y-m-d H:i:s");

        $sqlData = $this->prepareData();
        $this->db->where($this->primaryKey, $this->data[$this->primaryKey]);
        $res = $this->db->update($this->dbTable, $sqlData);
        return $res;
    }


    public function save($data = null)
    {
        if ($this->isNew)
            return $this->insert();
        return $this->update($data);
    }


    public function delete()
    {
        if (empty ($this->data[$this->primaryKey]))
            return false;

        $this->db->where($this->primaryKey, $this->data[$this->primaryKey]);
        try {
            $res = $this->db->delete($this->dbTable);
        } catch (\Exception $e) {
        }
        return $res;
    }


    protected function byId($id, $fields = null)
    {
        $this->db->where($this->dbTable . '.' . $this->primaryKey, $id);
        return $this->getOne($fields);
    }


    protected function getOne($fields = null)
    {
        $this->processHasOneWith();
        try {
            $results = $this->db->ArrayBuilder()->getOne($this->dbTable, $fields);
        } catch (\Exception $e) {
        }
        if ($this->db->count == 0)
            return null;
        $this->processArrays($results);
        $this->data = $results;
        $this->processAllWith($results);
        if ($this->returnType == 'Json')
            return json_encode($results);
        if ($this->returnType == 'Array')
            return $results;

        $item = new static ($results);
        $item->isNew = false;

        return $item;
    }

    protected function get($limit = null, $fields = null)
    {
        $objects = Array();
        $this->processHasOneWith();
        try {
            $results = $this->db->ArrayBuilder()->get($this->dbTable, $limit, $fields);
        } catch (\Exception $e) {
        }
        if ($this->db->count == 0)
            return null;

        foreach ($results as $k => &$r) {
            $this->processArrays($r);
            $this->data = $r;
            $this->processAllWith($r, false);
            if ($this->returnType == 'Object') {
                $item = new static ($r);
                $item->isNew = false;
                $objects[$k] = $item;
            }
        }
        $this->_with = Array();
        if ($this->returnType == 'Object')
            return $objects;

        if ($this->returnType == 'Json')
            return json_encode($results);

        return $results;
    }

    protected function getValue($k)
    {
        return $this->db->getValue($this->dbTable, $k);
    }

    protected function with($objectName)
    {
        if (!property_exists($this, 'relations') || !isset ($this->relations[$objectName]))
            die ("No relation with name $objectName found");
        $this->_with[$objectName] = $this->relations[$objectName];

        return $this;
    }

    protected function join($objectName, $key = null, $joinType = 'LEFT', $primaryKey = null)
    {
        $joinObj = new $objectName;
        if (!$key)
            $key = $objectName . "id";

        if (!$primaryKey)
            $primaryKey = $joinObj->dbTable . "." . $joinObj->primaryKey;

        if (!strchr($key, '.'))
            $joinStr = $this->dbTable . ".{$key} = " . $primaryKey;
        else
            $joinStr = "{$key} = " . $primaryKey;

        if (isset($this->db)) {
            try {
                $this->db->join($joinObj->dbTable, $joinStr, $joinType);
            } catch (\Exception $e) {
            }
        }
        return $this;
    }

    protected function count()
    {
        try {
            $res = $this->db->ArrayBuilder()->getValue($this->dbTable, "count(*)");
        } catch (\Exception $e) {
        }
        if (!$res)
            return 0;
        return $res;
    }

    protected function paginate($page, $fields = null)
    {
        $this->db->pageLimit = self::$pageLimit;
        $res = $this->db->paginate($this->dbTable, $page, $fields);
        self::$totalPages = $this->db->totalPages;
        if ($this->db->count == 0) return null;

        foreach ($res as $k => &$r) {
            $this->processArrays($r);
            $this->data = $r;
            $this->processAllWith($r, false);
            if ($this->returnType == 'Object') {
                $item = new static ($r);
                $item->isNew = false;
                $objects[$k] = $item;
            }
        }
        $this->_with = Array();
        if ($this->returnType == 'Object')
            return $objects;

        if ($this->returnType == 'Json')
            return json_encode($res);

        return $res;
    }

    public function __call($method, $arg)
    {
        if (isset($this->data) && !is_null($this->data) && array_key_exists($method, $this->data))
            return (isset($this->data[$method]) && !is_null($this->data[$method]) ? $this->data[$method] : null);

        if (method_exists($this, $method))
            return call_user_func_array(array($this, $method), $arg);
        // HARDCODED COMME JAMAIS HUHU...
        if (!isset($this->relations[$method]))
            call_user_func_array(array($this->db, $method), $arg);
        else
            return $this->$method;
        return $this;
    }

    public static function __callStatic($method, $arg)
    {
        $obj = new static;
        $result = call_user_func_array(array($obj, $method), $arg);
        if (method_exists($obj, $method))
            return $result;
        return $obj;
    }

    public function toArray()
    {
        $data = $this->data;
        $this->processAllWith($data);
        foreach ($data as &$d) {
            if ($d instanceof ModelAbstract)
                $d = $d->data;
        }
        return $data;
    }

    public function toJson()
    {
        return json_encode($this->toArray());
    }

    public function __toString()
    {
        return $this->toJson();
    }


    private function processAllWith(&$data, $shouldReset = true)
    {
        if (count($this->_with) == 0)
            return;

        foreach ($this->_with as $name => $opts) {
            $relationType = strtolower($opts[0]);
            $modelName = $opts[1];
            if ($relationType == 'hasone') {
                $obj = new $modelName;
                $table = $obj->dbTable;
                $primaryKey = $obj->primaryKey;

                if (!isset ($data[$table])) {
                    $data[$name] = $this->$name;
                    continue;
                }
                if ($data[$table][$primaryKey] === null) {
                    $data[$name] = null;
                } else {
                    if ($this->returnType == 'Object') {
                        $item = new $modelName ($data[$table]);
                        $item->returnType = $this->returnType;
                        $item->isNew = false;
                        $data[$name] = $item;
                    } else {
                        $data[$name] = $data[$table];
                    }
                }
                unset ($data[$table]);
            } else
                $data[$name] = $this->$name;
        }
        if ($shouldReset)
            $this->_with = Array();
    }

    private function processHasOneWith()
    {
        if (count($this->_with) == 0)
            return;
        foreach ($this->_with as $name => $opts) {
            $relationType = strtolower($opts[0]);
            $modelName = $opts[1];
            $key = null;
            if (isset ($opts[2]))
                $key = $opts[2];
            if ($relationType == 'hasone') {
                $this->db->setQueryOption("MYSQLI_NESTJOIN");
                $this->join($modelName, $key);
            }
        }
    }


    private function processArrays(&$data)
    {
        if (isset ($this->jsonFields) && is_array($this->jsonFields)) {
            foreach ($this->jsonFields as $key)
                $data[$key] = json_decode($data[$key]);
        }

        if (isset ($this->arrayFields) && is_array($this->arrayFields)) {
            foreach ($this->arrayFields as $key)
                $data[$key] = explode("|", $data[$key]);
        }
    }

    private function prepareData()
    {
        $sqlData = Array();
        if (count($this->data) == 0)
            return Array();

        if (method_exists($this, "preLoad"))
            $this->preLoad($this->data);

        if (!$this->dbFields)
            return $this->data;

        foreach ($this->data as $key => &$value) {
            if ($value instanceof ModelAbstract && $value->isNew == true) {
                $id = $value->save();
                if ($id)
                    $value = $id;
            }

            if (!in_array($key, array_keys($this->dbFields)))
                continue;
            if (array_key_exists($key, $this->relations))
                continue;
            if (!is_array($value)) {
                $sqlData[$key] = $value;
                continue;
            }

            if (isset ($this->jsonFields) && in_array($key, $this->jsonFields))
                $sqlData[$key] = json_encode($value);
            else if (isset ($this->arrayFields) && in_array($key, $this->arrayFields))
                $sqlData[$key] = implode("|", $value);
            else
                $sqlData[$key] = $value;
        }
        return $sqlData;
    }
}