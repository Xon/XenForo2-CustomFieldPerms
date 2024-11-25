<?php

namespace SV\CustomFieldPerms\XFMG\Entity;

use SV\CustomFieldPerms\Entity\CustomFieldEntityTrait;
use SV\CustomFieldPerms\Entity\IFieldPerm;

class MediaField extends XFCP_MediaField implements IFieldPerm
{
    use CustomFieldEntityTrait;
}