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
    
    public function entityPrimaryKey($entity)
    {
        return $this->getIdValue($entity);
    }
 
    public function entityParentKey($entity)
    {
        Assert::notEmpty($this->parentKey, 'Parent key not defined.');

        return $this->entityField($entity, $this->parentKey);
    }  

}