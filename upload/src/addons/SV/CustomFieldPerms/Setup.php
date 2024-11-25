<?php
/**
 * @noinspection PhpMissingParentCallCommonInspection
 */

namespace SV\CustomFieldPerms;

use SV\CustomFieldPerms\Repository\Field as FieldRepo;
use SV\StandardLib\Helper;
use SV\StandardLib\InstallerHelper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Util\Json;
use function array_keys;

class Setup extends AbstractSetup
{
    use InstallerHelper;
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
        foreach (FieldRepo::get()->getExtendedEntities() as $entity => $columns)
        {
            $entityStructure = Helper::getEntityStructure($entity);
            if ($entityStructure !== null && $sm->tableExists($entityStructure->table))
            {
                $sm->alterTable($entityStructure->table, function (Alter $table) {
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
        $repo = FieldRepo::get();
        $repo->applyCustomFieldSchemaChanges();
    }

    public function upgrade2040000Step2(): void
    {
        $repo = FieldRepo::get();
        $repo->applyPostInstallChanges();
    }

    public function upgrade2080000Step1(): void
    {
        $this->renamePhrases([
            'sedo_cuf_perms_enable_perms' => 'svCustomFieldPerms_permissions_enable_label',
            'sedo_cuf_perms_input' => 'svCustomFieldPerms_input_permissions',
            'sedo_cuf_perms_input_desc' => 'svCustomFieldPerms_input_permissions_explain',
            'sedo_cuf_perms_c_output' => 'svCustomFieldPerms_output_permissions_content',
            'sedo_cuf_perms_c_output_desc' => 'svCustomFieldPerms_output_permissions_content_explain',
            'sedo_cuf_perms_output' => 'svCustomFieldPerms_output_permissions_viewer',
            'sedo_cuf_perms_output_desc' => 'svCustomFieldPerms_output_permissions_viewer_explain',
            'sedo_cuf_perms_output_ui' => 'svCustomFieldPerms_output_permissions_viewer_user_info',
            'sedo_cuf_perms_output_ui_desc' => 'svCustomFieldPerms_output_permissions_viewer_user_info_explain',
            'sedo_cuf_perms_output_pp' => 'svCustomFieldPerms_output_permissions_viewer_profile',
            'sedo_cuf_perms_output_pp_desc' => 'svCustomFieldPerms_output_permissions_viewer_profile_explain',

            // fix typo
            'svCustomFieldPerms_ouput_content_owner_bypass_explain' => 'svCustomFieldPerms_output_content_owner_bypass_explain',
        ]);
    }

    public function upgrade2080000Step2(): void
    {
        // migrate serialize encoded values to a list of integers
        // also patches ['all'] => [-1] to use native XF usergroup selection widget
        $sm = $this->schemaManager();
        $db = $this->db();
        $db->beginTransaction();

        foreach (FieldRepo::get()->getExtendedEntities() as $entity => $columns)
        {
            $entityStructure = Helper::getEntityStructure($entity);
            if ($entityStructure !== null && $sm->tableExists($entityStructure->table))
            {
                $table = $entityStructure->table;
                $primaryId = $entityStructure->primaryKey;
                if (!is_string($primaryId))
                {
                    if (\XF::$developmentMode)
                    {
                        throw new \LogicException("Only simple primaryKeys are supported for $entity");
                    }
                    continue;
                }
                $updates = [];

                $rows = $db->fetchAll("select * from `$table`");
                if (count($rows) === 0)
                {
                    continue;
                }
                foreach ($rows as $row)
                {
                    $primaryKey = $row[$primaryId] ?? null;
                    if ($primaryKey === null)
                    {
                        if (\XF::$developmentMode)
                        {
                            throw new \LogicException("Unknown primaryKey $primaryId for $entity");
                        }
                        continue;
                    }

                    foreach ($columns as $column => $details)
                    {
                        if (!($details['isGroupList'] ?? false))
                        {
                            continue;
                        }

                        $oldValue = $row[$column] ?? null;
                        if (!is_string($oldValue))
                        {
                            continue;
                        }
                        // check if value has already been migrated
                        if (stripos($oldValue, ',') !== false)
                        {
                            continue;
                        }
                        $newValue = Json::decodeJsonOrSerialized($oldValue);
                        if ($newValue === null || $newValue === '')
                        {
                            $updates[$column] = null;
                        }
                        else if (is_array($newValue))
                        {
                            if ($newValue === ['all'])
                            {
                                $newValue = [-1];
                            }
                            $newValue = array_filter(array_map('\intval', $newValue));

                            $updates[$column] = implode(',', $newValue);
                        }
                    }

                    if (count($updates) !== 0)
                    {
                        $db->update($table, $updates, "`$primaryId` = ?", $primaryKey);
                    }
                }
            }
        }

        $db->commit();

        FieldRepo::get()->rebuildCaches();
    }

    public function uninstallStep1(): void
    {
        $sm = $this->schemaManager();
        foreach (FieldRepo::get()->getExtendedEntities() as $entity => $columns)
        {
            $entityStructure = Helper::getEntityStructure($entity);
            if ($entityStructure !== null && $sm->tableExists($entityStructure->table))
            {
                $sm->alterTable($entityStructure->table, function (Alter $table) use ($columns) {
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
