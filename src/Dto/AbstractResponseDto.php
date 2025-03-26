<?php
declare(strict_types=1);

namespace App\Dto;

abstract class AbstractResponseDto
{
    /**
     * @return mixed[] 
     */
    public function toArray(): array
    {
        $data = [];
        foreach (get_object_vars($this) as $key => $value) {
            if ($value instanceof self) {
                $data[$key] = $value->toArray();
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
