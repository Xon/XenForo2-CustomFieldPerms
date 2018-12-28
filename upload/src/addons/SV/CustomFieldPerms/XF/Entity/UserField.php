<?php

namespace SV\CustomFieldPerms\XF\Entity;

use SV\CustomFieldPerms\CustomFieldEntityTrait;
use SV\CustomFieldPerms\IFieldPerm;

class UserField extends XFCP_UserField implements IFieldPerm
{
    use CustomFieldEntityTrait;
}
