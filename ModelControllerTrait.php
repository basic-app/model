<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Model;

trait ModelControllerTrait
{

    protected function modelErrors() : array
    {
        return $this->model->errors();
    }

    protected function modelSave($data) : bool
    {
        return $this->model->save($data);
    }

    protected function modelFind($id = null)
    {
        return $this->model->find($id);
    }

    protected function modelFindAll(int $limit = 0, int $offset = 0)
    {
        return $this->model->findAll($limit, $offset);
    }

    public function modelDelete($id = null, bool $purge = false)
    {
        return $this->model->delete($id, $purge);
    }

    public function modelInsertID()
    {
        return $this->model->insertID();
    }

    public function modelIdValue($data)
    {
        return $this->model->idValue($data);
    }

}