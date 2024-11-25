<?php

namespace SV\CustomFieldPerms\NF\Tickets\Entity;

use SV\CustomFieldPerms\Entity\CustomFieldEntityTrait;
use SV\CustomFieldPerms\Entity\IFieldPerm;

/**
 * @extends \NF\Tickets\Entity\TicketField
 */
class TicketField extends XFCP_TicketField implements IFieldPerm
{
    use CustomFieldEntityTrait;
}