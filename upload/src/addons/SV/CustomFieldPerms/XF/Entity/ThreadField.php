<?php

namespace SV\CustomFieldPerms\XF\Entity;

use SV\CustomFieldPerms\Entity\CustomFieldEntityTrait;
use SV\CustomFieldPerms\Entity\IFieldPerm;

class ThreadField extends XFCP_ThreadField implements IFieldPerm
{
    use CustomFieldEntityTrait;
}
