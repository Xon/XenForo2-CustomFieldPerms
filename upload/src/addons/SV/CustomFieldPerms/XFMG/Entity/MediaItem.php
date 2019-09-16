<?php

namespace SV\CustomFieldPerms\XFMG\Entity;

use SV\CustomFieldPerms\CustomFieldFilterTrait;
use SV\CustomFieldPerms\IFieldEntityPerm;
use XF\Entity\User;

class MediaItem extends XFCP_MediaItem implements IFieldEntityPerm
{
    use CustomFieldFilterTrait;

    protected $customFieldRepo = 'XF:MediaField';
    protected $customFieldContainerKey = 'customFields.xfmgMediaFields';

    /**
     * @return null|User
     */
    function getContentUser()
    {
        return $this->User;
    }
}