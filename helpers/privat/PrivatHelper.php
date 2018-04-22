<?php

namespace greeschenko\wallet\helpers\privat;

use SimpleXMLElement;
use SimpleXMLIterator;
use DOMDocument;

/**
 * XML-processor and CURL-sender.
 */
class PrivatHelper
{
    /**
     * Convert data to XML-document.
     *
     * @param string $action   action name
     * @param string $dataType data type
     * @param array  $data
     *
     * @return string XML-document
     */
    public static function data2xml($action, $dataType, $data, $code = false)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Transfer xmlns="http://debt.privatbank.ua/Transfer" interface="Debt"></Transfer>');
        $xml->addAttribute('action', $action);
        $d = $xml->addChild('Data');
        $d->addAttribute('xsi:type', $dataType, 'http://www.w3.org/2001/XMLSchema-instance');
        if (is_numeric($code)) {
            $d->addAttribute('code', $code);
        }
        self::_data2xml($xml->Data, $data);

        return $xml->asXML();
    }

    public static function _print_xml($xml)
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->loadXML($xml);
        $dom->formatOutput = true;

        return htmlspecialchars($dom->saveXML());
    }

    /**
     * Append children elements.
     *
     * @param SimpleXMLElement $parent  parent element
     * @param array            $dataSet data
     */
    protected static function _data2xml(&$parent, $dataSet)
    {
        foreach ($dataSet as $data) {
            $el = $parent->addChild($data->name, $data->getValue());
            foreach ($data->attributes as $key => $val) {
                $el->addAttribute($key, $val);
            }
            self::_data2xml($el, $data->getChildren());
        }
    }

    /**
     * Convert XML-document to data.
     *
     * @param string $xml
     *
     * @return DataElement
     */
    public static function xml2data($xml)
    {
        $xml = new SimpleXMLElement($xml);
        $iterator = new SimpleXMLIterator($xml->Data->asXml());

        return new DataElement('Data', self::_xml2data($iterator), ['action' => (string) $xml->attributes()->action]);
    }

    /**
     * Create array from XML.
     *
     * @param SimpleXMLIterator
     *
     * @return array
     */
    protected static function _xml2data($iterator)
    {
        $dataSet = array();
        for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
            $data = new DataElement($iterator->key());
            foreach ($iterator->current()->attributes() as $name => $value) {
                $data->attributes[(string) $name] = (string) $value;
            }
            if ($iterator->hasChildren()) {
                $data->setValue(self::_xml2data($iterator->getChildren()));
            } else {
                $data->setValue((string) $iterator->current());
            }
            $dataSet[] = $data;
        }

        return $dataSet;
    }

    /**
     * Send XML-document.
     *
     * @param string $url  url
     * @param string $data XML-document
     *
     * @return string xml-responce
     */
    public static function sendRequest($url, $data)
    {
        $headers = array(
            'Accept:text/xml',
            'Content-Type:text/xml; charset=utf-8',
            'Connection:keep-alive',
            'Content-Length:'.mb_strlen($data, 'utf-8'),
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $responce = curl_exec($ch);
        curl_close($ch);

        return $responce;
    }

    /**
     * Converts raw php-array to Data Elements array.
     *
     * @param array $array
     *
     * @return array
     */
    public static function array2data(array $array)
    {
        $data = array();
        foreach ($array as $item) {
            if (!array_key_exists('name', $item)) {
                $item['name'] = 'Undefined element';
            }
            if (!array_key_exists('value', $item)) {
                $item['value'] = '';
            }
            if (!array_key_exists('attributes', $item)) {
                $item['attributes'] = array();
            }
            if (is_array($item['value'])) {
                $value = self::array2data($item['value']);
            } else {
                $value = $item['value'];
            }
            $data[] = new DataElement($item['name'], $value, $item['attributes']);
        }

        return $data;
    }

    /**
     * Converts Data Elements array to raw php-array.
     *
     * @param array $array
     *
     * @return array
     */
    public static function data2array($data)
    {
        if (!is_array($data)) {
            $data = array($data);
        }
        $array = array();
        foreach ($data as $item) {
            if (!$item instanceof DataElement) {
                continue;
            }
            $value = $item->getValue();
            if (empty($value)) {
                $value = self::data2array($item->getChildren());
            }
            $array[] = ['name' => $item->name, 'attributes' => $item->attributes, 'value' => $value];
        }

        return $array;
    }
}
