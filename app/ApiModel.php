<?php

namespace App;

interface ApiModel
{
    /**
     * Retrieve an array of allowed relationships
     *
     * @return array
     */
    public static function getAllowedRelationships(): array;
}
