<?php
/**
 * @noinspection PhpMissingParentCallCommonInspection
 */

namespace SV\CustomFieldPerms;

use SV\CustomFieldPerms\Repository\Field as FieldRepo;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use function array_keys;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function installStep1(): void
    {
        $repo = FieldRepo::get();
        $repo->applyCustomFieldSchemaChanges();
    }

    public function installStep2(): void
    {
        $repo = FieldRepo::get();
        $repo->applyPostInstallChanges();
    }

    public function upgrade2020000Step1(): void
    {
        $sm = $this->schemaManager();
        foreach (Globals::$tables as $table => $columns)
        {
            if ($sm->tableExists($table))
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
    }

    public function upgrade2020000Step2(): void
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

    public function upgrade2040000Step1(): void
    {
        $this->installStep1();
    }

    public function upgrade2040000Step2(): void
    {
        $this->installStep2();
    }

    public function uninstallStep1(): void
    {
        $sm = $this->schemaManager();
        foreach (Globals::$tables as $table => $columns)
        {
            if ($sm->tableExists($table))
            {
                $sm->alterTable($table, function (Alter $table) use ($columns) {
                    $table->dropColumns(array_keys($columns));
                });
            }
        }
    }

    public function postInstall(array &$stateChanges): void
    {
        $repo = FieldRepo::get();
        $repo->rebuildCaches();
    }

    public function postUpgrade($previousVersion, array &$stateChanges): void
    {
        $repo = FieldRepo::get();
        $repo->applyCustomFieldSchemaChanges();
        $repo->rebuildCaches();
    }

    public function postRebuild(): void
    {
        $repo = FieldRepo::get();
        $repo->applyCustomFieldSchemaChanges();
        $repo->rebuildCaches();
    }
}
