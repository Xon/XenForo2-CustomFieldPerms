<?php

namespace SV\CustomFieldPerms;

use XF\CustomField\Set;

class SetEntity extends Set
{
    /**
     * @param Set $set
     * @return \XF\Mvc\Entity\Entity
     */
    public static function getEntity($set)
    {
        if ($set instanceof Set)
        {
            return $set->entity;
        }

        return null;
    }
}