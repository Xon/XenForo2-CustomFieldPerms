<?php
/**
 * @noinspection PhpUnusedParameterInspection
 */

namespace SV\CustomFieldPerms;


use XF\Template\Templater;

class Listener
{
    public static function customFieldsEdit(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        if (\XF::app() instanceof \XF\Admin\App && !($templater instanceof \XF\Mail\Templater))
        {
            return;
        }
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyUsergroupCustomFieldPermissionFilters($arguments, 'input');
    }

    public static function customFieldsView(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        if (\XF::app() instanceof \XF\Admin\App && !($templater instanceof \XF\Mail\Templater))
        {
            return;
        }
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $permType = isset($arguments['group']) && $arguments['group'] === 'about' ? 'output_ui' : 'output_pp';
        $repo->applyUsergroupCustomFieldPermissionFilters($arguments, $permType);
    }

    public static function customFieldsViewValues(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        if (\XF::app() instanceof \XF\Admin\App && !($templater instanceof \XF\Mail\Templater))
        {
            return;
        }
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyUsergroupCustomFieldPermissionFilters($arguments, 'output_ui');
    }

    public static function addonPostRebuild(\XF\AddOn\AddOn $addOn, \XF\Entity\AddOn $installedAddOn, array $json)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyCustomFieldSchemaChanges($addOn->getAddOnId());
        $repo->rebuildCaches($addOn->getAddOnId());
    }

    public static function addonPostInstall(\XF\AddOn\AddOn $addOn, \XF\Entity\AddOn $installedAddOn, array $json, array &$stateChanges)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyCustomFieldSchemaChanges($addOn->getAddOnId());
        $repo->applyPostInstallChanges($addOn->getAddOnId());
        $repo->rebuildCaches($addOn->getAddOnId());
    }
}
