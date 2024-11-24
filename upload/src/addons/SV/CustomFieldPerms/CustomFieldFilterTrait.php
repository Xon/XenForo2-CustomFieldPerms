<?php
/**
 * @noinspection PhpMultipleClassDeclarationsInspection
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\CustomFieldPerms;

use SV\StandardLib\Helper;
use XF\CustomField\Set as CustomFieldSet;
use XF\Entity\User as UserEntity;
use XF\Repository\AbstractField as AbstractFieldRepo;
use function array_intersect;
use function array_map;
use function array_merge;
use function in_array;
use function is_array;

trait CustomFieldFilterTrait
{
    /**
     * Insert a new filter type into the DefinitionSet.
     *
     * @return CustomFieldSet
     * @throws \Exception
     */
    public function getCustomFields()
    {
        $set = parent::getCustomFields();

        if (isset($this->customFieldRepo) && isset($this->customFieldContainerKey))
        {
            foreach ($set->getDefinitionSet()->getIterator() as $field)
            {
                if (!isset($field['cfp_v_input_enable']))
                {
                    $customFieldRepo = Helper::repository($this->customFieldRepo);
                    if ($customFieldRepo instanceof AbstractFieldRepo)
                    {
                        $customFieldRepo->rebuildFieldCache();
                    }
                    \XF::app()->container()->decache($this->customFieldContainerKey);

                    $set = parent::getCustomFields();

                    break;
                }
            }
        }

        $definitionSet = $set->getDefinitionSet();
        $definitionSet->addFilter(
            'check_visitor_usergroup_perms', function (array $field, array $userGroups, string $keyWithPerms, ?UserEntity $visitor = null, ?UserEntity $contentUser = null) {
            $bypassValue = $field['cfp_o_' . $keyWithPerms . '_bypass'] ?? null;
            if ($bypassValue !== null)
            {
                $value = (int)$bypassValue;
                if ($value !== 0)
                {
                    $visitorUserId = $visitor->user_id ?? 0;

                    if ($visitorUserId !== 0 && $visitorUserId === ($contentUser->user_id ?? 0))
                    {
                        return true;
                    }
                }
            }

            if (!empty($field['cfp_v_' . $keyWithPerms . '_enable']))
            {
                $permittedUserGroups = $field['cfp_v_' . $keyWithPerms . '_val'];

                return !is_array($permittedUserGroups) ||
                       !empty(array_intersect($userGroups, $permittedUserGroups))
                       || $permittedUserGroups === [-1];
            }

            return true;
        });

        $definitionSet->addFilter(
            'check_content_usergroup_perms', function (array $field, array $userGroups, string $keyWithPerms, ?UserEntity $visitor = null, ?UserEntity $contentUser = null) {
            $bypassValue = $field['cfp_o_' . $keyWithPerms . '_bypass'] ?? null;
            if ($bypassValue !== null)
            {
                $value = (int)$bypassValue;
                if ($value !== 0)
                {
                    $visitorUserId = $visitor->user_id ?? 0;

                    if ($visitorUserId !== 0 && $visitorUserId === ($contentUser->user_id ?? 0))
                    {
                        return true;
                    }
                }
            }

            if (!empty($field['cfp_c_' . $keyWithPerms . '_enable']))
            {
                $permittedUserGroups = $field['cfp_c_' . $keyWithPerms . '_val'];

                return !is_array($permittedUserGroups) ||
                       !empty(array_intersect($userGroups, $permittedUserGroups))
                       || $permittedUserGroups === [-1];
            }

            return true;
        });

        $filters = DefinitionSetAccess::getFilters($definitionSet);
        /** @var null|\Closure(array, CustomFieldSet, bool): bool $editableCallback */
        $editableCallback = $filters['editable'] ?? null;
        $definitionSet->addFilter('editable', function(array $field, CustomFieldSet $set, $editMode) use ($editableCallback)
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
                    $userGroups = array_merge([$user->user_group_id], array_map('\intval', $user->secondary_group_ids));

                    $permittedUserGroups = $field['cfp_v_input_val'];

                    return !is_array($permittedUserGroups) ||
                           !empty(array_intersect($userGroups, $permittedUserGroups))
                           || $permittedUserGroups === [-1];
                }
            }

            return true;
        });

        return $set;
    }
}