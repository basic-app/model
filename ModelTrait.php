<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Model;

use Closure;
use CodeIgniter\Database\ConnectionInterface;
use Webmozart\Assert\Assert;

trait ModelTrait
{

    protected $parentKey;

    protected $unsafeFields = [];

    public static function model(bool $getShared = true, ConnectionInterface &$conn = null)
    {
        return model(static::class, $getShared, $conn);
    }

    public function count()
    {
        return $this->countAllResults();
    }

    public function one()
    {
        $return = $this->first();

        if ($return)
        {
            $return = $this->prepareEntity($return);
        }

        return $return;
    }

    public function all()
    {
        $return = $this->findAll();
    
        foreach($return as $key => $data)
        {
            $return[$key] = $this->prepareEntity($data);
        }

        return $return;
    }

    public function findOne($id)
    {
        Assert::notEmpty($id, 'Primary key is not defined.');

        if (is_array($id))
        {
            return $this->where($id)->one();
        }

        Assert::false(!is_numeric($id) && !is_string($id), 'Bad primary key.');

        $return = $this->find($id);

        if ($return)
        {
            $return = $this->prepareEntity($return);
        }

        return $return;
    }

    public function findOrFail($id)
    {
        $return = $this->findOne($id);

        Assert::notEmpty($return, 'Row not found.');

        return $return;
    }

    public function allowed()
    {
        return $this->select($this->allowedFields);
    }

    public function prepareEntity($entity)
    {
        if (is_array($entity))
        {
            foreach($this->unsafeFields as $field)
            {
                unset($entity[$field]);
            }
        }
        else
        {
            foreach($this->unsafeFields as $field)
            {
                unset($entity->$field);
            }
        }

        return $entity;
    }

    public function fillEntity($entity, array $data, &$hasChanged = null)
    {
        foreach($data as $key => $value)
        {
            if (array_search($key, $this->unsafeFields) !== false)
            {
                unset($data[$key]);
            }
        }

        if (is_array($entity))
        {
            $hasChanged = false;

            foreach($data as $key => $value)
            {
                if (!array_key_exists($key, $entity) || ($value != $entity[$key]))
                {
                    $hasChanged = true;
                }

                $entity[$key] = $value;
            }            
        }
        else
        {
            $entity->fill($data);

            $hasChanged = $entity->hasChanged();
        }

        return $entity;
    }
    
    public function entityPrimaryKey($entity)
    {
        return $this->idValue($entity);
    }

    public function createEntity(array $data = [])
    {
        if ($this->returnType == 'array')
        {
            $return = [];
        }
        else
        {
            $entityClass = $this->returnType;

            $return = new $entityClass;
        }

        $return = $this->fillEntity($return, $data);

        return $return;
    }

    public function deleteEntity($entity)
    {
        if ($this->parentKey)
        {
            foreach($this->entityChildrens($entity) as $children)
            {
                if (!$this->deleteEntity($children))
                {
                    return false;
                }
            }
        }

        $id = $this->entityPrimaryKey($entity);

        return $this->delete($id);
    }
 
    public function entityParentKey($entity)
    {
        Assert::notEmpty($this->parentKey, 'Parent key is not defined.');

        if ($this->returnType == 'array')
        {
            return $entity[$this->parentKey];
        }

        return $entity->{$this->parentKey};
    }

    public function entityChildrens($entity)
    {
        $id = $this->entityPrimaryKey($entity);

        return $this->where($this->parentKey, $id)->findAll();
    }

    public function setEntityParentKey(&$entity, $parentId)
    {
        Assert::notEmpty($this->parentKey, 'Parent key is not defined.');

        if ($this->returnType == 'array')
        {
            $entity[$this->parentKey] = $parentId;
        }
        else
        {
            $entity->{$this->parentKey} = $parentId;
        }
    }

    public function errors(bool $forceDB = false) : array
    {
        $return = parent::errors($forceDB);
    
        if (!$return)
        {
            return [];
        }

        return $return;
    }

    public function findOrCreate(array $key, $fields = null)
    {
        $return = $this->where($key)->one();

        if ($return)
        {
            return $return;
        }

        if ($fields)
        {
            if ($fields instanceof Closure)
            {
                $fields = $fields->bindTo($this, $this);

                Assert::notEmpty($fields, 'Bind failed.');

                $fields = $fields();
            }

            $data = array_merge($fields, $key);
        }
        else
        {
            $data = $key;
        }

        $entity = $this->createEntity($data);

        $this->saveOrFail($entity->toArray());

        return $this->findOrFail($key);
    }

    public function saveOrFail($data = null)
    {
        $return = $this->save($data);

        Assert::true($return, 'Save failed.');

        return $return;
    }

}