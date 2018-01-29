<?php

namespace SV\CustomFieldPerms\XF\Entity;

use XF\CustomField\Set;

class UserProfile extends XFCP_UserProfile
{
    /**
     * Insert a new filter type into the DefinitionSet.
     *
     * @return Set
     */
    public function getCustomFields()
    {
        $set = parent::getCustomFields();

        $set->getDefinitionSet()->addFilter(
            'check_usergroup_perms', function (array $field, $usergroups, $keyWithPerms) {
            if (isset($field[$keyWithPerms . '_enable']) && $field[$keyWithPerms . '_enable'])
            {
                $permittedUsergroups = $field[$keyWithPerms . '_val'];

                return !empty(array_intersect($usergroups, $permittedUsergroups))
                       || in_array('all', $permittedUsergroups);
            }

            return true;
        }
        );

        return $set;
    }
}

