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

    public function getSortItems() : array
    {
        return $this->sortItems;
    }

    public function sort(string $key)
    {
        Assert::keyExists($this->sortItems, $key);

        $this->orderBy($this->sortItems[$key]); 

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