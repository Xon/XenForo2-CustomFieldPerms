<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\CustomFieldPerms;

use XF\CustomField\Set;

trait CustomFieldFilterTrait
{
    /**
     * Insert a new filter type into the DefinitionSet.
     *
     * @return Set
     * @throws \Exception
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
                    /** @var \XF\Repository\AbstractField $customFieldRepo */
                    $customFieldRepo = $this->repository($this->customFieldRepo);
                    $customFieldRepo->rebuildFieldCache();
                    \XF::app()->container()->decache($this->customFieldContainerKey);

                    /** @noinspection PhpUndefinedClassInspection */
                    $set = parent::getCustomFields();

                    break;
                }
            }
        }

        $definitionSet = $set->getDefinitionSet();
        $definitionSet->addFilter(
            'check_visitor_usergroup_perms', function (array $field, $usergroups, $keyWithPerms) {
            if (!empty($field['cfp_v_' . $keyWithPerms . '_enable']))
            {
                $permittedUserGroups = $field['cfp_v_' . $keyWithPerms . '_val'];

                return !is_array($permittedUserGroups) ||
                       !empty(array_intersect($usergroups, $permittedUserGroups))
                       || in_array('all', $permittedUserGroups);
            }

            return true;
        });

        $definitionSet->addFilter(
            'check_content_usergroup_perms', function (array $field, $usergroups, $keyWithPerms) {
            if (!empty($field['cfp_c_' . $keyWithPerms . '_enable']))
            {
                $permittedUserGroups = $field['cfp_c_' . $keyWithPerms . '_val'];

                return !is_array($permittedUserGroups) ||
                       !empty(array_intersect($usergroups, $permittedUserGroups))
                       || in_array('all', $permittedUserGroups);
            }

            return true;
        });

        $filters = DefinitionSetAccess::getFilters($definitionSet);
        /** @var \Closure $editableCallback */
        $editableCallback = isset($filters['editable']) ? $filters['editable'] : null;
        $definitionSet->addFilter('editable', function(array $field, Set $set, $editMode) use ($editableCallback)
        {
            $editable = $editableCallback ? $editableCallback($field, $set, $editMode) : true;
            if (!$editable)
            {
                return false;
            }

            if ($editMode === 'user')
            {
                if (!empty($field['cfp_v_input_enable']))
                {
                    $user = \XF::visitor();
                    $usergroups = array_merge([$user->user_group_id], array_map('\intval', $user->secondary_group_ids));

                    $permittedUserGroups = $field['cfp_v_input_val'];

                    return !is_array($permittedUserGroups) ||
                           !empty(array_intersect($usergroups, $permittedUserGroups))
                           || in_array('all', $permittedUserGroups, true);
                }
            }

            return true;
        });

        return $set;
    }
}