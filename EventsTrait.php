<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Model;

trait EventsTrait
{

    protected $beforeSave = [];

    protected $afterSave = [];    

    protected function setDefaultEvents()
    {
        $this->beforeInsert[] = 'beforeInsert';
        $this->afterInsert[] = 'afterInsert';
        $this->beforeUpdate[] = 'beforeUpdate';
        $this->afterUpdate[] = 'afterUpdate';
        $this->beforeFind[] = 'beforeFind';
        $this->afterFind[] = 'afterFind';
        $this->beforeDelete[] = 'beforeDelete';
        $this->afterDelete[] = 'afterDelete';
        $this->beforeSave[] = 'beforeSave';
        $this->afterSave[] = 'afterSave';
    }

    protected function beforeInsert(array $data) : array
    {
        return $data;
    }

    protected function afterInsert(array $data) : array
    {
        return $data;
    }

    protected function beforeUpdate(array $data) : array
    {
        return $data;
    }

    protected function afterUpdate(array $data) : array
    {
        return $data;
    }

    protected function beforeFind(array $data) : array
    {
        return $data;
    }

    protected function afterFind(array $data) : array
    {
        return $data;
    }

    protected function beforeDelete(array $data) : array
    {
        return $data;
    }

    protected function afterDelete(array $data) : array
    {
        return $data;
    }

    protected function beforeSave(array $data) : array
    {
        return $data;
    }

    protected function afterSave(array $data) : array
    {
        return $data;
    }

}