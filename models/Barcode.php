<?php

require_once "Autoload.php";

class Barcode extends Model
{
    private const TABLE_NAME = "CODEBARRE";
    public string $codeBar, $codeBareSyn;

    /**
     * Barcode constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Barcode constructor.
     * @param $id
     * @param string $codeBar
     * @param string $codeBareSyn
     */
    private function fillParams($id, string $codeBar, string $codeBareSyn)
    {
        $this->id = $id;
        $this->codeBar = $codeBar;
        $this->codeBareSyn = $codeBareSyn;
    }

    public static function withParams($id, $codeBar, $codeBareSyn)
    {
        $barcode = new Barcode();
        $barcode->fillParams($id, $codeBar, $codeBareSyn);
        return $barcode;
    }

    public static function getBarcodes($productCode)
    {
        $statement = self::$DBConnection::prepareSelectStatement(self::TABLE_NAME, null, ["CODE_BARRE"],
            [$productCode]);
        if ($statement->execute()) {
            return self::fetchResult($statement);
        }
        return [];
    }


    protected static function fetchResult($statement): array
    {
        $codes = [];
        while ($row = $statement->fetch()) {
            array_push($codes, self::withParams($row["RECORDID"], self::utf($row["CODE_BARRE"]),
                self::utf($row["CODE_BARRE_SYN"])));
        }
        return $codes;
    }
}