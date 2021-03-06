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

    use SortTrait;

    use ModelTrait;

    use EntityTrait;

    use DefaultEventsTrait;

    use BuilderTrait;

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
    
}