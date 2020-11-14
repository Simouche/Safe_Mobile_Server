<?php

require_once 'Autoload.php';

abstract class Model
{

    protected static DBConnector $DBConnection;

    public int $recordID;
    public int $id;

    /**
     * Model constructor.
     */
    public function __construct()
    {
        self::init();
    }

    public static function init()
    {
        self::$DBConnection = DBConnector::getInstance();
    }

    protected static function utf($value)
    {
        return utf8_encode($value);
    }

    public static function isInitialized()
    {
        return isset(self::$DBConnection);
    }

    abstract protected static function fetchResult($statement): array;


}

if (!Model::isInitialized())
    Model::init();