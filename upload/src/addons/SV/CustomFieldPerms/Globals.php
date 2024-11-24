<?php

namespace SV\CustomFieldPerms;

use XF\Mvc\Entity\Entity;

class Globals
{
    /** @noinspection PhpFullyQualifiedNameUsageInspection */
    public static $repos = [
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
    /** @noinspection PhpFullyQualifiedNameUsageInspection */
    public static $entities = [
        \XF\Entity\UserField::class     => [
            'cfp_v_input_enable'     => self::COLUMN_FLAG_VALUE,
            'cfp_v_input_val'        => self::COLUMN_GROUP_LIST,

            'cfp_v_output_ui_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_ui_val'    => self::COLUMN_GROUP_LIST,

            'cfp_v_output_pp_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_pp_val'    => self::COLUMN_GROUP_LIST,
        ],
        \XF\Entity\ThreadField::class   => [
            'cfp_o_input_bypass'       => self::COLUMN_FLAG_VALUE,
            'cfp_o_output_ui_bypass'   => self::COLUMN_FLAG_VALUE,

            'cfp_v_input_enable'       => self::COLUMN_FLAG_VALUE,
            'cfp_v_input_val'          => self::COLUMN_GROUP_LIST,

            'cfp_v_output_ui_enable'   => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_ui_val'      => self::COLUMN_GROUP_LIST,

            'cfp_c_output_ui_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_c_output_ui_val'    => self::COLUMN_GROUP_LIST,
        ],
        \XFMG\Entity\MediaField::class => [
            'cfp_o_input_bypass'       => self::COLUMN_FLAG_VALUE,
            'cfp_o_output_ui_bypass'   => self::COLUMN_FLAG_VALUE,

            'cfp_v_input_enable'       => self::COLUMN_FLAG_VALUE,
            'cfp_v_input_val'          => self::COLUMN_GROUP_LIST,

            'cfp_v_output_ui_enable'   => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_ui_val'      => self::COLUMN_GROUP_LIST,

            'cfp_c_output_ui_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_c_output_ui_val'    => self::COLUMN_GROUP_LIST,
        ],
        \NF\Tickets\Entity\TicketField::class => [
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

    private function __construct() { }
}