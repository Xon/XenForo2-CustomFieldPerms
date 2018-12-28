<?php

namespace SV\CustomFieldPerms\XF\Entity;

use SV\CustomFieldPerms\CustomFieldEntityTrait;
use SV\CustomFieldPerms\IFieldPerm;

class ThreadField extends XFCP_ThreadField implements IFieldPerm
{
    use CustomFieldEntityTrait;
}
