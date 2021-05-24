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

    protected $validationOnly;

    protected $validationExcept;

    protected $requiredFields = [];

    public static function model(bool $getShared = true, ConnectionInterface &$conn = null)
    {
        return model(get_called_class(), $getShared, $conn);
    }

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

        $return = parent::getValidationRules($options);
    
        foreach($this->requiredFields as $field)
        {
            if (strpos($return[$field]['rules'], 'required') === false)
            {
                if (empty($return[$field]['rules']))
                {
                    $return[$field]['rules'] = 'required';
                }
                else
                {
                    $return[$field]['rules'] = 'required|' . $return[$field]['rules'];
                }
            }
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

        return $this->first();
    }

    public function findOrFail($id, string $error = null)
    {
        $return = $this->findOne($id, $error);

        Assert::notEmpty($return, $error ?? 'Data not found.');

        return $return;
    }

    public function errors(bool $forceDB = false) : array
    {
        return (array) parent::errors($forceDB);
    }

    public function findOrCreate(array $key, $fields = null)
    {
        $return = $this->where($key)->first();

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

        $entity = $this->createData($data);

        $this->saveOrFail($entity->toArray());

        return $this->findOrFail($key);
    }

    public function saveOrFail($data = null, $error = null)
    {
        $return = $this->save($data);

        Assert::true($return, $error ?? $this->firstError('Save failed.'));

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

    public function refresh(&$entity)
    {
        $id = $this->idValue($entity);

        Assert::notEmpty($id, 'ID not found.');

        $entity = $this->findOrFail($id);
    }

    public function firstError($default = null, ...$params)
    {
        $errors = $this->errors(...$params);

        if (count($errors) > 0)
        {
            return array_shift($errors);
        }

        return $default;
    }

    public function filterWhere(array $params = [])
    {
        foreach($params as $key => $value)
        {
            if (!$value)
            {
                unset($params[$key]);
            }
        }

        if ($params)
        {
            return $this->where($params);
        }

        return $this;
    }

    public function fillArray(array $data, array $params, &$hasChanged = null) : array
    {
        $hasChanged = false;

        foreach($params as $key => $value)
        {
            if (!array_key_exists($key, $data) || ($value != $data[$key]))
            {
                $hasChanged = true;
            }

            $data[$key] = $value;
        }

        return $data;
    }

    public function createData(array $defaults = [])
    {
        if ($this->returnType == 'array')
        {
            $return = [];

            return $this->fillArray($return, $defaults);
        }

        $entityClass = $this->returnType;

        return new $entityClass($defaults);
    }

    public function fillEntity($entity, array $data) : bool 
    {
        $entity->fill($data);

        return $entity->hasChanged();
    }

    public function fill($data, array $params, &$hasChanged = null)
    {
        if (is_array($entity))
        {
            return $this->fillArray($data, $params, $hasChanged);
        }

        $hasChanged = $this->fillEntity($data, $params);

        return $data;
    }

    public function childrens($data)
    {
        $id = $this->idValue($data);

        return $this->where($this->parentKey, $id)->findAll();
    }

    public function deleteData($entity)
    {
        if ($this->parentKey)
        {
            foreach($this->childrens($entity) as $children)
            {
                if (!$this->deleteData($children))
                {
                    return false;
                }
            }
        }

        $id = $this->idValue($entity);

        return $this->delete($id);
    }

    public function save($data, &$errors = null) : bool
    {
        if (is_object($data) && ! $data instanceof stdClass)
        {
            $data = $this->objectToArray($data, false, true); // SET ONLY CHANGED FLAG TO FALSE
        }
        else
        {
            if ($this->shouldUpdate($data))
            {
                $data = $this->transformDataToArray($data, 'update');
            }
            else
            {
                $data = $this->transformDataToArray($data, 'insert');
            }
        }

        $idValue = $this->idValue($data);

        $allowedFields = $this->allowedFields;

        $validationExcept = $this->validationExcept;

        $validationOnly = $this->validationOnly;

        $requiredFields = $this->requiredFields;

        $result = $this->trigger('beforeSave', [
            'data' => $data,
            'errors' => $errors,
            'result' => true,
            'idValue' => $idValue
        ]);

        $return = $result['result'];

        $data = $result['data'];

        $errors = $result['errors'];

        if ($return)
        {
            $return = parent::save($data);

            if ($return)
            {
                $result = $this->trigger('afterSave', [
                    'data' => $data,
                    'errors' => $errors,
                    'result' => true,
                    'idValue' => $idValue
                ]);

                $return = $result['result'];

                $data = $result['data'];

                $errors = $result['errors'];
            }
        }

        $this->allowedFields = $allowedFields;

        $this->validationExcept = $validationExcept;

        $this->validationOnly = $validationOnly;

        $this->requiredFields = $requiredFields;

        return $return;
    }

    public function unsetAllowedField(string $name)
    {
        $index = array_search($name, $this->allowedFields);
    
        if ($index !== false)
        {
            unset($this->allowedFields[$index]);
        }
    }

    public function unsetRequiredField(string $name)
    {
        $index = array_search($name, $this->requiredFields);
    
        if ($index !== false)
        {
            unset($this->requiredFields[$index]);
        }
    }

    public function prepareBuilder()
    {
        return $this;
    }

}