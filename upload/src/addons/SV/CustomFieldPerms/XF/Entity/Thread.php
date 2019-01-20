<?php

namespace SV\CustomFieldPerms\XF\Entity;

use SV\CustomFieldPerms\CustomFieldFilterTrait;
use SV\CustomFieldPerms\IFieldEntityPerm;
use XF\Entity\User;

class Thread extends XFCP_Thread implements IFieldEntityPerm
{
    use CustomFieldFilterTrait;

    protected $customFieldRepo = 'XF:ThreadField';

    /**
     * @return null|User
     */
    function getContentUser()
    {
        return $this->User;
    }
}

