<?php

namespace SV\CustomFieldPerms\NF\Tickets\Entity;

use NF\Tickets\Repository\TicketField as TicketFieldRepo;
use SV\CustomFieldPerms\CustomFieldFilterTrait;
use SV\CustomFieldPerms\IFieldEntityPerm;
use XF\Entity\User as UserEntity;

/**
 * @extends \NF\Tickets\Entity\Ticket
 */
class Ticket extends XFCP_Ticket implements IFieldEntityPerm
{
    use CustomFieldFilterTrait;

    protected $customFieldRepo         = TicketFieldRepo::class;
    protected $customFieldContainerKey = 'customFields.tickets';

    /**
     * @return null|UserEntity
     * @noinspection PhpMissingReturnTypeInspection
     */
    public function getContentUser()
    {
        return $this->User;
    }
}