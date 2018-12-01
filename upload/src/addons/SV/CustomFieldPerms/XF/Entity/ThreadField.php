<?php

namespace SV\CustomFieldPerms\XF\Entity;

use SV\CustomFieldPerms\CustomFieldEntityTrait;
use SV\CustomFieldPerms\IFieldPerm;

class ThreadField extends XFCP_ThreadField implements IFieldPerm
{
    protected static $tableName = 'xf_thread_field';
    use CustomFieldEntityTrait;
}
