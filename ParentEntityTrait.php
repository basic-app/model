<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
 namespace BasicApp\Model;

 trait ParentEntityTrait
 {

    protected $parentKey;

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

    public function setEntityParentKey(&$entity, $parentId)
    {
        assert($this->parentKey ? true : false, __CLASS__ . '::parentKey');

        if ($this->returnType == 'array')
        {
            $entity[$this->parentKey] = $parentId;
        }
        else
        {
            $entity->{$this->parentKey} = $parentId;
        }
    }

}