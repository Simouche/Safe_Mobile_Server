<?php
require_once "Configs.php";

class DBConnector
{
    private static PDO $DBConnection;
    private static DBConnector $connection;

    /**
     * DBConnector constructor.
     */
    private final function __construct()
    {
        self::$DBConnection = new PDO("firebird:dbname=" . DB_PATH, USERNAME, PASSWORD,
            array(PDO::ATTR_PERSISTENT => true));
    }

    public function isConnected(): bool
    {
        return self::$DBConnection->getAttribute(PDO::ATTR_CONNECTION_STATUS) == 1;
    }

    public static function getInstance(): DBConnector
    {
        if (!isset(self::$connection))
            self::$connection = new DBConnector();
        return self::$connection;
    }

    public static function prepareSelectStatement(string $tableName, array $columns = Null, array $whereArgs = Null, array $whereValues = Null,
                                           string $specialWhere = Null, int $limit = Null, string $orderBy = Null)
    {

        if (isset($whereArgs) and !isset($whereValues))
            throw new InvalidArgumentException("parameter whereArgs is provided, you should provide whereValues argument too.");
        if (isset($whereArgs) and isset($whereValues) and sizeof($whereValues) != sizeof($whereArgs))
            throw new InvalidArgumentException("parameter whereValues doesn't have enough values to match with whereArgs.");

        $query = "SELECT ";
        if (isset($columns) and sizeof($columns) > 0) {
            $joined = join(", ", $columns);
            $query .= $joined . " FROM ";
        } else {
            $query .= "* FROM ";
        }

        $query .= "$tableName ";

        // injecting the where args
        if (isset($whereArgs)) {
            $query .= "WHERE ";
            if (sizeof($whereArgs) == 1) {
                $query .= "$whereArgs[0] = ? ";
            } elseif (sizeof($whereArgs) > 1) {
                $joined = join(" = ? AND ", $whereArgs);
                $query .= $joined . " = ? ";
            }
        }

        if (isset($specialWhere))
            if (strpos($query, "WHERE") == false) {
                $query .= "WHERE $specialWhere";
            } else {
                $query .= "AND $specialWhere ";
            }

        if (isset($limit))
            $query .= "LIMIT $limit ";

        if (isset($orderBy))
            $query .= "ORDER BY $orderBy";
        else
            $query .= "ORDER by RECORDID";

        $query .= ";";

        $statement = self::$DBConnection->prepare($query);

        if ($statement == false)
            throw new InvalidArgumentException("Failed to compile this query: $query");

        if (isset($whereValues))
            foreach ($whereArgs as $key => $value)
                $statement->bindParam($key + 1, $whereValues[$key]);

        return $statement;
    }

}