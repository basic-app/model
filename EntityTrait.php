<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Model;

use Webmozart\Assert\Assert;

trait EntityTrait
{

    public function entityField($entity, $field, $default = null)
    {
        if ($this->returnType == 'array')
        {
            if (array_key_exists($field, $entity))
            {
                return $entity[$field];
            }

            return $default;
        }
        else
        {
            return $entity->{$field};
        }
    }

    public function entitySetField($entity, $field, $value)
    {
        if ($this->returnType == 'array')
        {
            $entity[$field] = $value;
        }
        else
        {
            $entity->{$field} = $value;
        }

        return $entity;
    }

    public function setEntityParentKey($entity, $parentId)
    {
        Assert::notEmpty($this->parentKey, 'Parent key not defined.');

        return $this->setEntityField($entity, $this->parentKey, $parentId);
    }

    public function fillEntity($entity, array $data, &$hasChanged = null)
    {
        foreach($data as $key => $value)
        {
            if (!$this->fillUnsafeFields && array_search($key, $this->unsafeFields) !== false)
            {
                unset($data[$key]);
            }

            if ($this->fillableFields)
            {
                if (array_search($key, $this->fillableFields) === false)
                {
                    unset($data[$key]);
                }
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
        Assert::notEmpty($this->parentKey, 'Parent key not defined.');

        return $this->entityField($entity, $this->parentKey);
    }

    public function entityChildrens($entity)
    {
        $id = $this->entityPrimaryKey($entity);

        return $this->where($this->parentKey, $id)->findAll();
    }    

}