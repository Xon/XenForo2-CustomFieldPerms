<?php

namespace SV\CustomFieldPerms\Repository;

use XF\Mvc\Entity\Repository;

class Field extends Repository
{
    protected $svVisitorGroupIds = [];

    /**
     * Insert additionalFilters for various custom_fields_macro arguments
     *
     * @param array  $arguments
     * @param string $key
     */
    public function applyUsergroupCustomFieldPermissionFilters(&$arguments, $key)
    {
        if (empty($arguments['type']) || $arguments['type'] !== 'users')
        {
            return;
        }

        $user = \XF::visitor();
        if (!isset($this->visitorGroupIds[$user->user_id]))
        {
            $this->svVisitorGroupIds[$user->user_id] = array_merge(
                [$user->user_group_id],
                array_map('intval', $user->secondary_group_ids)
            );
        }

        if (!isset($arguments['additionalFilters']))
        {
            $arguments['additionalFilters'] = [];
        }

        $arguments['additionalFilters'] =
            array_merge(
                $arguments['additionalFilters'],
                [
                    'check_usergroup_perms' => [
                        $this->svVisitorGroupIds[$user->user_id], $key
                    ]
                ]
            );
    }
}
