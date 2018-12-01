<?php

namespace SV\CustomFieldPerms\Repository;

use SV\CustomFieldPerms\IFieldEntityPerm;
use SV\CustomFieldPerms\SetEntity;
use XF\Entity\User;
use XF\Mvc\Entity\Repository;

class Field extends Repository
{
    protected $svVisitorGroupIds = [];

    protected function getUserGroups(User $user = null)
    {
        if (!$user)
        {
            return [];
        }

        $userId = $user->user_id;
        if (!isset($this->visitorGroupIds[$userId]))
        {
            $this->svVisitorGroupIds[$userId] = array_merge(
                [$user->user_group_id],
                array_map('intval', $user->secondary_group_ids)
            );
        }

        return $this->svVisitorGroupIds[$userId];
    }

    /**
     * Insert additionalFilters for various custom_fields_macro arguments
     *
     * @param array  $arguments
     * @param string $key
     */
    public function applyUsergroupCustomFieldPermissionFilters(&$arguments, $key)
    {
        $entity = SetEntity::getEntity($arguments['set']);
        if (!$entity instanceof IFieldEntityPerm)
        {
            return;
        }

        $visitorUserGroups = $this->getUserGroups(\XF::visitor());
        $contentUserGroups = $this->getUserGroups($entity->getContentUser());

        if (!isset($arguments['additionalFilters']))
        {
            $arguments['additionalFilters'] = [];
        }

        $arguments['additionalFilters'] =
            array_merge(
                $arguments['additionalFilters'],
                [
                    'check_visitor_usergroup_perms' => [
                        $visitorUserGroups, $key
                    ],
                    'check_content_usergroup_perms' => [
                        $contentUserGroups, $key
                    ]
                ]
            );
    }
}
