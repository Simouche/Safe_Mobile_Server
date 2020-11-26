<?php


class Provider extends Model
{

    const TABLE_NAME = "FOURNIS";
    public string $codeFournis;
    public $fournis, $activity, $codePostal, $address, $commune, $wilaya, $contact, $phone, $fax, $rc, $if, $is,
        $art, $compte, $rib, $email, $siteWeb, $soldInitial, $verser, $solde, $notes, $codeClient;


    /**
     * Client constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Provider constructor.
     * @param string $codeFournis
     * @param $fournis
     * @param $activity
     * @param $codePostal
     * @param $address
     * @param $commune
     * @param $wilaya
     * @param $contact
     * @param $phone
     * @param $fax
     * @param $rc
     * @param $if
     * @param $is
     * @param $art
     * @param $compte
     * @param $rib
     * @param $email
     * @param $siteWeb
     * @param $soldInitial
     * @param $verser
     * @param $solde
     * @param $notes
     * @param $codeClient
     */
    public function fillParams($id, string $codeFournis, $fournis, $activity, $codePostal, $address, $commune, $wilaya,
                               $contact, $phone, $fax, $rc, $if, $is, $art, $compte, $rib, $email, $siteWeb, $soldInitial,
                               $verser, $solde, $notes, $codeClient)
    {
        $this->id = $id;
        $this->codeFournis = $codeFournis;
        $this->fournis = $fournis;
        $this->activity = $activity;
        $this->codePostal = $codePostal;
        $this->address = $address;
        $this->commune = $commune;
        $this->wilaya = $wilaya;
        $this->contact = $contact;
        $this->phone = $phone;
        $this->fax = $fax;
        $this->rc = $rc;
        $this->if = $if;
        $this->is = $is;
        $this->art = $art;
        $this->compte = $compte;
        $this->rib = $rib;
        $this->email = $email;
        $this->siteWeb = $siteWeb;
        $this->soldInitial = $soldInitial;
        $this->verser = $verser;
        $this->solde = $solde;
        $this->notes = $notes;
        $this->codeClient = $codeClient;
    }


    public static function withParams($id, string $codeFournis, $fournis, $activity, $codePostal, $address, $commune, $wilaya,
                                      $contact, $phone, $fax, $rc, $if, $is, $art, $compte, $rib, $email, $siteWeb, $soldInitial,
                                      $verser, $solde, $notes, $codeClient)
    {
        $instance = new self();
        $instance->fillParams($id, $codeFournis, $fournis, $activity, $codePostal, $address, $commune, $wilaya, $contact, $phone,
            $fax, $rc, $if, $is, $art, $compte, $rib, $email, $siteWeb, $soldInitial, $verser, $solde, $notes, $codeClient);
        return $instance;
    }

    public static function getProviders()
    {
        $statement = self::$DBConnection::prepareSelectStatement(self::TABLE_NAME);
        if ($statement->execute()) {
            $result = self::fetchResult($statement);
            $response = array("status" => true, "message" => "all providers", "providers" => $result);

            echo json_encode($response);
        } else {
            echo json_encode(array("status" => false, "message" => "failed!", "providers" => []));
        }
    }

    public static function synchronizeProviders(IRequest $request)
    {
        try {
            $ids = json_decode($request->getBody()["ids"]);
            $statement = self::$DBConnection::prepareSelectStatement(self::TABLE_NAME, null, null,
                null, "RECORDID NOT IN (" . implode(',', $ids) . ") ");
            if ($statement->execute()) {
                $result = self::fetchResult($statement);
                $response = array("status" => true, "message" => "all providers", "data" => array("providers" => $result));

                echo json_encode($response);
            } else {
                echo json_encode(array("status" => false, "message" => "failed", "data" => array()));
            }
        } catch (InvalidArgumentException $exception) {
            echo $exception;
            return;
        }
    }


    protected static function fetchResult($statement): array
    {
        $providers = [];
        while ($row = $statement->fetch()) {
            $provider = self::withParams($row["RECORDID"], self::utf($row["CODE_FRS"]), self::utf($row["FOURNIS"]),
                self::utf($row["ACTIVITE"]), self::utf($row["CODE_POSTAL"]), self::utf($row["ADRESSE"]),
                self::utf($row["COMMUNE"]), self::utf($row["WILAYA"]), self:: utf($row["CONTACT"]), self::utf($row["TEL"]),
                self::utf($row["FAX"]), self:: utf($row["NUM_RC"]), self::utf($row["NUM_IF"]), self::utf($row["NUM_IS"]),
                self::utf($row["NUM_ART"]), self::utf($row["COMPTE"]), self::utf($row["RIB"]), self::utf($row["EMAIL"]),
                self::utf($row["SITE_WEB"]), self::utf($row["SOLDE_INI"]), utf8_encode($row["VERSER"]),
                doubleval(self::utf($row["SOLDE"])), self::utf($row["NOTES"]), intval(self::utf($row["CODE_CLIENT"])));
            array_push($providers, $provider);
        }
        return $providers;
    }
}

