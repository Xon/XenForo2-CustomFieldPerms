<?php

namespace SV\CustomFieldPerms;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Mvc\Entity\Entity;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public static $repos = [
        'XF:UserField'   => 'XF',
        'XF:ThreadField' => 'XF',
        'XFMG:MediaField'  => 'XFMG',
    ];

    // note; CustomFieldFilterTrait expected that cfp_v_input_enable is always in each entity
    public static $tables1 = [
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

    public function installStep1()
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyCustomFieldSchemaChanges();
    }

    public function upgrade2020000Step1()
    {
        $sm = $this->schemaManager();
        foreach (self::$tables1 as $table => $columns)
        {
            $sm->alterTable($table, function (Alter $table) {
                if ($table->getColumnDefinition('sedo_perms_input_enable'))
                {
                    $table->renameColumn('sedo_perms_input_enable', 'cfp_v_input_enable');
                    $table->renameColumn('sedo_perms_input_val', 'cfp_v_input_val');
                }

                if ($table->getColumnDefinition('sedo_perms_output_ui_enable'))
                {
                    $table->renameColumn('sedo_perms_output_ui_enable', 'cfp_v_output_ui_enable');
                    $table->renameColumn('sedo_perms_output_ui_val', 'cfp_v_output_ui_val');
                }

                if ($table->getColumnDefinition('sedo_perms_output_pp_enable'))
                {
                    $table->renameColumn('sedo_perms_output_pp_enable', 'cfp_v_output_pp_enable');
                    $table->renameColumn('sedo_perms_output_pp_val', 'cfp_v_output_pp_val');
                }
            });
        }
    }

    public function upgrade2020000Step2()
    {
        $sm = $this->schemaManager();
        $sm->alterTable(
            'xf_thread_field', function (Alter $table) {
            $table->dropColumns(['cfp_v_output_pp_enable', 'cfp_v_output_pp_val']);
        });
        if ($sm->tableExists('xf_mg_media_field'))
        {
            $sm->alterTable(
                'xf_mg_media_field', function (Alter $table) {
                $table->dropColumns(['cfp_v_output_pp_enable', 'cfp_v_output_pp_val']);
            });
        }
    }

    public function upgrade2020000Step3()
    {
        $this->installStep1();
    }

    public function uninstallStep1()
    {
        $sm = $this->schemaManager();
        foreach (self::$tables1 as $table => $columns)
        {
            if ($sm->tableExists($table))
            {
                $sm->alterTable(
                    $table, function (Alter $table) use ($columns) {
                    $table->dropColumns(array_keys($columns));
                });
            }
        }
    }

    public function postInstall(array &$stateChanges)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->rebuildCaches();
    }

    public function postUpgrade($previousVersion, array &$stateChanges)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->rebuildCaches();
    }
}
