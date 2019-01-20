<?php

namespace SV\CustomFieldPerms\Repository;

use SV\CustomFieldPerms\IFieldEntityPerm;
use SV\CustomFieldPerms\SetEntity;
use SV\CustomFieldPerms\Setup;
use XF\Db\Schema\Alter;
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

    /**
     * @param string|null $addonId
     */
    public function applyCustomFieldSchemaChanges(/** @noinspection PhpUnusedParameterInspection */
        $addonId = null)
    {
        if ($addonId)
        {
            $addOns = \array_fill_keys(\array_values(Setup::$repos), true);
            if (empty($addOns[$addonId]))
            {
                return;
            }
        }
        $sm = \XF::db()->getSchemaManager();
        foreach (Setup::$tables1 as $table => $columns)
        {
            if ($sm->tableExists($table))
            {
                $sm->alterTable($table, function (Alter $table) use ($columns) {
                    foreach ($columns as $column => $details)
                    {
                        if ($table->getColumnDefinition($column))
                        {
                            $col = $table->changeColumn($column, $details['type']);
                        }
                        else
                        {
                            $col = $table->addColumn($column, $details['type']);
                        }

                        if (isset($details['nullable']))
                        {
                            $col->nullable($details['nullable']);
                        }
                        if (array_key_exists('default', $details))
                        {
                            $col->setDefault($details['default']);
                        }
                    }
                });
            }
        }
    }

    /**
     * @param string|null $addonId
     */
    public function rebuildCaches(/** @noinspection PhpUnusedParameterInspection */
        $addonId = null)
    {
        $addOns = \XF::app()->container('addon.cache');
        foreach(Setup::$repos as $repoName => $addOn)
        {
            if (isset($addOns[$addOn]))
            {
                /** @var \XF\Repository\AbstractField $repo */
                $repo = \XF::app()->repository($repoName);
                $repo->rebuildFieldCache();
            }
        }
    }
}
