<?php

namespace SV\CustomFieldPerms\XF\Admin\Controller;

use SV\CustomFieldPerms\CustomFieldAdminTrait;

class ThreadField extends XFCP_ThreadField
{
    protected static $tableName = 'xf_thread_field';
    use CustomFieldAdminTrait;
}
