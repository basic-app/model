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
        return $this->first();
    }

    public function all()
    {
        return $this->findAll();
    }

    public function findOne($id)
    {
        assert($id ? true : false);

        return $this->find($id);
    }

    public function findOrFail($id)
    {
        assert($id ? true : false);

        $return = $this->find($id);

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

}