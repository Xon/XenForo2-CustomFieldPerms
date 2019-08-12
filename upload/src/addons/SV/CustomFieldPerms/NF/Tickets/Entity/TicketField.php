<?php

namespace SV\CustomFieldPerms\NF\Tickets\Entity;

use SV\CustomFieldPerms\CustomFieldEntityTrait;
use SV\CustomFieldPerms\IFieldPerm;

/**
 * Extends \NF\Tickets\Entity\TicketField
 */
class TicketField extends XFCP_TicketField implements IFieldPerm
{
    use CustomFieldEntityTrait;
}