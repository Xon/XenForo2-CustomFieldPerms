<?php

namespace SV\CustomFieldPerms\NF\Tickets\Admin\Controller;

use SV\CustomFieldPerms\CustomFieldAdminTrait;

/**
 * @extends \NF\Tickets\Admin\Controller\TicketField
 */
class TicketField extends XFCP_TicketField
{
    use CustomFieldAdminTrait;
}