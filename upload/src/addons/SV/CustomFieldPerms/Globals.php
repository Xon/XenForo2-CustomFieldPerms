<?php
/**
 * @noinspection DuplicatedCode
 */

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
        'entity' => ['type' => Entity::UINT, 'default' => 0],
        'field_type' => 'uint',
    ];
    public const COLUMN_GROUP_LIST = [
        'sql' => ['type' => 'blob', 'default' => null, 'nullable' => true],
        'entity' => ['type' => Entity::SERIALIZED, 'default' => ''],
        'field_type' => 'array',
    ];

    // note; CustomFieldFilterTrait expected that cfp_v_input_enable is always in each entity
    public static $tables = [
        'xf_user_field'     => [
            'cfp_v_input_enable'     => self::COLUMN_FLAG_VALUE,
            'cfp_v_input_val'        => self::COLUMN_GROUP_LIST,

            'cfp_v_output_ui_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_ui_val'    => self::COLUMN_GROUP_LIST,

            'cfp_v_output_pp_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_pp_val'    => self::COLUMN_GROUP_LIST,
        ],
        'xf_thread_field'   => [
            'cfp_o_input_bypass'       => self::COLUMN_FLAG_VALUE,
            'cfp_o_output_ui_bypass'   => self::COLUMN_FLAG_VALUE,

            'cfp_v_input_enable'       => self::COLUMN_FLAG_VALUE,
            'cfp_v_input_val'          => self::COLUMN_GROUP_LIST,

            'cfp_v_output_ui_enable'   => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_ui_val'      => self::COLUMN_GROUP_LIST,

            'cfp_c_output_ui_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_c_output_ui_val'    => self::COLUMN_GROUP_LIST,
        ],
        'xf_mg_media_field' => [
            'cfp_o_input_bypass'       => self::COLUMN_FLAG_VALUE,
            'cfp_o_output_ui_bypass'   => self::COLUMN_FLAG_VALUE,

            'cfp_v_input_enable'       => self::COLUMN_FLAG_VALUE,
            'cfp_v_input_val'          => self::COLUMN_GROUP_LIST,

            'cfp_v_output_ui_enable'   => self::COLUMN_FLAG_VALUE,
            'cfp_v_output_ui_val'      => self::COLUMN_GROUP_LIST,

            'cfp_c_output_ui_enable' => self::COLUMN_FLAG_VALUE,
            'cfp_c_output_ui_val'    => self::COLUMN_GROUP_LIST,
        ],
        'xf_nf_tickets_ticket_field' => [
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