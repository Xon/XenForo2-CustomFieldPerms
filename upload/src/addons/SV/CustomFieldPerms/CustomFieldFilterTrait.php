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
        /** @noinspection PhpUndefinedClassInspection */
        $set = parent::getCustomFields();

        if (isset($this->customFieldRepo) && isset($this->customFieldContainerKey))
        {
            foreach ($set->getDefinitionSet()->getIterator() as $field)
            {
                if (!isset($field['cfp_v_input_enable']))
                {
                    /** @noinspection PhpUndefinedMethodInspection */
                    $this->repository($this->customFieldRepo)
                         ->rebuildFieldCache()
                    ;
                    \XF::app()->container()->decache($this->customFieldContainerKey);

                    /** @noinspection PhpUndefinedClassInspection */
                    $set = parent::getCustomFields();

                    break;
                }
            }
        }

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