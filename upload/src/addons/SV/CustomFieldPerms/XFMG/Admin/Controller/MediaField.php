<?php

namespace SV\CustomFieldPerms\XFMG\Admin\Controller;

use SV\CustomFieldPerms\CustomFieldAdminTrait;

class MediaField extends XFCP_MediaField
{
    protected static $tableName = 'xf_mg_media_field';
    use CustomFieldAdminTrait;
}