<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Model;

use Closure;
use Exception;
use CodeIgniter\Validation\ValidationInterface;

abstract class FormModel extends \CodeIgniter\BaseModel
{

    protected $strictProperties = true;
    
    protected $strictMethods = true;
    
    protected $idValue;

    use ModelTrait;
    
    use SortTrait;
    
    use DefaultEventsTrait;

    public function __construct(ValidationInterface $validation = null)
    {
        parent::__construct($validation);

        $this->setDefaultEvents();
    }

    protected function doFind(bool $singleton, $id = null)
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    protected function doFindColumn(string $columnName)
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    protected function doFindAll(int $limit = 0, int $offset = 0)
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    protected function doFirst()
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    protected function doInsert(array $data)
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    protected function doInsertBatch(?array $set = null, ?bool $escape = null, int $batchSize = 100, bool $testing = false)
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    protected function doUpdate($id = null, $data = null): bool
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    protected function doUpdateBatch(array $set = null, string $index = null, int $batchSize = 100, bool $returnSQL = false)
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    protected function doDelete($id = null, bool $purge = false)
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    protected function doPurgeDeleted()
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    protected function doOnlyDeleted()
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    protected function doReplace(array $data = null, bool $returnSQL = false)
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    protected function doErrors()
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    public function countAllResults(bool $reset = true, bool $test = false)
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    public function chunk(int $size, Closure $userFunc)
    {
        throw new Exception('The ' . __METHOD__ . ' method is not implemented.');
    }

    public function __get(string $name)
    {
        if (property_exists($this, $name))
        {
            return $this->$name;
        }

        if (!$this->strictProperties)
        {
            return null;
        }

        throw new Exception('The ' . $name . ' property is not found.');
    }

    public function __isset(string $name): bool
    {
        if (property_exists($this, $name))
        {
            return true;
        }

        return false;
    }

    public function __call(string $name, array $params)
    {
        if (!$this->strictMethods)
        {
            return null;
        }

        throw new Exception('The ' . $name . ' method is not found.');
    }

    public function errors(bool $forceDB = false)
    {
        if (!$this->skipValidation && ($errors = $this->validation->getErrors()))
        {
            return $errors;
        }

        return [];
    }
    
    protected function idValue($data)
    {
        return $this->idValue;
    }

}