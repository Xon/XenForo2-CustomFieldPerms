<?php

namespace SV\CustomFieldPerms\XF\Repository;

use XF\Mvc\Reply\View;

class UserField extends XFCP_UserField
{
    /**
     * Insert additionalFilters into a reply.
     * These are then passed in to the custom_fields_macro via a template modification.
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

        $visitorUserGroups = array_merge(
            [\XF::visitor()->user_group_id],
            \XF::visitor()->secondary_group_ids
        );

        if (!isset($arguments['additionalFilters']))
        {
            $arguments['additionalFilters'] = [];
        }

        $arguments['additionalFilters'] =
            array_merge(
                $arguments['additionalFilters'],
                [
                    'check_usergroup_perms' => [
                        $visitorUserGroups, $key
                    ]
                ]
            );
    }
}
