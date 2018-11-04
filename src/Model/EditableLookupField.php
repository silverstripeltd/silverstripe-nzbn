<?php

namespace Somar\NZBN\Model;

use SilverStripe\Forms\TextField;
use SilverStripe\UserForms\Model\EditableFormField\EditableTextField;
use SilverStripe\UserForms\Model\EditableFormField\EditableDropdown;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NumericField;
use SilverStripe\UserForms\Model\EditableFormField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Versioned\Versioned;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\Map;

class EditableLookupField extends EditableTextField
{
    /**
     * @var array
     */
    private static $has_many = [
        'NZBNOptions' => EditableOption::class,
    ];

    /**
     * @var array
     */
    private static $owns = [
        'NZBNOptions',
    ];

    /**
     * @var string
     */
    private static $singular_name = 'NZBN Lookup Field';

    /**
     * @var string
     */
    private static $plural_name = 'NZBN Lookup Fields';

    /**
     * @var string
     */
    private static $table_name = 'NZBNEditableLookupField';

    /**
     * @var bool
     */
    private static $has_placeholder = true;

    /**
     * @var array
     */
    private static $dataFieldOptions = [
        'AustralianBusinessNumber' => 'Australian Business Number',
        'AustralianCompanyNumber' => 'Australian Company Number',
        'CompanyStatus' => 'Company Status',
        'GSTEffectiveDate' => 'GST Effective Date',
        'GSTStatus' => 'GST Status',
        'HeadOfficeAddress_City' => 'Head Office Address City',
        'HeadOfficeAddress_Country' => 'Head Office Address Country',
        'HeadOfficeAddress_Line1' => 'Head Office Address Line1',
        'HeadOfficeAddress_Line2' => 'Head Office Address Line2',
        'HeadOfficeAddress_PostalCode' => 'Head Office Address Postal Code',
        'LegalName' => 'Legal Name',
        'RegisteredAddress_City' => 'Registered Address City',
        'RegisteredAddress_Country' => 'Registered Address Country',
        'RegisteredAddress_Line1' => 'Registered Address Line1',
        'RegisteredAddress_Line2' => 'Registered Address Line2',
        'RegisteredAddress_PostalCode' => 'Registered Address Postal Code',
        'TradingName' => 'Trading Name',
        'TypeOfCompany' => 'Type Of Company'
    ];

    /**
     * @return \SilverStripe\Forms\FieldList
     */
    public function getCMSFields()
    {
        $this->beforeUpdateCMSFields(function ($fields) {
            $fields->addFieldsToTab(
                'Root.Main',
                [
                    NumericField::create(
                        'Rows',
                        _t(__CLASS__ . '.NUMBERROWS', 'Number of rows')
                    )->setDescription(_t(
                        __CLASS__ . '.NUMBERROWS_DESCRIPTION',
                        'Fields with more than one row will be generated as a textarea'
                    )),
                    DropdownField::create(
                        'Autocomplete',
                        _t(__CLASS__ . '.AUTOCOMPLETE', 'Autocomplete'),
                        $this->config()->get('autocomplete_options')
                    )->setDescription(_t(
                        __CLASS__ . '.AUTOCOMPLETE_DESCRIPTION',
                        'Supported browsers will attempt to populate this field automatically with the users information, use to set the value populated'
                    ))
                ]
            );

            $editableColumns = new GridFieldEditableColumns();
            $editableColumns->setDisplayFields([
                'FieldKey' => [
                    'title' => 'Form Field',
                    'callback' => function ($record, $column, $grid) {
                        $fields = $this->Parent()->Fields()->filter('ClassName', [EditableTextField::class, EditableDropdown::class]);
                        return DropdownField::create($column, $column, $fields->map('Name', 'Title'))->setEmptyString('Select One');
                    }
                ],
                'DataKey' => [
                    'title' => 'NZBN Field',
                    'callback' => function ($record, $column, $grid) {
                        return DropdownField::create($column, $column, self::$dataFieldOptions)->setEmptyString('Select One');
                    }
                ]
            ]);

            $optionsConfig = GridFieldConfig::create()
                ->addComponents([
                    new GridFieldButtonRow(),
                    new GridFieldToolbarHeader(),
                    new GridFieldTitleHeader(),
                    $editableColumns,
                    new GridFieldDeleteAction(),
                    new GridFieldAddNewInlineButton(),
                    new GridFieldOrderableRows('Sort')
                ]);

            $optionsGrid = GridField::create(
                'Fields',
                'Fields',
                $this->NZBNOptions(),
                $optionsConfig
            );

            $fields->insertAfter(Tab::create('Fields', 'Fields'), 'Main');
            $fields->addFieldToTab('Root.Fields', $optionsGrid);
        });

        return parent::getCMSFields();
    }

    /**
     * @return LookupField
     */
    public function getFormField()
    {
        $field = LookupField::create($this->Name, $this->Title ?: false, $this->getNZBNOptionsMap(), $this->Default)
            ->setFieldHolderTemplate(EditableFormField::class . '_holder')
            ->setTemplate('LookupField');

        $this->doUpdateFormField($field);

        return $field;
    }

    /**
     * Gets map of field options suitable for use in a form
     *
     * @return array
     */
    protected function getNZBNOptionsMap()
    {
        $optionSet = $this->NZBNOptions();
        $optionMap = $optionSet->map('FieldKey', 'DataKey');

        if ($optionMap instanceof Map) {
            return $optionMap->toArray();
        }

        return $optionMap;
    }
}
