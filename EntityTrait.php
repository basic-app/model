<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
 namespace BasicApp\Model;

 trait EntityTrait
 {

    protected $parentKey;

    public function entityPrimaryKey($entity)
    {
        assert($this->primaryKey ? true : false, __CLASS__ . '::primaryKey');

        if ($this->returnType == 'array')
        {
            return $entity[$this->primaryKey];
        }

        return $entity->{$this->primaryKey};
    }

    public function entityParentKey($entity)
    {
        assert($this->parentKey ? true : false, __CLASS__ . '::parentKey');

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
    
 }