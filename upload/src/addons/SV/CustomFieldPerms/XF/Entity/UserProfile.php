<?php

namespace SV\CustomFieldPerms\XF\Entity;

use SV\CustomFieldPerms\CustomFieldFilterTrait;
use SV\CustomFieldPerms\IFieldEntityPerm;
use XF\Entity\User as UserEntity;
use XF\Repository\UserField as UserFieldRepo;

class UserProfile extends XFCP_UserProfile implements IFieldEntityPerm
{
    use CustomFieldFilterTrait;

    protected $customFieldRepo         = UserFieldRepo::class;
    protected $customFieldContainerKey = 'customFields.users';

    /**
     * @return null|UserEntity
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getContentUser()
    {
        return $this->User;
    }
}

