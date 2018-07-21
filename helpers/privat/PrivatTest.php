<?php

namespace greeschenko\wallet\helpers\privat;

/**
 * Html helper class.
 */
class PrivatTest
{
    /**
     * Prints XML form array.
     *
     * @param array $array input data
     */
    public static function printXML($array)
    {
        $data = PrivatHelper::array2data($array);
        $xml = PrivatHelper::data2xml('Search', 'Payer', $data);
        echo PrivatHelper::_print_xml($xml);
    }

    /**
     * Prints Payer info.
     *
     * @param string $url   request url
     * @param array  $array input data
     */
    public static function testSearch($url, $array)
    {
        $data = PrivatHelper::array2data($array);
        $requestXML = PrivatHelper::data2xml('Search', 'Payer', $data);
        $responceXML = PrivatHelper::sendRequest($url, $requestXML);
        echo '<pre>';
        echo "<b>XML запит:</b>\n\n";
        echo PrivatHelper::_print_xml($requestXML);
        echo '<hr/>';
        echo "<b>XML відповідь:</b>\n\n";
        echo PrivatHelper::_print_xml($responceXML);
        echo '<hr/>';
        $responce = PrivatHelper::xml2data($responceXML);
        switch ($responce->getAttribute('action')):
            case 'Search':
                echo $responce->find('Message')->getValue(), "\n";
        echo 'billIdentifier: ', $responce->find('PayerInfo')->getAttribute('billIdentifier'), "\n";
        echo "\n";
        foreach ($responce->find('PayerInfo')->getChildren() as $data) {
            echo $data->name, ': ', $data->getValue(), "\n";
        }
        echo "\n";
        break;
        default:
                echo $responce->find('Message')->getValue(), "\n";
        endswitch;
    }

    /**
     * Prints human-readable information.
     *
     * @param string $url      request url
     * @param string $action   request action
     * @param string $dataType request data type
     * @param array  $array    input data
     */
    public static function print_r($url, $action, $dataType, $array)
    {
        $requestXML = PrivatHelper::data2xml($action, $dataType, PrivatHelper::array2data($array));
        $responceXML = PrivatHelper::sendRequest($url, $requestXML);
        print_r(PrivatHelper::data2array(PrivatHelper::xml2data($responceXML)));
    }
}
