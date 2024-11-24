<?php
/**
 * @noinspection PhpMultipleClassDeclarationsInspection
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\CustomFieldPerms;

use XF\Mvc\Entity\Structure;

trait CustomFieldEntityTrait
{
    public function hasCustomFieldPerm(string $column, bool $raw = false): bool
    {
        $structure = $this->structure();

        if ($raw)
        {
            return isset($structure->columns['cfp_' . $column]);
        }

        return isset($structure->columns['cfp_' . $column . '_enable']) && isset($structure->columns['cfp_' . $column . '_val']);
    }

    /**
     * Insert additional columns into ThreadField
     *
     * @param Structure $structure
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $entityClassName = \XF::stringToClass($structure->shortName, '%s\Entity\%s');
        $entity = Globals::$entities[$entityClassName] ?? null;
        if ($entity !== null)
        {
            foreach ($entity as $column => $details)
            {
                $structure->columns[$column] = $details['entity'];
            }
        }

        return $structure;
    }
}