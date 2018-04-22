<?php

namespace greeschenko\wallet\helpers\privat;

/**
 * Actions class.
 */
class PrivatWorker
{
    /**
     * Process input XML document;.
     *
     * @param string $input
     *
     * @return string XML document
     */
    public static function process()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'):
            $input = file_get_contents('php://input');

        return PrivatHelper::xml2data($input); else:
            return self::getError('Invalid request method. Only POST request allowed.', 400);
        endif;
    }

    /**
     * Creates XML error message.
     *
     * @param string $message message text
     * @param string $code    error code
     *
     * @return scting XML document
     */
    public static function getError($message, $code)
    {
        $data = array(
                ['name' => 'Message', 'value' => $message],
        );

        return PrivatHelper::data2xml(self::$action, 'ErrorInfo', PrivatHelper::array2data($data), $code);
    }
}
