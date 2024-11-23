<?php

namespace SV\CustomFieldPerms;

use XF\Entity\User as UserEntity;

/**
 * used to tag an entity as supported custom fields
 *
 * @package SV\CustomFieldPerms
 */
interface IFieldEntityPerm
{
    /**
     * @return null|UserEntity
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getContentUser();
}
