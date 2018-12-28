<?php


namespace SV\CustomFieldPerms;


use XF\Template\Templater;

class Listener
{
    public static function customFieldsEdit(/** @noinspection PhpUnusedParameterInspection */
        Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyUsergroupCustomFieldPermissionFilters($arguments, 'input');
    }

    public static function customFieldsView(/** @noinspection PhpUnusedParameterInspection */
        Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $permType = isset($arguments['group']) && $arguments['group'] === 'about' ? 'output_ui' : 'output_pp';
        $repo->applyUsergroupCustomFieldPermissionFilters($arguments, $permType);
    }

    public static function customFieldsViewValues(/** @noinspection PhpUnusedParameterInspection */
        Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyUsergroupCustomFieldPermissionFilters($arguments, 'output_ui');
    }

    public static function addonPostRebuild(/** @noinspection PhpUnusedParameterInspection */
        \XF\AddOn\AddOn $addOn, \XF\Entity\AddOn $installedAddOn, array $json)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyCustomFieldSchemaChanges($addOn->getAddOnId());
        $repo->rebuildCaches($addOn->getAddOnId());
    }

    public static function addonPostInstall(/** @noinspection PhpUnusedParameterInspection */
        \XF\AddOn\AddOn $addOn, \XF\Entity\AddOn $installedAddOn, array $json, array &$stateChanges)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyCustomFieldSchemaChanges($addOn->getAddOnId());
        $repo->rebuildCaches($addOn->getAddOnId());
    }
}
