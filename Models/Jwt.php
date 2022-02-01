<?php
namespace Models;

use \Core\Model;

class Jwt extends Model
{
    /**
     * cipher secret
     *
     * @var string
     */
    private $secret;

    /**
     * cria o segredo do cipher
     */
    public function __construct()
    {
        global $config;
        $this->secret = hash("sha256", $config["jwt_secret"]);
    }

    /**
     * gera um JWT com base nos dados enviados
     *
     * @param integer $data
     * @return string
     */
    public function create(array $data): string
    {
        $header = json_encode([
            "type" => "JWT",
            "alg"  => "HS256"
        ]);

        $payload = json_encode($data);

        $hbase = $this->base64url_encode($header);
        $pbase = $this->base64url_encode($payload);

        $sig  = hash_hmac("sha256", $hbase.".".$pbase, $this->secret, true);
        $bsig = $this->base64url_encode($sig); 

        $jwt = $hbase.".".$pbase.".".$bsig;

        return $jwt;
    }

    /**
     * decodifica um JWT
     *
     * @param string $token
     * @return mixed
     */
    public function validate(string $token): mixed
    {
        $array = [];

        $jwt_split = explode(".", $token);
        if (count($jwt_split) == 3) {
            $sig  = hash_hmac("sha256", $jwt_split[0].".".$jwt_split[1], $this->secret, true);
            $bsig = $this->base64url_encode($sig);

            if ($bsig == $jwt_split[2]) {
                $array = json_decode($this->base64url_decode($jwt_split[1]));
                return $array;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * codifica o parâmetro para base64url
     * https://www.php.net/manual/en/function.base64-encode.php#121767
     *
     * @param string $data
     * @return string
     */
    private function base64url_encode(string $data): string
    {
        return rtrim( strtr( base64_encode( $data ), '+/', '-_'), '=');
    }

    /**
     * decodifica o parâmetro em base64url
     * https://www.php.net/manual/en/function.base64-encode.php#121767
     *
     * @param string $data
     * @return string
     */
    private function base64url_decode(string $data): string
    {
        return base64_decode( strtr( $data, '-_', '+/') . str_repeat('=', 3 - ( 3 + strlen( $data )) % 4 ));
    }
}
