<?php

namespace tdt\core;

/**
 * Content negotiator
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class ContentNegotiator{

    /**
     * Map MIME-types on formatters for Accept-header
     */
    public static $mime_types_map = array(
        'text/csv' => 'CSV',
        'application/json' => 'JSON',
        'application/xml' => 'XML',
        'application/xslt+xml' => 'XML',
    );

    /**
     * Format using requested formatter (via extension, Accept-header or default)
     */
    public static function getResponse($data, $extension = null){

        // Extension has priority over Accept-header
        if(!$extension){

            // Check Accept-header
            $accept_header = \Request::header('Accept');

            $mimes = explode(',', $accept_header);
            foreach($mimes as $mime){
                if(!empty(ContentNegotiator::$mime_types_map[$mime])){
                    // Matched mime type
                    $extension = ContentNegotiator::$mime_types_map[$mime];
                    break;
                }
            }

            // Still nothing? Use default formatter
            if(!$extension){
                // TODO: get default formatter from config
                $extension = 'HTML';
            }

        }

        // Safety first
        $extension = strtoupper($extension);

        // Formatter class
        $formatter_class = '\\tdt\\core\\formatters\\'.$extension.'Formatter';

        if(!class_exists($formatter_class)){
            \App::abort(400, "Formatter $extension doesn't exist.");
        }

        // Return formatted response
        return $formatter_class::createResponse($data);
    }

}