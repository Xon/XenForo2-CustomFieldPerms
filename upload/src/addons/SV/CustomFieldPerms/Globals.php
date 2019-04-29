<?php

namespace SV\CustomFieldPerms;

use XF\Mvc\Entity\Entity;

class Globals
{
    public static $repos = [
        'XF:UserField'   => 'XF',
        'XF:ThreadField' => 'XF',
        'XFMG:MediaField'  => 'XFMG',
    ];

    // note; CustomFieldFilterTrait expected that cfp_v_input_enable is always in each entity
    public static $tables = [
        'xf_user_field'     => [
            'cfp_v_input_enable'     => ['type' => 'tinyint', 'default' => 0, 'entity_type' => Entity::UINT, 'entity_default' => 0, 'field_type' => 'uint'],
            'cfp_v_input_val'        => ['type' => 'blob', 'default' => null, 'nullable' => true, 'entity_type' => Entity::SERIALIZED, 'entity_default' => '', 'field_type' => 'array'],

            'cfp_v_output_ui_enable' => ['type' => 'tinyint', 'default' => 0, 'entity_type' => Entity::UINT, 'entity_default' => 0, 'field_type' => 'uint'],
            'cfp_v_output_ui_val'    => ['type' => 'blob', 'default' => null, 'nullable' => true, 'entity_type' => Entity::SERIALIZED, 'entity_default' => '', 'field_type' => 'array'],

            'cfp_v_output_pp_enable' => ['type' => 'tinyint', 'default' => 0, 'entity_type' => Entity::UINT, 'entity_default' => 0, 'field_type' => 'uint'],
            'cfp_v_output_pp_val'    => ['type' => 'blob', 'default' => null, 'nullable' => true, 'entity_type' => Entity::SERIALIZED, 'entity_default' => '', 'field_type' => 'array'],
        ],
        'xf_thread_field'   => [
            'cfp_v_input_enable'       => ['type' => 'tinyint', 'default' => 0, 'entity_type' => Entity::UINT, 'entity_default' => 0, 'field_type' => 'uint'],
            'cfp_v_input_val'          => ['type' => 'blob', 'default' => null, 'nullable' => true, 'entity_type' => Entity::SERIALIZED, 'entity_default' => '', 'field_type' => 'array'],

            'cfp_v_output_ui_enable'   => ['type' => 'tinyint', 'default' => 0, 'entity_type' => Entity::UINT, 'entity_default' => 0, 'field_type' => 'uint'],
            'cfp_v_output_ui_val'      => ['type' => 'blob', 'default' => null, 'nullable' => true, 'entity_type' => Entity::SERIALIZED, 'entity_default' => '', 'field_type' => 'array'],

            'cfp_c_output_ui_enable' => ['type' => 'tinyint', 'default' => 0, 'entity_type' => Entity::UINT, 'entity_default' => 0, 'field_type' => 'uint'],
            'cfp_c_output_ui_val'    => ['type' => 'blob', 'default' => null, 'nullable' => true, 'entity_type' => Entity::SERIALIZED, 'entity_default' => '', 'field_type' => 'array'],
        ],
        'xf_mg_media_field' => [
            'cfp_v_input_enable'       => ['type' => 'tinyint', 'default' => 0, 'entity_type' => Entity::UINT, 'entity_default' => 0, 'field_type' => 'uint'],
            'cfp_v_input_val'          => ['type' => 'blob', 'default' => null, 'nullable' => true, 'entity_type' => Entity::SERIALIZED, 'entity_default' => '', 'field_type' => 'array'],

            'cfp_v_output_ui_enable'   => ['type' => 'tinyint', 'default' => 0, 'entity_type' => Entity::UINT, 'entity_default' => 0, 'field_type' => 'uint'],
            'cfp_v_output_ui_val'      => ['type' => 'blob', 'default' => null, 'nullable' => true, 'entity_type' => Entity::SERIALIZED, 'entity_default' => '', 'field_type' => 'array'],

            'cfp_c_output_ui_enable' => ['type' => 'tinyint', 'default' => 0, 'entity_type' => Entity::UINT, 'entity_default' => 0, 'field_type' => 'uint'],
            'cfp_c_output_ui_val'    => ['type' => 'blob', 'default' => null, 'nullable' => true, 'entity_type' => Entity::SERIALIZED, 'entity_default' => '', 'field_type' => 'array'],
        ],
    ];

    private function __construct() { }
}