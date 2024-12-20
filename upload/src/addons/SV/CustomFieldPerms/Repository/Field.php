<?php

namespace SV\CustomFieldPerms\Repository;

use LogicException;
use NF\Tickets\Entity\TicketField as TicketFieldEntity;
use NF\Tickets\Finder\TicketField as TicketFieldFinder;
use SV\CustomFieldPerms\Entity\IFieldEntityPerm;
use SV\CustomFieldPerms\Entity\SetEntity;
use SV\StandardLib\Helper;
use XF\Admin\App as AdminApp;
use XF\Db\Schema\Alter;
use XF\Entity\ThreadField as ThreadFieldEntity;
use XF\Entity\User as UserEntity;
use XF\Entity\UserField as UserFieldEntity;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Repository;
use XF\Repository\AbstractField;
use XF\Template\Templater;
use XFMG\Entity\MediaField as MediaFieldEntity;
use function array_fill_keys;
use function array_key_exists;
use function array_map;
use function array_merge;
use function array_values;
use function class_exists;
use function count;
use function get_class;
use function implode;

class Field extends Repository
{
    public static function get(): self
    {
        return Helper::repository(self::class);
    }

    public function applyCustomFieldFilters(Templater $templater): bool
    {
        return !(\XF::app() instanceof AdminApp) || ($templater instanceof \XF\Mail\Templater);
    }

    /** @noinspection PhpFullyQualifiedNameUsageInspection */
    public $repos = [
        \XF\Repository\UserField::class   => 'XF',
        \XF\Repository\ThreadField::class => 'XF',
        \XFMG\Repository\MediaField::class  => 'XFMG',
        \NF\Tickets\Repository\TicketField::class => 'NF\Tickets'
    ];

    public const COLUMN_FLAG_VALUE = [
        'sql' => ['type' => 'tinyint', 'default' => 0],
        'entity' => ['type' => Entity::UINT, 'default' => 0, 'api' => true],
        'filter_type' => 'uint',
    ];
    public const COLUMN_GROUP_LIST = [
        'sql' => ['type' => 'blob', 'default' => null, 'nullable' => true],
        'entity' => ['type' => Entity::LIST_COMMA, 'default' => null, 'api' => true, 'list' => ['type' => 'int', 'unique' => true, 'sort' => SORT_NUMERIC]],
        'isGroupList' => true,
    ];

    // note; CustomFieldFilterTrait expected that cfp_v_input_enable is always in each entity
    public $entities = [
        UserFieldEntity::class => [
            'cfp_v_input_enable'     => self::COLUMN_FLAG_VALUE,
            'cfp_v_input_val'        => self::COLUMN_GROUP_LIST,

            'cfp_v_output_ui_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_ui_val'    => self::COLUMN_GROUP_LIST,

            'cfp_v_output_pp_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_pp_val'    => self::COLUMN_GROUP_LIST,
        ],
        ThreadFieldEntity::class => [
            'cfp_o_input_bypass'       => self::COLUMN_FLAG_VALUE,
            'cfp_o_output_ui_bypass'   => self::COLUMN_FLAG_VALUE,

            'cfp_v_input_enable'       => self::COLUMN_FLAG_VALUE,
            'cfp_v_input_val'          => self::COLUMN_GROUP_LIST,

            'cfp_v_output_ui_enable'   => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_ui_val'      => self::COLUMN_GROUP_LIST,

            'cfp_c_output_ui_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_c_output_ui_val'    => self::COLUMN_GROUP_LIST,
        ],
        MediaFieldEntity::class => [
            'cfp_o_input_bypass'       => self::COLUMN_FLAG_VALUE,
            'cfp_o_output_ui_bypass'   => self::COLUMN_FLAG_VALUE,

            'cfp_v_input_enable'       => self::COLUMN_FLAG_VALUE,
            'cfp_v_input_val'          => self::COLUMN_GROUP_LIST,

            'cfp_v_output_ui_enable'   => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_ui_val'      => self::COLUMN_GROUP_LIST,

            'cfp_c_output_ui_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_c_output_ui_val'    => self::COLUMN_GROUP_LIST,
        ],
        TicketFieldEntity::class => [
            'cfp_o_input_bypass'       => self::COLUMN_FLAG_VALUE,
            'cfp_o_output_ui_bypass'   => self::COLUMN_FLAG_VALUE,

            'cfp_v_input_enable'       => self::COLUMN_FLAG_VALUE,
            'cfp_v_input_val'          => self::COLUMN_GROUP_LIST,

            'cfp_v_output_ui_enable'   => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_ui_val'      => self::COLUMN_GROUP_LIST,

            'cfp_c_output_ui_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_c_output_ui_val'    => self::COLUMN_GROUP_LIST,
        ],
    ];

    public function getExtendedEntities(): array
    {
        return $this->entities;
    }

    public function getExtendedEntity(string $class): ?array
    {
        $entityClassName = \XF::stringToClass($class, '%s\Entity\%s');
        $entities = $this->getExtendedEntities();
        return $entities[$entityClassName] ?? null;
    }

    /** @var array<int,array<int,int>> */
    protected $svVisitorGroupIds = [];

    /**
     * @param UserEntity|null $user
     * @return int[]
     */
    protected function getUserGroups(?UserEntity $user = null): array
    {
        if ($user === null)
        {
            return [];
        }

        $userId = $user->user_id;
        if (!array_key_exists($userId, $this->svVisitorGroupIds))
        {
            $this->svVisitorGroupIds[$userId] = array_merge(
                [$user->user_group_id],
                array_map('\intval', $user->secondary_group_ids)
            );
        }

        return $this->svVisitorGroupIds[$userId];
    }

    /**
     * Insert additionalFilters for various custom_fields_macro arguments
     */
    public function applyUsergroupCustomFieldPermissionFilters(array &$arguments, string $key): void
    {
        $entity = SetEntity::getEntity($arguments['set']);
        if (!$entity instanceof IFieldEntityPerm)
        {
            return;
        }

        $visitor = \XF::visitor();
        $contentUser = $entity->getContentUser();
        $visitorUserGroups = $this->getUserGroups($visitor);
        $contentUserGroups = $this->getUserGroups($contentUser);

        $arguments['additionalFilters'] = array_merge(
            $arguments['additionalFilters'] ?? [],
            [
                'check_visitor_usergroup_perms' => [
                    $visitorUserGroups, $key, $visitor, $contentUser,
                ],
                'check_content_usergroup_perms' => [
                    $contentUserGroups, $key, $visitor, $contentUser,
                ],
            ]
        );
    }

    public function applyCustomFieldSchemaChanges(?string $addonId = null): void
    {
        if ($addonId)
        {
            $addOns = array_fill_keys(array_values($this->repos), true);
            if (empty($addOns[$addonId]))
            {
                return;
            }
        }
        $sm = \XF::db()->getSchemaManager();
        foreach ($this->getExtendedEntities() as $entity => $columns)
        {
            $entityStructure = Helper::getEntityStructure($entity);
            if ($entityStructure !== null && $sm->tableExists($entityStructure->table))
            {
                $sm->alterTable($entityStructure->table, function (Alter $table) use ($columns) {
                    foreach ($columns as $column => $columnDefinition)
                    {
                        $details = $columnDefinition['sql'];
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

    public function applyPostInstallChanges(?string $addonId = null): void
    {
        $sm = \XF::db()->getSchemaManager();
        if (($addonId === null || $addonId === 'NF/Tickets') &&
            $sm->tableExists('xf_nf_tickets_ticket_field') &&
            class_exists(TicketFieldEntity::class))
        {
            $fields = Helper::finder(TicketFieldFinder::class)->fetch();

            if ($fields->count())
            {
                /** @var TicketFieldEntity $field */
                foreach ($fields AS $field)
                {
                    $updates = [];
                    if (isset($field->structure()->columns['usable_user_group_ids']))
                    {
                        if (isset($field->usable_user_group_ids[0]) && $field->usable_user_group_ids[0] === '-1')
                        {
                            $updates = array_merge($updates, [
                                'cfp_v_input_enable' => 0,
                                'cfp_v_input_val'    => null,
                            ]);
                        }
                        else
                        {
                            $updates = array_merge($updates, [
                                'cfp_v_input_enable' => 1,
                                'cfp_v_input_val'    => $field->getValueSourceEncoded('usable_user_group_ids')
                            ]);
                        }
                    }
                    if (isset($field->structure()->columns['viewable_user_group_ids']))
                    {
                        if (isset($field->viewable_user_group_ids[0]) && $field->viewable_user_group_ids[0] === '-1')
                        {
                            $updates = array_merge($updates, [
                                'cfp_v_output_ui_enable' => 0,
                                'cfp_v_output_ui_val'    => null,
                            ]);
                        }
                        else
                        {
                            $updates = array_merge($updates, [
                                'cfp_v_output_ui_enable' => 1,
                                'cfp_v_output_ui_val'    => $field->getValueSourceEncoded('viewable_user_group_ids')
                            ]);
                        }
                    }

                    if (isset($field->structure()->columns['viewable_owner_user_group_ids']))
                    {
                        if (isset($field->viewable_owner_user_group_ids[0]) && $field->viewable_owner_user_group_ids[0] === '-1')
                        {
                            $updates = array_merge($updates, [
                                'cfp_c_output_ui_enable' => 0,
                                'cfp_c_output_ui_val'    => null,
                            ]);
                        }
                        else
                        {
                            $updates = array_merge($updates, [
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

    protected function updateEntity(Entity $entity, array $updates): void
    {
        if (count($updates) === 0)
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
                throw new LogicException('Found null in primary key for entity. Was this called before saving?');
            }
            $conditions[] = "`$key` = " . $db->quote($value);
        }

        if (!$conditions)
        {
            throw new LogicException('No primary key defined for entity ' . get_class($this));
        }

        $condition = implode(' AND ', $conditions);

        $this->db()->update($entity->structure()->table, $updates, $condition);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function rebuildCaches(?string $addonId = null)
    {
        $addOns = \XF::app()->container('addon.cache');
        foreach($this->repos as $repoName => $addOn)
        {
            if (isset($addOns[$addOn]))
            {
                $repo = Helper::repository($repoName);
                if ($repo instanceof AbstractField)
                {
                    $repo->rebuildFieldCache();
                }
            }
        }
    }
}
