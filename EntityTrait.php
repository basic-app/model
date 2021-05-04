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

    public function fillArray(array $entity, array $data, &$hasChanged = null) : array
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

        return $entity;
    }

    public function fillEntity($entity, array $data, &$hasChanged = null)
    {
        if (is_array($entity))
        {
            return $this->fillArray($entity, $data, $hasChanged);
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

    public function createEntity(array $defaults = [])
    {
        $entityClass = $this->returnType;

        return new $entityClass($defaults);
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