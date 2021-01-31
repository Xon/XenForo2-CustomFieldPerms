<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\CustomFieldPerms;

use XF\CustomField\Set;

class SetEntity extends Set
{
    /**
     * @param Set $set
     * @return \XF\Mvc\Entity\Entity
     */
    public static function getEntity(Set $set)
    {
        if ($set instanceof Set)
        {
            return $set->entity;
        }

        return null;
    }
}