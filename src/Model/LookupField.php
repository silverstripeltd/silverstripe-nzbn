<?php

namespace Somar\NZBN\Model;

use ArrayAccess;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\Map;
use SilverStripe\ORM\SS_List;
use SilverStripe\View\Requirements;

class LookupField extends TextField
{
    use Configurable;

    /**
     * @config
     * @var string
     */
    private static $button_text = 'Lookup';

    /**
     * Associative or numeric array of all dropdown items,
     * with array key as the submitted field value, and the array value as a
     * natural language description shown in the interface element.
     *
     * @var array
     */
    protected $source;

    /**
     * Returns an input field.
     *
     * @param string $name
     * @param null|string $title
     * @param array $source
     * @param string $value
     * @param null|int $maxLength Max characters to allow for this field. If this value is stored
     * against a DB field with a fixed size it's recommended to set an appropriate max length
     * matching this size.
     * @param null|Form $form
     */
    public function __construct($name, $title = null, $source = null, $value = '', $maxLength = null, $form = null)
    {
        Requirements::javascript('somar/silverstripe-nzbn:client/dist/js/nzbn.js');
        Requirements::css('somar/silverstripe-nzbn:client/dist/css/nzbn.css');

        if ($source) {
            $this->setSource($source);
        }

        if ($maxLength) {
            $this->setMaxLength($maxLength);
        }

        if ($form) {
            $this->setForm($form);
        }

        parent::__construct($name, $title, $value);
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        $parent = parent::getAttributes();
        $parent['class'] .= ' text';

        $maxLength = $this->getMaxLength();

        $attributes = array();

        if ($maxLength) {
            $attributes['maxLength'] = $maxLength;
            $attributes['size'] = min($maxLength, 30);
        }

        $source = $this->getSource();
        $attributes['data-nzbn'] = json_encode($source);

        return array_merge(
            $parent,
            $attributes
        );
    }

    /**
     * Gets the source array not including any empty default values.
     *
     * @return array|ArrayAccess
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the source for this list
     *
     * @param mixed $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $this->getListMap($source);
        return $this;
    }

    /**
     * Given a list of values, extract the associative map of id => title
     *
     * @param mixed $source
     * @return array Associative array of ids and titles
     */
    protected function getListMap($source)
    {
        // Extract source as an array
        if ($source instanceof SS_List) {
            $source = $source->map();
        }

        if ($source instanceof Map) {
            $source = $source->toArray();
        }

        if (!is_array($source) && !($source instanceof ArrayAccess)) {
            user_error('$source passed in as invalid type', E_USER_ERROR);
        }

        return $source;
    }

    /**
     * @return string
     */
    protected function getButtonText()
    {
        return self::config()->get('button_text');
    }
}
