<?php
/**
 * @noinspection PhpUnusedParameterInspection
 */

namespace SV\CustomFieldPerms;

use SV\CustomFieldPerms\Repository\Field as FieldRepo;
use XF\AddOn\AddOn;
use XF\Entity\AddOn as AddOnEntity;
use XF\Template\Templater;

abstract class Listener
{
    public static function customFieldsEdit(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars): void
    {
        $repo = FieldRepo::get();
        if ($repo->applyCustomFieldFilters($templater))
        {
            $repo->applyUsergroupCustomFieldPermissionFilters($arguments, 'input');
        }
    }

    public static function customFieldsView(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars): void
    {
        $repo = FieldRepo::get();
        if ($repo->applyCustomFieldFilters($templater))
        {
            $permType = isset($arguments['group']) && $arguments['group'] === 'about' ? 'output_ui' : 'output_pp';
            $repo->applyUsergroupCustomFieldPermissionFilters($arguments, $permType);
        }
    }

    public static function customFieldsViewValues(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        $repo = FieldRepo::get();
        if ($repo->applyCustomFieldFilters($templater))
        {
            $repo->applyUsergroupCustomFieldPermissionFilters($arguments, 'output_ui');
        }
    }

    public static function addonPostRebuild(AddOn $addOn, AddOnEntity $installedAddOn, array $json): void
    {
        $repo = FieldRepo::get();
        $repo->applyCustomFieldSchemaChanges($addOn->getAddOnId());
        $repo->rebuildCaches($addOn->getAddOnId());
    }

    public static function addonPostInstall(AddOn $addOn, AddOnEntity $installedAddOn, array $json, array &$stateChanges)
    {
        $repo = FieldRepo::get();
        $repo->applyCustomFieldSchemaChanges($addOn->getAddOnId());
        $repo->applyPostInstallChanges($addOn->getAddOnId());
        $repo->rebuildCaches($addOn->getAddOnId());
    }
}
