<?php

namespace SV\CustomFieldPerms\XF\Entity;

use SV\CustomFieldPerms\CustomFieldFilterTrait;
use SV\CustomFieldPerms\IFieldEntityPerm;
use XF\Entity\User;

class UserProfile extends XFCP_UserProfile implements IFieldEntityPerm
{
    use CustomFieldFilterTrait;

    /**
     * @return null|User
     */
    function getContentUser()
    {
        return $this->User;
    }
}

