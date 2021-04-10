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

    public function count()
    {
        return $this->countAllResults();
    }

    public function one()
    {
        $return = $this->first();

        if ($return)
        {
            $return = $this->prepareData($return);
        }

        return $return;
    }

    public function all()
    {
        $return = $this->findAll();
    
        foreach($return as $key => $data)
        {
            $return[$key] = $this->prepareData($data);
        }

        return $return;
    }

    public function findOne($id)
    {
        Assert::notEmpty($id, 'ID not defined.');

        if (is_array($id))
        {
            $this->where($id);
        }
        else
        {
            $this->where($this->table . '.' . $this->primaryKey, $id);
        }

        return $this->one();
    }

    public function findOrFail($id, string $error = null)
    {
        $return = $this->findOne($id);

        Assert::notEmpty($return, $error ?? 'Data not found.');

        return $return;
    }

    public function allowed()
    {
        return $this->select($this->allowedFields);
    }

    public function prepareData($entity)
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

    public function saveOrFail($data = null, $error = null)
    {
        $return = $this->save($data);

        Assert::true($return, $error ?? 'Save failed.');

        return $return;
    }

}