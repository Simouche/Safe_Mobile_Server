<?php

require_once "Autoload.php";

class Purchase extends Model
{
    const TABLE_NAME = "BON_A1";

    const NUM_BON = "NUM_BON";
    const NUM_FACTURE = "NUM_FACTURE";
    const CODE_FRS = "CODE_FRS";
    const DATE_BON = "DATE_BON";
    const HEURE = "HEURE";

    const ALL_COLUMNS = [self::NUM_BON, self::CODE_FRS, self::DATE_BON, self::HEURE];

    protected static function fetchResult($statement): array
    {
        // TODO: Implement fetchResult() method.
    }

    public static function addPurchases(IRequest $request)
    {
        $data = $request->getBody();
        error_log(json_encode($data), 3, "logs/logs.json");
        $purchases = $data["raw"];

    }

    private static function getNumber(): string
    {
        $statement = self::$DBConnection::executeRawQuery("SELECT GEN_ID(GEN_BON_A1_ID,1) FROM RDB\$DATABASE");
        if ($statement->execute()) {
            $result = $statement->fetch();
            return $result["GEN_ID"];
        }
        return rand(1000, 9000);
    }
}
