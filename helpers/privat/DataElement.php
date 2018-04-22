<?php

namespace greeschenko\wallet\helpers\privat;

/**
 * Data access class.
 */
class DataElement
{
    /**
     * @var string data name
     */
    public $name;

    /**
     * @var array data attributes
     */
    public $attributes;

    /**
     * @var array|string data values
     *                   String when data element has no children or array of the children data
     */
    protected $value = '';

    /**
     * Constructor.
     *
     * @var string       data name
     * @var array|string $value data value
     * @var array        $attributes data attributes
     */
    public function __construct($name, $value = '', array $attributes = array())
    {
        $this->name = $name;
        $this->attributes = $attributes;
        $this->value = $value;
    }

    /**
     * Set data value.
     *
     * @param array|string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get data value when data has no children.
     *
     * @return string
     */
    public function getValue()
    {
        return is_string($this->value) ? $this->value : '';
    }

    /**
     * Get data value when data has children.
     *
     * @return array children data list
     */
    public function getChildren()
    {
        return is_array($this->value) ? $this->value : [];
    }

    /**
     * Get data attribute.
     *
     * @param string attribute name
     *
     * @return string
     */
    public function getAttribute($key)
    {
        return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : null;
    }

    /**
     * Get children data by name.
     *
     * @param string data name
     *
     * @return DataElement
     */
    public function find($elementName, $valueName = null)
    {
        foreach ($this->getChildren() as $data) {
            if ($data->name === $elementName && ($valueName === null || $valueName == $data->getAttribute('name'))) {
                return $data;
            }
        }

        return new $this("Undefined element {$elementName} or value {$valueName}");
    }
}
