<?php

require_once "Autoload.php";

class Product extends Model
{
    const TABLE_NAME = "PRODUIT";
    public string $codeBarre, $refProduit, $produit, $datePer, $marque;
    public float $paHT, $tva, $paTTC, $pv1HT, $pv1TTC, $pv2HT, $pv2TTC, $pv3HT,
        $pv3TTC, $promo, $stock;
    public array $barcodes;

    const CODE_BARRE = "CODE_BARRE";
    const REF_PRODUIT = "REF_PRODUIT";
    const PRODUIT = "PRODUIT";
    const PA_HT = "PA_HT";
    const TVA = "TVA";
    const PA_TTC = "PA_TTC";
    const PAMP_HT = "PAMP_HT";
    const PAMP_TTC = "PAMP_TTC";
    const PV1_HT = "PV1_HT";
    const PV1_TTC = "PV1_TTC";
    const PV2_HT = "PV2_HT";
    const PV2_TTC = "PV2_TTC";
    const PV3_HT = "PV3_HT";
    const PV3_TTC = "PV3_TTC";
    const STOCK = "STOCK";
    const MARQUE = "MARQUE";

    const ALL_COLUMNS = [self::CODE_BARRE, self::REF_PRODUIT, self::PRODUIT, self::PA_HT, self::TVA,
        self::PAMP_HT, self::PV1_HT, self::PV2_HT, self::PV3_HT, self::STOCK];


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
        $this->paHT = doubleval($paHT);
        $this->tva = doubleval($tva);
        $this->paTTC = doubleval($paTTC);
        $this->pv1HT = doubleval($pv1HT);
        $this->pv1TTC = doubleval($pv1TTC);
        $this->pv2HT = doubleval($pv2HT);
        $this->pv2TTC = doubleval($pv2TTC);
        $this->pv3HT = doubleval($pv3HT);
        $this->pv3TTC = doubleval($pv3TTC);
        $this->stock = doubleval($stock);
        $this->datePer = $datePer;
        $this->promo = doubleval($promo);
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
            $response = array("status" => true, "message" => "all products", "products" => $result);

            echo json_encode($response);
        } else {
            echo json_encode(array("status" => false, "message" => "failed!", "products" => array()));
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
                $response = array("status" => true, "message" => "all products", "products" => $result);
            } else {
                $response = array("status" => true, "message" => "all products", "products" => array());
            }
            echo json_encode($response);
        } catch (InvalidArgumentException $exception) {
            echo $exception;
            return;
        }
    }

    public static function addProducts(IRequest $request)
    {
        try {
            $data = $request->getBody();
//            error_log(json_encode($data),3,"logs/logs.json");
            $products = $data["raw"];
            $values = array();
            foreach ($products as $row)
                $values[] = [$row["produit"], $row["refProduit"], $row["produit"], $row["paHT"], $row["tva"],
                    $row["steadyPurchasePriceHT"] ?? 0.0, $row["pv1HT"], $row["pv2HT"], $row["pv3HT"],
                    $row["stock"]];

            self::$DBConnection::prepareInsertStatement(self::TABLE_NAME, self::ALL_COLUMNS, $values);

            $response = array("status" => true, "message" => "all products");
            return json_encode($response);
        } catch (InvalidArgumentException $exception) {
            $response = array("status" => true, "message" => $exception->getMessage());
            return json_encode($response);
        }
    }

}

if (!Product::isInitialized())
    new Client();