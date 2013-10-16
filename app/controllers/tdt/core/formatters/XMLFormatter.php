<?php

namespace tdt\core\formatters;


define("NUMBER_TAG_PREFIX", "_");
define("DEFAULT_ROOTNAME", "data");

/**
 * XML Formatter
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class XMLFormatter implements IFormatter{

    public static function createResponse($dataObj){

        // Create response
        $response = \Response::make(self::getBody($dataObj), 200);

        // Set headers
        $response->header('Access-Control-Allow-Origin', '*');
        $response->header('Content-Type', 'text/xml;charset=UTF-8');

        return $response;
    }

    public static function getBody($dataObj){

        // Rootname equals resource name
        $rootname = $dataObj->definition->resource_name;

        // Build the body
        $body = '<?xml version="1.0" encoding="UTF-8" ?>';
        $body .= self::transformToXML($dataObj->data, $rootname);

        return $body;
    }

    private static function transformToXML($data, $nameobject){

        // Rename empty elements to 'element'
        // if($nameobject == null) $nameobject = 'element';

        // Open tag
        $object = "<$nameobject>";

        if(is_array($data)){

            // Check for attributes
            if(!empty($data['@attributes'])){
                $attributes = $data['@attributes'];

                if(is_array($attributes) && count($attributes) > 0){
                    // Trim last '>'
                    $object = rtrim($object, '>');

                    // Add attributes
                    foreach($attributes as $name => $value){
                        $object .= " $name='".$value."'";
                    }

                    $object .= '>';
                }

                unset($data['@attributes']);
            }

            // Data is an array (translates to elements)
            foreach($data as $key => $value){

                // Check for special keys, then add elements recursively
                if($key === '@value'){
                    $object .= self::getXMLString($value);
                }elseif(is_numeric($key)){
                    $object .= self::transformToXML($value, 'element');
                }else{
                    $object .= self::transformToXML($value, $key);
                }

            }
        }elseif(is_object($data)){
            // Data is object
            foreach($data as $key => $value){
                // Recursively add elements
                $object .= self::transformToXML($value, $key);
            }
        }else{
            // Data is string append it
            $object .= self::getXMLString($data);
        }

        // Close tag
        $object .= "</$nameobject>";

        return $object;
    }

    private static function getXMLString($string){
        // Check for XML syntax to escape
        if(preg_match('/[<>&]+/', $string)){
            $string = '<![CDATA[' . $string . ']]>';
        }

        return $string;
    }

    public static function getDocumentation(){
        return "Prints plain old XML. Watch out for tags starting with an integer: an underscore will be added.";
    }

}