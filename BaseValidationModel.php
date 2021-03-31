<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @author CodeIgniter Foundation <admin@codeigniter.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Model;

use CodeIgniter\Validation\ValidationInterface;
use Config\Services;

class BaseValidationModel
{

    use ModelTrait;

    /**
     * The table's primary key.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The Database connection group that
     * should be instantiated.
     *
     * @var string
     */
    protected $DBGroup;

    /**
     * The format that the results should be returned as.
     * Will be overridden if the as* methods are used.
     *
     * @var string
     */
    protected $returnType = 'array';

    /**
     * An array of field names that are allowed
     * to be set by the user in inserts/updates.
     *
     * @var array
     */
    protected $allowedFields = [];

    /**
     * Rules used to validate data in insert, update, and save methods.
     * The array must match the format of data passed to the Validation
     * library.
     *
     * @var array|string
     */
    protected $validationRules = [];

    /**
     * Contains any custom error messages to be
     * used during data validation.
     *
     * @var array
     */
    protected $validationMessages = [];

    /**
     * Whether rules should be removed that do not exist
     * in the passed in data. Used between inserts/updates.
     *
     * @var boolean
     */
    protected $cleanValidationRules = true;

    /**
     * Our validator instance.
     *
     * @var ValidationInterface
     */
    protected $validation;


    /**
     * BaseValidationModel constructor.
     *
     * @param ValidationInterface|null $validation Validation
     */
    public function __construct(ValidationInterface $validation = null)
    {
        $this->tempReturnType = $this->returnType;

        $this->validation = $validation ?? Services::validation(null, false);
    }

    /**
     * Grabs the last error(s) that occurred. If data was validated,
     * it will first check for errors there, otherwise will try to
     * grab the last error from the Database connection.
     *
     * @param boolean $forceDB Always grab the db error, not validation
     *
     * @return array|null
     */
    public function errors()
    {
        return $this->validation->getErrors();
    }

    /**
     * Allows to set validation messages.
     * It could be used when you have to change default or override current validate messages.
     *
     * @param array $validationMessages Value
     *
     * @return $this
     */
    public function setValidationMessages(array $validationMessages)
    {
        $this->validationMessages = $validationMessages;

        return $this;
    }

    /**
     * Allows to set field wise validation message.
     * It could be used when you have to change default or override current validate messages.
     *
     * @param string $field         Field Name
     * @param array  $fieldMessages Validation messages
     *
     * @return $this
     */
    public function setValidationMessage(string $field, array $fieldMessages)
    {
        $this->validationMessages[$field] = $fieldMessages;

        return $this;
    }

    /**
     * Allows to set validation rules.
     * It could be used when you have to change default or override current validate rules.
     *
     * @param array $validationRules Value
     *
     * @return $this
     */
    public function setValidationRules(array $validationRules)
    {
        $this->validationRules = $validationRules;

        return $this;
    }

    /**
     * Allows to set field wise validation rules.
     * It could be used when you have to change default or override current validate rules.
     *
     * @param string       $field      Field Name
     * @param string|array $fieldRules Validation rules
     *
     * @return $this
     */
    public function setValidationRule(string $field, $fieldRules)
    {
        $this->validationRules[$field] = $fieldRules;

        return $this;
    }

    /**
     * Should validation rules be removed before saving?
     * Most handy when doing updates.
     *
     * @param boolean $choice Value
     *
     * @return $this
     */
    public function cleanRules(bool $choice = false)
    {
        $this->cleanValidationRules = $choice;

        return $this;
    }

    /**
     * Validate the data against the validation rules (or the validation group)
     * specified in the class property, $validationRules.
     *
     * @param array|object $data Data
     *
     * @return boolean
     */
    public function validate($data): bool
    {
        $rules = $this->getValidationRules();

        if (empty($rules) || empty($data))
        {
            return true;
        }

        //Validation requires array, so cast away.
        if (is_object($data))
        {
            $data = (array) $data;
        }

        $rules = $this->cleanValidationRules ? $this->cleanValidationRules($rules, $data) : $rules;

        // If no data existed that needs validation
        // our job is done here.
        if (empty($rules))
        {
            return true;
        }

        return $this->validation->setRules($rules, $this->validationMessages)->run($data, null, $this->DBGroup);
    }

    /**
     * Returns the model's defined validation rules so that they
     * can be used elsewhere, if needed.
     *
     * @param array $options Options
     *
     * @return array
     */
    public function getValidationRules(array $options = []): array
    {
        $rules = $this->validationRules;

        // ValidationRules can be either a string, which is the group name,
        // or an array of rules.
        if (is_string($rules))
        {
            // @phpstan-ignore-next-line
            $rules = $this->validation->loadRuleGroup($rules);
        }

        if (isset($options['except']))
        {
            $rules = array_diff_key($rules, array_flip($options['except']));
        }
        elseif (isset($options['only']))
        {
            $rules = array_intersect_key($rules, array_flip($options['only']));
        }

        return $rules;
    }

    /**
     * Returns the model's define validation messages so they
     * can be used elsewhere, if needed.
     *
     * @return array
     */
    public function getValidationMessages(): array
    {
        return $this->validationMessages;
    }

    /**
     * Removes any rules that apply to fields that have not been set
     * currently so that rules don't block updating when only updating
     * a partial row.
     *
     * @param array      $rules Array containing field name and rule
     * @param array|null $data  Data
     *
     * @return array
     */
    protected function cleanValidationRules(array $rules, array $data = null): array
    {
        if (empty($data))
        {
            return [];
        }

        foreach ($rules as $field => $rule)
        {
            if (! array_key_exists($field, $data))
            {
                unset($rules[$field]);
            }
        }

        return $rules;
    }

    public function save($data): bool
    {
        return $this->validate($data);
    }

    public function insertID()
    {
        return null;
    }

    /**
     * Returns the id value for the data array or object
     *
     * @param array|object $data Data
     *
     * @return integer|array|string|null
     */
    public function idValue($data)
    {
        if (is_object($data) && isset($data->{$this->primaryKey}))
        {
            return $data->{$this->primaryKey};
        }

        if (is_array($data) && ! empty($data[$this->primaryKey]))
        {
            return $data[$this->primaryKey];
        }

        return null;
    }


}