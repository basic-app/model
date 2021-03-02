<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
 namespace BasicApp\Model;

 trait EntityTrait
 {

    public function entityPrimaryKey($entity)
    {
        assert($this->primaryKey ? true : false, __CLASS__ . '::primaryKey');

        if ($this->returnType == 'array')
        {
            return $entity[$this->primaryKey];
        }

        return $entity->{$this->primaryKey};
    }

    public function createEntity(array $default = [])
    {
        if ($this->returnType == 'array')
        {
            $return = $default;

            foreach($this->allowedFields as $key)
            {
                if (!array_key_exists($key, $return))
                {
                    $return[$key] = null;
                }
            }

            return $return;
        }

        $entityClass = $this->returnType;

        return new $entityClass($default);
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

    public function fillEntity(&$entity, $request)
    {
        if ($this->returnType == 'array')
        {
            $hasChanged = false;

            foreach($request as $key => $value)
            {
                if (!array_key_exists($key, $entity) || ($value != $entity[$key]))
                {
                    $hasChanged = true;
                }

                $entity[$key] = $value;
            }

            return $hasChanged;
        }

        $entity->fill($request);

        return $entity->hasChanged();
    }
    
 }