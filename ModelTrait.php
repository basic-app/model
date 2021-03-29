<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Model;

use CodeIgniter\Exceptions\PageNotFoundException;

trait ModelTrait
{

    public function idValue($data)
    {
        return parent::idValue($data);
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
            $return = $this->prepareEntity($return);
        }

        return $return;
    }

    public function all()
    {
        $return = $this->findAll();
    
        foreach($return as $key => $data)
        {
            $return[$key] = $this->prepareEntity($data);
        }

        return $return;
    }

    public function findOne($id)
    {
        assert($id ? true : false);

        $return = $this->find($id);

        if ($return)
        {
            $return = $this->prepareEntity($return);
        }

        return $return;
    }

    public function findOrFail($id)
    {
        $return = $this->findOne($id);

        if (!$return)
        {
            throw PageNotFoundException::forPageNotFound();
        }

        return $return;
    }

    public function allowed()
    {
        return $this->select($this->allowedFields);
    }

    public function prepareEntity($data)
    {
        return $data;
    }

}