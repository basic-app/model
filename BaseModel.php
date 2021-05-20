<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Model;

use CodeIgniter\Validation\ValidationInterface;

abstract class BaseModel extends \CodeIgniter\Model
{

    use ModelTrait;

    use EntityTrait;

    use EventsTrait;

    /**
     * BaseModel constructor.
     *
     * @param ValidationInterface|null $validation Validation
     */
    public function __construct(ValidationInterface $validation = null)
    {
        parent::__construct($validation);

        $this->setDefaultEvents();
    }

    protected function transformDataToArray($data, string $type): array
    {
        if ($type == 'insert')
        {
            if (is_object($data) && ! $data instanceof stdClass)
            {
                $data = $this->objectToArray($data, false, true);
            }
        }

        return parent::transformDataToArray($data, $type);
    }

}