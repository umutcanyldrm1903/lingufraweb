<?php

namespace App\Support;

class SettingBag
{
    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function __get(string $name): mixed
    {
        return null;
    }
}

