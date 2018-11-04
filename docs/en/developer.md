# Developer Documentation

A NZBN lookup field can be added to a form by a developer in PHP.

### Somar\NZBN\LookupField
The LookupField can be added like any other form field to a form. It takes a mandatory third argument which is a map of form fields to pre-populate.

```php
public function __construct($controller, $name)
{
    $fieldMap = [
        'TradingName' => 'TradingName',
        'LegalName' => 'LegalName',
        'TypeOfCompany' => 'TypeOfCompany',
        'CustomField' => 'TypeOfCompany'
    ];

    $fields = new FieldList(
        LookupField::create('NZBN', 'NZBN', $fieldMap),
        TextField::create('TradingName'),
        TextField::create('LegalName'),
        TextField::create('TypeOfCompany'),
        TextField::create('CustomField')
    );

    $actions = new FieldList(
        FormAction::create('submit', 'Submit')
    );

    parent::__construct($controller, $name, $fields, $actions);
}
```

### Pre-populating fields
Text or Dropdown fields can be pre-populated. It is up to the developer to make sure that a field with the correct field name is included in the form.

### Dropdown options
It is up to the developer to make sure that the options are added to the form for the dropdown to populate.
