<?php

require_once "Autoload.php";

class Client extends Model
{
    const TABLE_NAME = "CLIENTS";
    public string $codeClient;
    public $client, $activity, $codePostal, $address, $commune, $wilaya, $contact, $phone, $fax, $rc, $if, $is,
        $art, $compte, $rib, $email, $siteWeb, $soldInitial, $creditLimit, $solde, $modeTarif, $latitude, $longitude;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Fill params function.
     * @param int $id
     * @param string $codeClient
     * @param string|null $client
     * @param string|null $activity
     * @param string|null $codePostal
     * @param string|null $address
     * @param string|null $commune
     * @param string|null $wilaya
     * @param string|null $contact
     * @param string|null $phone
     * @param string|null $fax
     * @param string|null $rc
     * @param string|null $if
     * @param string|null $is
     * @param string|null $art
     * @param string|null $compte
     * @param string|null $rib
     * @param string|null $email
     * @param string|null $siteWeb
     * @param string|null $soldInitial
     * @param string|null $creditLimit
     * @param string|null $solde
     * @param string|null $modeTarif
     * @param string|null $latitude
     * @param string|null $longitude
     */
    private function fillParams(int $id, string $codeClient, string $client = null, string $activity = null,
                                string $codePostal = null, string $address = null, string $commune = null,
                                string $wilaya = null, string $contact = null, string $phone = null, string $fax = null,
                                string $rc = null, string $if = null, string $is = null, string $art = null,
                                string $compte = null, string $rib = null, string $email = null, string $siteWeb = null,
                                string $soldInitial = null, string $creditLimit = null, string $solde = null,
                                string $modeTarif = null, string $latitude = null, string $longitude = null)
    {
        $this->id = $id;
        $this->codeClient = $codeClient;
        $this->client = $client;
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
        $this->creditLimit = $creditLimit;
        $this->solde = $solde;
        $this->modeTarif = $modeTarif;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public static function withParams(int $id, string $codeClient, string $client = null, string $activity = null, string $codePostal = null, string $address = null,
                                      string $commune = null, string $wilaya = null, string $contact = null, string $phone = null, string $fax = null, string $rc = null,
                                      string $if = null, string $is = null, string $art = null, string $compte = null, string $rib = null, string $email = null,
                                      string $siteWeb = null, string $soldInitial = null, string $creditLimit = null, string $solde = null,
                                      string $modeTarif = null, string $latitude = null, string $longitude = null)
    {
        $instance = new self();
        $instance->fillParams($id, $codeClient, $client, $activity, $codePostal, $address, $commune, $wilaya, $contact, $phone,
            $fax, $rc, $if, $is, $art, $compte, $rib, $email, $siteWeb, $soldInitial, $creditLimit, $solde, $modeTarif,
            $latitude, $longitude);
        return $instance;
    }


    public static function getClients()
    {
        $statement = self::$DBConnection::prepareSelectStatement(self::TABLE_NAME);
        if ($statement->execute()) {
            $clients = [];
            while ($row = $statement->fetch()) {
                array_push($clients, self::withParams($row["RECORDID"], utf8_encode($row["CODE_CLIENT"]), utf8_encode($row["CLIENT"]),
                    utf8_encode($row["ACTIVITE"]), utf8_encode($row["CODE_POSTAL"]), utf8_encode($row["ADRESSE"]), utf8_encode($row["COMMUNE"]), utf8_encode($row["WILAYA"]), utf8_encode($row["CONTACT"]),
                    utf8_encode($row["TEL"]), utf8_encode($row["FAX"]), utf8_encode($row["NUM_RC"]), utf8_encode($row["NUM_IF"]), utf8_encode($row["NUM_IS"]), utf8_encode($row["NUM_ART"]),
                    utf8_encode($row["COMPTE"]), utf8_encode($row["RIB"]), utf8_encode($row["EMAIL"]), utf8_encode($row["SITE_WEB"]), utf8_encode($row["SOLDE_INI"]), utf8_encode($row["CREDIT_LIMIT"]),
                    utf8_encode($row["SOLDE"]), utf8_encode($row["MODE_TARIF"]), utf8_encode($row["LATITUDE"]), utf8_encode($row["LONGITUDE"])));
            }
            $response = array("status" => true, "message" => "all clients", "data" => array("clients" => $clients));

            echo json_encode($response);
        } else {
            echo json_encode(array("status" => true, "message" => "all clients", "data" => array()));
        }
    }

    function clientsCount()
    {
        try {
            $statement = self::$DBConnection::prepareSelectStatement(self::TABLE_NAME, ["COUNT(*) AS ROWS_COUNT"]);
            if ($statement->execute()) {
                $row = $statement->fetch();
                echo $row["ROWS_COUNT"];
            } else {
                echo json_encode(array("status" => true, "message" => "all clients", "data" => array()));
            }
        } catch (InvalidArgumentException $exception) {
            echo $exception;
            return;
        }
    }

    static function synchronizeClients(IRequest $request)
    {
        try {
            $ids = json_decode($request->getQueries()["ids"]);
            $statement = self::$DBConnection::prepareSelectStatement(self::TABLE_NAME, null, null, null, "RECORDID NOT IN (" . implode(',', $ids) . ") ");
            if ($statement->execute()) {
                $clients = [];
                while ($row = $statement->fetch()) {
                    array_push($clients, self::withParams($row["RECORDID"], utf8_encode($row["CODE_CLIENT"]), utf8_encode($row["CLIENT"]),
                        utf8_encode($row["ACTIVITE"]), utf8_encode($row["CODE_POSTAL"]), utf8_encode($row["ADRESSE"]), utf8_encode($row["COMMUNE"]), utf8_encode($row["WILAYA"]), utf8_encode($row["CONTACT"]),
                        utf8_encode($row["TEL"]), utf8_encode($row["FAX"]), utf8_encode($row["NUM_RC"]), utf8_encode($row["NUM_IF"]), utf8_encode($row["NUM_IS"]), utf8_encode($row["NUM_ART"]),
                        utf8_encode($row["COMPTE"]), utf8_encode($row["RIB"]), utf8_encode($row["EMAIL"]), utf8_encode($row["SITE_WEB"]), utf8_encode($row["SOLDE_INI"]), utf8_encode($row["CREDIT_LIMIT"]),
                        utf8_encode($row["SOLDE"]), utf8_encode($row["MODE_TARIF"]), utf8_encode($row["LATITUDE"]), utf8_encode($row["LONGITUDE"])));
                }
                $response = array("status" => true, "message" => "all clients", "data" => array("clients" => $clients));

                echo json_encode($response);
            } else {
                echo json_encode(array("status" => true, "message" => "all clients", "data" => array()));
            }
        } catch (InvalidArgumentException $exception) {
            echo $exception;
            return;
        }
    }


    protected static function fetchResult($statement): array
    {
        // TODO: Implement fetchResult() method.
    }
}

if (!Client::isInitialized())
    new Client();