<?php
/**
 * @noinspection PhpMissingParamTypeInspection
 */

namespace SV\CustomFieldPerms\Repository;

use SV\CustomFieldPerms\Globals;
use SV\CustomFieldPerms\IFieldEntityPerm;
use SV\CustomFieldPerms\SetEntity;
use XF\Db\Schema\Alter;
use XF\Entity\User;
use XF\Mvc\Entity\Repository;

class Field extends Repository
{
    /** @var int[][] */
    protected $svVisitorGroupIds = [];

    protected function getUserGroups(User $user = null): array
    {
        if (!$user)
        {
            return [];
        }

        $userId = $user->user_id;
        if (!isset($this->visitorGroupIds[$userId]))
        {
            $this->svVisitorGroupIds[$userId] = \array_merge(
                [$user->user_group_id],
                \array_map('\intval', $user->secondary_group_ids)
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
    public function applyUsergroupCustomFieldPermissionFilters(array &$arguments, string $key)
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
            \array_merge(
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
    public function applyCustomFieldSchemaChanges($addonId = null)
    {
        if ($addonId)
        {
            $addOns = \array_fill_keys(\array_values(Globals::$repos), true);
            if (empty($addOns[$addonId]))
            {
                return;
            }
        }
        $sm = \XF::db()->getSchemaManager();
        foreach (Globals::$tables as $table => $columns)
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
                        if (\array_key_exists('default', $details))
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
    public function applyPostInstallChanges($addonId = null)
    {
        $sm = \XF::db()->getSchemaManager();
        if (($addonId === null || $addonId === 'NF/Tickets') &&
            $sm->tableExists('xf_nf_tickets_ticket_field') &&
            \class_exists('NF\Tickets\Entity\TicketField'))
        {
            $fields = $this->app()->finder('NF\Tickets:TicketField')->fetch();

            if ($fields->count())
            {
                /** @var \NF\Tickets\Entity\TicketField $field */
                foreach ($fields AS $field)
                {
                    $updates = [];
                    if (isset($field->structure()->columns['usable_user_group_ids']))
                    {
                        if (isset($field->usable_user_group_ids[0]) && $field->usable_user_group_ids[0] === '-1')
                        {
                            $updates = \array_merge($updates, [
                                'cfp_v_input_enable' => 0,
                                'cfp_v_input_val'    => \serialize([]),
                            ]);
                        }
                        else
                        {
                            $updates = \array_merge($updates, [
                                'cfp_v_input_enable' => 1,
                                'cfp_v_input_val'    => $field->getValueSourceEncoded('usable_user_group_ids')
                            ]);
                        }
                    }
                    if (isset($field->structure()->columns['viewable_user_group_ids']))
                    {
                        if (isset($field->viewable_user_group_ids[0]) && $field->viewable_user_group_ids[0] === '-1')
                        {
                            $updates = \array_merge($updates, [
                                'cfp_v_output_ui_enable' => 0,
                                'cfp_v_output_ui_val'    => \serialize([]),
                            ]);
                        }
                        else
                        {
                            $updates = \array_merge($updates, [
                                'cfp_v_output_ui_enable' => 1,
                                'cfp_v_output_ui_val'    => $field->getValueSourceEncoded('viewable_user_group_ids')
                            ]);
                        }
                    }

                    if (isset($field->structure()->columns['viewable_owner_user_group_ids']))
                    {
                        if (isset($field->viewable_owner_user_group_ids[0]) && $field->viewable_owner_user_group_ids[0] === '-1')
                        {
                            $updates = \array_merge($updates, [
                                'cfp_c_output_ui_enable' => 0,
                                'cfp_c_output_ui_val'    => \serialize([]),
                            ]);
                        }
                        else
                        {
                            $updates = \array_merge($updates, [
                                'cfp_c_output_ui_enable' => 1,
                                'cfp_c_output_ui_val'    => $field->getValueSourceEncoded('viewable_owner_user_group_ids')
                            ]);
                        }
                    }

                    // do not use entity to update, as this may be running in a process without the entity fully extended yet
                    $this->updateEntity($field, $updates);
                }
            }
        }
    }

    protected function updateEntity(\XF\Mvc\Entity\Entity $entity, $updates)
    {
        if (!$updates)
        {
            return;
        }
        $conditions = [];
        $db = $this->db();
        foreach ((array)$entity->structure()->primaryKey AS $key)
        {
            $value = $entity->getValue($key);
            if ($value === null)
            {
                throw new \LogicException("Found null in primary key for entity. Was this called before saving?");
            }
            $conditions[] = "`$key` = " . $db->quote($value);
        }

        if (!$conditions)
        {
            throw new \LogicException("No primary key defined for entity " . \get_class($this));
        }

        $condition = \implode(' AND ', $conditions);

        $this->db()->update($entity->structure()->table, $updates, $condition);
    }

    /**
     * @param string|null $addonId
     */
    public function rebuildCaches(/** @noinspection PhpUnusedParameterInspection */
        $addonId = null)
    {
        $addOns = \XF::app()->container('addon.cache');
        foreach(Globals::$repos as $repoName => $addOn)
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
