<?php

namespace SV\CustomFieldPerms\XF\Entity;

use SV\CustomFieldPerms\Entity\CustomFieldEntityTrait;
use SV\CustomFieldPerms\Entity\IFieldPerm;

class UserField extends XFCP_UserField implements IFieldPerm
{
    use CustomFieldEntityTrait;
}
