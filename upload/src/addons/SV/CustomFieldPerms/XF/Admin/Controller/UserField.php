<?php

namespace SV\CustomFieldPerms\XF\Admin\Controller;

use SV\CustomFieldPerms\CustomFieldAdminTrait;

class UserField extends XFCP_UserField
{
    protected static $tableName = 'xf_user_field';
    use CustomFieldAdminTrait;
}
