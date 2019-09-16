<?php

namespace SV\CustomFieldPerms\NF\Tickets\Entity;

use SV\CustomFieldPerms\CustomFieldFilterTrait;
use SV\CustomFieldPerms\IFieldEntityPerm;
use XF\Entity\User;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * Extends \NF\Tickets\Entity\Ticket
 */
class Ticket extends XFCP_Ticket implements IFieldEntityPerm
{
    use CustomFieldFilterTrait;

    protected $customFieldRepo = 'NF\Tickets:TicketField';
    protected $customFieldContainerKey = 'customFields.tickets';

    /**
     * @return null|User
     */
    function getContentUser()
    {
        return $this->User;
    }
}