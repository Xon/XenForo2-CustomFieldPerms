<?php

namespace SV\CustomFieldPerms\XF\Entity;

use SV\CustomFieldPerms\Setup;
use XF\Mvc\Entity\Structure;

/*
 * Extends \XF\Entity\UserField
 */
class UserField extends XFCP_UserField
{
    /**
     * Insert additional columns into UserField
     *
     * @param Structure $structure
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        foreach (Setup::$tables1['xf_user_field'] as $column => $details)
        {
            $structure->columns[$column] = ['type' => $details['entity_type'], 'default' => $details['entity_default']];
        }

        return $structure;
    }
}
