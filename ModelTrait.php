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

    protected $fillUnsafeFields = false;

    protected $fillableFields;

    protected $validationOnly;

    protected $validationExcept;

    public function getValidationRules(array $options = []): array
    {
        if (!array_key_exists('except', $options) && $this->validationExcept)
        {
            $options['except'] = $this->validationExcept;
        }

        if (!array_key_exists('only', $options) && $this->validationOnly)
        {
            $options['only'] = $this->validationOnly;
        }

        return parent::getValidationRules($options);
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

    public function findOne($id, $error = null)
    {
        Assert::notEmpty($id, $error ?? 'ID not defined.');

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
        $return = $this->findOne($id, $error);

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

    public function insertOrFail($data = null, bool $returnID = true, ?string $error = null)
    {
        $return = $this->insert($data, $returnID);

        Assert::true($return ? true : false, $error ?? 'Insert failed.');

        return $return;
    }

    public function updateOrFail($id = null, $data = null, ?string $error = null)
    {
        $return = $this->update($id, $data);

        Assert::true($return ? true : false, $error ?? 'Update failed.');

        return $return;
    }

    public function deleteOrFail($id, $error = null)
    {
        $return = $this->delete($id);

        $error = $error ?? 'Delete error.';

        Assert::true($return->resultID, $this->db->error() ?? $error);

        return $return;
    }

}