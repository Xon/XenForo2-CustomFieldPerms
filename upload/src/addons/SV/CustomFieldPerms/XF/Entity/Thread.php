<?php

namespace SV\CustomFieldPerms\XF\Entity;

use SV\CustomFieldPerms\CustomFieldFilterTrait;
use SV\CustomFieldPerms\IFieldEntityPerm;
use XF\Entity\User as UserEntity;
use XF\Repository\ThreadField as ThreadFieldRepo;

class Thread extends XFCP_Thread implements IFieldEntityPerm
{
    use CustomFieldFilterTrait;

    protected $customFieldRepo         = ThreadFieldRepo::class;
    protected $customFieldContainerKey = 'customFields.threads';

    /**
     * @return null|UserEntity
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getContentUser()
    {
        return $this->User;
    }
}

