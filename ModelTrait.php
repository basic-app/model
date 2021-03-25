<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Model;

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

}