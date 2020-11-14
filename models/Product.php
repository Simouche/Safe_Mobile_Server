<?php

require_once "Autoload.php";

class Product extends Model
{
    const TABLE_NAME = "PRODUIT";
    public string $codeBarre, $refProduit, $produit, $paHT, $tva, $paTTC, $pv1HT, $pv1TTC, $pv2HT, $pv2TTC, $pv3HT,
        $pv3TTC, $stock, $datePer, $promo, $marque;
    public array $barcodes;


    /**
     * Product constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Product constructor.
     * @param string $codeBarre
     * @param string $refProduit
     * @param string $produit
     * @param string $paHT
     * @param string $tva
     * @param string $paTTC
     * @param string $pv1HT
     * @param string $pv1TTC
     * @param string $pv2HT
     * @param string $pv2TTC
     * @param string $pv3HT
     * @param string $pv3TTC
     * @param string $stock
     * @param string $datePer
     * @param string $promo
     * @param string $marque
     */
    private function fillParams(int $id, string $codeBarre, string $refProduit, string $produit, string $paHT, string $tva,
                                string $paTTC, string $pv1HT, string $pv1TTC, string $pv2HT, string $pv2TTC, string $pv3HT,
                                string $pv3TTC, string $stock, string $datePer, string $promo, string $marque)
    {
        $this->id = $id;
        $this->codeBarre = $codeBarre;
        $this->refProduit = $refProduit;
        $this->produit = $produit;
        $this->paHT = $paHT;
        $this->tva = $tva;
        $this->paTTC = $paTTC;
        $this->pv1HT = $pv1HT;
        $this->pv1TTC = $pv1TTC;
        $this->pv2HT = $pv2HT;
        $this->pv2TTC = $pv2TTC;
        $this->pv3HT = $pv3HT;
        $this->pv3TTC = $pv3TTC;
        $this->stock = $stock;
        $this->datePer = $datePer;
        $this->promo = $promo;
        $this->marque = $marque;
    }

    public static function withParams(int $id, string $codeBarre, string $refProduit, string $produit, string $paHT, string $tva,
                                      string $paTTC, string $pv1HT, string $pv1TTC, string $pv2HT, string $pv2TTC, string $pv3HT,
                                      string $pv3TTC, string $stock, string $datePer, string $promo, string $marque)
    {
        $product = new Product();
        $product->fillParams($id, $codeBarre, $refProduit, $produit, $paHT, $tva, $paTTC, $pv1HT, $pv1TTC, $pv2HT,
            $pv2TTC, $pv3HT, $pv3TTC, $stock, $datePer, $promo, $marque);
        return $product;
    }

    public static function getProducts()
    {
        $statement = self::$DBConnection::prepareSelectStatement(self::TABLE_NAME);
        if ($statement->execute()) {
            $result = self::fetchResult($statement);
            $response = array("status" => true, "message" => "all products", "data" => array("products" => $result));

            echo json_encode($response);
        } else {
            echo json_encode(array("status" => false, "message" => "failed!", "data" => array()));
        }
    }

    protected static function fetchResult($statement): array
    {
        $products = [];
        while ($row = $statement->fetch()) {
            $product = self::withParams($row["RECORDID"], self::utf($row["CODE_BARRE"]),
                self::utf($row["REF_PRODUIT"]), self::utf($row["PRODUIT"]), self::utf($row["PA_HT"]), self::utf($row["TVA"]),
                self::utf($row["PA_TTC"]), self::utf($row["PV1_HT"]), self:: utf($row["PV1_TTC"]), self::utf($row["PV2_HT"]),
                self::utf($row["PV2_TTC"]), self:: utf($row["PV3_HT"]), self::utf($row["PV3_TTC"]), self::utf($row["STOCK"]),
                self::utf($row["DATE_PER"]), self::utf($row["PROMO"]), self::utf($row["MARQUE"]));
            $product->barcodes = Barcode::getBarcodes($product->codeBarre);
            array_push($products, $product);
        }
        return $products;
    }

    public static function synchronizeProducts(IRequest $request)
    {
        try {
            $ids = json_decode($request->getBody()["ids"]);
            $statement = self::$DBConnection::prepareSelectStatement(self::TABLE_NAME, null, null,
                null, "RECORDID NOT IN (" . implode(',', $ids) . ") ");
            if ($statement->execute()) {
                $result = self::fetchResult($statement);
                $response = array("status" => true, "message" => "all products", "data" => array("products" => $result));

                echo json_encode($response);
            } else {
                echo json_encode(array("status" => false, "message" => "failed", "data" => array()));
            }
        } catch (InvalidArgumentException $exception) {
            echo $exception;
            return;
        }
    }

}

if (!Product::isInitialized())
    new Client();