<?php

namespace SV\CustomFieldPerms;

use XF\Entity\User;

/**
 * used to tag an entity as supported custom fields
 *
 * @package SV\CustomFieldPerms
 */
interface IFieldEntityPerm
{
    /**
     * @return null|User
     */
    function getContentUser();
}
