<?php
/**
 * @noinspection PhpUnusedParameterInspection
 */

namespace SV\CustomFieldPerms\Listener;

use SV\CustomFieldPerms\Repository\Field as FieldRepo;
use XF\AddOn\AddOn;
use XF\Entity\AddOn as AddOnEntity;

abstract class AddOns
{
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
