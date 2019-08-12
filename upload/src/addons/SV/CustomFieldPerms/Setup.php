<?php

namespace SV\CustomFieldPerms;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;

class Setup extends AbstractSetup
{
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;


    public function installStep1()
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyCustomFieldSchemaChanges();
    }

    public function upgrade2020000Step1()
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

    public function upgrade2030200Step1()
    {
        $this->installStep1();
    }

    public function upgrade2030200Step2()
    {
        if ($this->schemaManager()->tableExists('xf_nf_tickets_ticket_field'))
        {
            $fields = $this->app()->finder('NF\Tickets:TicketField')->fetch();

            if ($fields->count())
            {
                /** @var \NF\Tickets\Entity\TicketField $field */
                foreach ($fields AS $field)
                {
                    $updates = [];
                    if ($field->usable_user_group_ids[0] === '-1')
                    {
                        $updates = array_merge($updates, [
                            'cfp_v_input_enable' => 0,
                            'cfp_v_input_val' => []
                        ]);
                    }
                    else
                    {
                        $updates = array_merge($updates, [
                            'cfp_v_input_enable' => 1,
                            'cfp_v_input_val' => $field->usable_user_group_ids_
                        ]);
                    }

                    if ($field->viewable_user_group_ids[0] === '-1')
                    {
                        $updates = array_merge($updates, [
                            'cfp_v_output_ui_enable' => 0,
                            'cfp_v_output_ui_val' => []
                        ]);
                    }
                    else
                    {
                        $updates = array_merge($updates, [
                            'cfp_v_output_ui_enable' => 1,
                            'cfp_v_output_ui_val' => $field->viewable_user_group_ids_
                        ]);
                    }

                    if ($field->viewable_owner_user_group_ids[0] === '-1')
                    {
                        $updates = array_merge($updates, [
                            'cfp_c_output_ui_enable' => 0,
                            'cfp_c_output_ui_val' => []
                        ]);
                    }
                    else
                    {
                        $updates = array_merge($updates, [
                            'cfp_c_output_ui_enable' => 1,
                            'cfp_c_output_ui_val' => $field->viewable_owner_user_group_ids_
                        ]);
                    }

                    $field->fastUpdate($updates);
                }
            }
        }
    }

    public function uninstallStep1()
    {
        $sm = $this->schemaManager();
        foreach (Globals::$tables as $table => $columns)
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
