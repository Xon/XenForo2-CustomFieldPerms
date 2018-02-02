<?php


namespace SV\CustomFieldPerms;


use XF\Template\Templater;

class Listener
{
    public static function customFieldsEdit(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyUsergroupCustomFieldPermissionFilters($arguments, 'sedo_perms_input');
    }

    public static function customFieldsView(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $permType = isset($arguments['group']) && $arguments['group'] === 'about' ? 'sedo_perms_output_ui' : 'sedo_perms_output_pp';
        $repo->applyUsergroupCustomFieldPermissionFilters($arguments, $permType);
    }

    public static function customFieldsViewValues(Templater $templater, &$type, &$template, &$name, array &$arguments, array &$globalVars)
    {
        /** @var \SV\CustomFieldPerms\Repository\Field $repo */
        $repo = \XF::repository('SV\CustomFieldPerms:Field');
        $repo->applyUsergroupCustomFieldPermissionFilters($arguments, 'sedo_perms_output_ui');
    }
}
