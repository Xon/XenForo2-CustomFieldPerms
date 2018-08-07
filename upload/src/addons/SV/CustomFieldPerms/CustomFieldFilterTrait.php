<?php

namespace SV\CustomFieldPerms;

use XF\CustomField\Set;

trait CustomFieldFilterTrait
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
            'check_visitor_usergroup_perms', function (array $field, $usergroups, $keyWithPerms) {
            if (!empty($field['cfp_v_' . $keyWithPerms . '_enable']))
            {
                $permittedUserGroups = $field['cfp_v_' . $keyWithPerms . '_val'];

                return !empty(array_intersect($usergroups, $permittedUserGroups))
                       || in_array('all', $permittedUserGroups);
            }

            return true;
        });

        $set->getDefinitionSet()->addFilter(
            'check_content_usergroup_perms', function (array $field, $usergroups, $keyWithPerms) {
            if (!empty($field['cfp_c_' . $keyWithPerms . '_enable']))
            {
                $permittedUserGroups = $field['cfp_c_' . $keyWithPerms . '_val'];

                return !empty(array_intersect($usergroups, $permittedUserGroups))
                       || in_array('all', $permittedUserGroups);
            }

            return true;
        });

        return $set;
    }
}