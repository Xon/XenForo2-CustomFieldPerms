<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\CustomFieldPerms;

use XF\Mvc\Entity\Structure;

trait CustomFieldEntityTrait
{
    public function hasCustomFieldPerm($column): bool
    {
        /** @var Structure $structure */
        $structure = $this->structure();

        return isset($structure->columns['cfp_' . $column . '_enable']);
    }

    /**
     * Insert additional columns into ThreadField
     *
     * @param Structure $structure
     * @return Structure
     */
    public static function getStructure(Structure $structure)
    {
        /** @noinspection PhpUndefinedClassInspection */
        $structure = parent::getStructure($structure);

        if (isset(Globals::$tables[$structure->table]))
        {
            foreach (Globals::$tables[$structure->table] as $column => $details)
            {
                $structure->columns[$column] = ['type' => $details['entity_type'], 'default' => $details['entity_default']];
            }
        }

        return $structure;
    }
}