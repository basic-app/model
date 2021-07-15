<?php
/**
 * @author Basic App Dev Team <dev@basic-app.com>
 * @license MIT
 * @link https://basic-app.com
 */
namespace BasicApp\Model;

use DateTime;

trait BuilderTrait
{

    public function filterWhere(array $params = [])
    {
        foreach($params as $key => $value)
        {
            if (!$value)
            {
                unset($params[$key]);
            }

            if (is_array($value))
            {
                $this->whereIn($key, $value);

                unset($params[$key]);
            }
        }

        if ($params)
        {
            $this->where($params);
        }

        return $this;
    }

    public function whereDate(string $column, DateTime $date)
    {
        $column = $this->protectIdentifiers($column);

        $this->where('DAY(' . $column . ')', $date->format('d'), false);

        $this->where('MONTH(' . $column . ')', $date->format('m'), false);

        $this->where('YEAR(' . $column . ')', $date->format('Y'), false);

        return $this;
    }

    public function filterWhereDate($column, $date)
    {
        if (!$date)
        {
            return $this;
        }

        return $this->whereDate($column, $date);
    }

}