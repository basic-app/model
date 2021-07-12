<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Model;

use Webmozart\Assert\Assert;

trait SortTrait
{

    protected $sortItems = [];

    public function sort(string $key)
    {
        Assert::arrayHasKey($this->orderByItems, $key);

        $this->orderBy($this->orderByItems[$key]); 

        return $this;
    }

    public function filterSort(?string $key)
    {
        if ($key)
        {
            return $this->sort($key);
        }

        return $this;
    }

}