<?php
require_once "IRequest.php";

class Request implements IRequest
{

    function __construct()
    {
        $this->bootstrapSelf();
    }

    public function getBody()
    {
        if ($this->requestMethod === "GET") {
            return;
        }

        if ($this->requestMethod == "POST") {
            $body = array();
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
            $raw = file_get_contents("php://input");
            if ($raw)
                $body["raw"] = json_decode($raw, TRUE);
            return $body;
        }
    }

    public function getQueries()
    {
        if ($this->requestMethod === "POST") {
            return;
        }

        if ($this->requestMethod == "GET") {
            $queries = array();
            foreach ($_GET as $key => $value) {
                $queries[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
            return $queries;
        }
    }

    private function bootstrapSelf()
    {
        foreach ($_SERVER as $key => $value) {
            $this->{$this->toCamelCase($key)} = $value;
        }
    }

    private function toCamelCase(string $string)
    {
        $result = strtolower($string);
        preg_match_all('/_[a-z]/', $result, $matches);

        foreach ($matches[0] as $match) {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }
        return $result;
    }


}