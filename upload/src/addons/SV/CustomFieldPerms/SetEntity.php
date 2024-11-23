<?php

namespace SV\CustomFieldPerms;

use XF\CustomField\Set as CustomFieldSet;
use XF\Mvc\Entity\Entity;

abstract class SetEntity extends CustomFieldSet
{
    /**
     * @param mixed $set
     * @return Entity
     */
    public static function getEntity($set): ?Entity
    {
        if ($set instanceof CustomFieldSet)
        {
            return $set->entity;
        }

        return null;
    }
}