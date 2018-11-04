<?php

namespace Somar\NZBN\Model;

use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

/**
 * Base Class for EditableOption Fields such as the ones used in
 * dropdown fields and in radio check box groups
 *
 * @method EditableMultipleOptionField Parent()
 * @package userforms
 */
class EditableOption extends DataObject
{
    /**
     * @var array
     */
    private static $db = [
        'FieldKey' => 'Varchar(255)',
        'DataKey' => 'Varchar(255)',
        'Sort' => 'Int',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'NZBNLookupField' => EditableLookupField::class
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'FieldKey',
        'DataKey'
    ];

    /**
     * @var string
     */
    private static $table_name = 'NZBNEditableOption';

    /**
     * @var string
     */
    private static $default_sort = 'Sort';

    /**
     * @return void
     */
    protected function onBeforeWrite()
    {
        if (!$this->Sort) {
            $this->Sort = EditableOption::get()->max('Sort') + 1;
        }

        parent::onBeforeWrite();
    }
}
