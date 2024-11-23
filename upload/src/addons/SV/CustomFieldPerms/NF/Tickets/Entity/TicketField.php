<?php

namespace SV\CustomFieldPerms\NF\Tickets\Entity;

use SV\CustomFieldPerms\CustomFieldEntityTrait;
use SV\CustomFieldPerms\IFieldPerm;

/**
 * @extends \NF\Tickets\Entity\TicketField
 */
class TicketField extends XFCP_TicketField implements IFieldPerm
{
    use CustomFieldEntityTrait;
}