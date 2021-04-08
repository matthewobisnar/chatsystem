<?php
namespace Core\Helpers;

class Helper 
{
    public const DIE = true;
    public const RETURN = false;
    public const CREATED_BY = 'SYSTEM';
    
    public const ALPHA_NUMERIC = 'ALPHA_NUMERIC';
    public const ALPHABET = 'ALPHABET';
    public const NUMERIC = 'NUMERIC';

    public static function postRequest() {
    
        if (isset($_SERVER['CONTENT_TYPE']) 
        && $_SERVER['CONTENT_TYPE'] == 'application/json') {
            
           return json_decode(
                file_get_contents('php://input'), true
            );
    
        } else {
            return $_POST;
        }

    }

    public static function Randomizer ($length = 20, $CHARACTER = Helper::ALPHA_NUMERIC) 
    {
        $RANDOMIZER = [
            'ALPHA_NUMERIC' => array_merge(range(0,9), range('a', 'z'), range('A','Z')),
            'ALPHABET' => range('a','z'),
            'NUMERIC' => range(0,9),
        ];

        $rand = $RANDOMIZER[$CHARACTER];
        $output=[];
        $temp =[];
        $count = 0;

        while ($count < $length) {
            $output[] = $rand[mt_rand(0, $length)];
            $temp[] = $rand[$count];

            if ($output[$count] == $temp[$count]) {
                $output[$count] = $rand[mt_rand(0, $length)];
            }

            $count++;
        }

        return implode("", $output);
    }

    public static function getRequest()
    {
        return $_GET;
    }
    
    public static function parseConfig($string)
    {
        return json_decode($string, 1);
    }

    public static function className($object)
    {
        return (new \ReflectionClass($object))->getShortName();
    }

    public static function getArrayValue($arr, $key)
    {   
        if (!empty($arr[$key])) {
            if (is_array($arr[$key])) {
                return (array) $arr[$key];
            } else {
                return $arr[$key] ?? null;
            }
        }

        return null;
    }

    public static function response($die = Helper::DIE, $status = true, $error = false, $content = [])
    {
        // header('Content-Type: application/json');
        
        ob_clean();
        ob_flush();

        if ($die) {
            die(json_encode(array('status'=>$status, 'error' => $error, 'content' => $content)));
        }
        
       return is_array($content) ? json_encode($content) : $content;
    }

    public static function fetchRequiredData ($param, $key)
    {
        return isset($param[$key]) && $param[$key] != '' ? $param[$key] : die("Unable to locate (".$key."). parameter."); 
    }

    public static function fetchDataFromArray($param, $key)
    {
        return isset($param[$key]) && $param[$key] != '' ? $param[$key] : null;
    }

    public static function AllowAccessOrigin()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
    }

    public static function generateEnvFile($data)
    {
        $file = ROOT_PATH . "/Core/Ping/ping.txt";

        $fileHandler = fopen($file, "wa+");

        fwrite($fileHandler, $data);
        fclose($fileHandler);
        return true;
    }

    public static function getEnvFile()
    {
       return json_decode(file_get_contents(ROOT_PATH . "/Core/Ping/ping.txt"), true);
    }
}