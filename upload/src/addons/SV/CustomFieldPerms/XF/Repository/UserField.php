<?php

namespace SV\CustomFieldPerms\XF\Repository;

use XF\Mvc\Reply\View;

class UserField extends XFCP_UserField
{

    /**
     * @param $userId
     */
    public function rebuildUserFieldValuesCache($userId)
    {
        $result = parent::rebuildUserFieldValuesCache($userId);

        $fieldPerms = $this->finder('XF:UserField')
                           ->fetchColumns('field_id', 'sedo_perms_output_ui_enable', 'sedo_perms_output_ui_val');

        $permsCache = [];
        foreach ($fieldPerms as $fieldPerm)
        {
            if ($fieldPerm['sedo_perms_output_ui_enable'])
            {
                $permsCache[$fieldPerm['field_id']] = $fieldPerm['sedo_perms_output_ui_val'];
            }
        }

        \XF::registry()->set('sedo_perms_ui_users', $permsCache);

        return $result;
    }

    /**
     * Insert additionalFilters into a reply.
     * These are then passed in to the custom_fields_macro via a template modification.
     *
     * @param       $reply
     * @param       $key
     * @param array $supplementaryFilters
     */
    public function applyUsergroupCustomFieldPermissionFilters(&$reply, $key, $supplementaryFilters = [])
    {
        if ($reply instanceof View)
        {
            $visitorUserGroups = array_merge(
                [\XF::visitor()->user_group_id],
                \XF::visitor()->secondary_group_ids
            );
            $reply->setParam(
                'additionalFilters',
                array_merge(
                    [
                        'check_usergroup_perms' => [
                            $visitorUserGroups, $key
                        ]
                    ],
                    $supplementaryFilters
                )
            );
        }
    }
}
