<?php

namespace SV\CustomFieldPerms\XFMG\Entity;

use SV\CustomFieldPerms\Entity\CustomFieldFilterTrait;
use SV\CustomFieldPerms\Entity\IFieldEntityPerm;
use XF\Entity\User as UserEntity;
use XFMG\Repository\MediaField as MediaFieldRepo;

class MediaItem extends XFCP_MediaItem implements IFieldEntityPerm
{
    use CustomFieldFilterTrait;

    protected $customFieldRepo         = MediaFieldRepo::class;
    protected $customFieldContainerKey = 'customFields.xfmgMediaFields';

    /**
     * @return null|UserEntity
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getContentUser()
    {
        return $this->User;
    }
}