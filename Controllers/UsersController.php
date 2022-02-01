<?php
namespace Controllers;

use Core\Controller;
use Models\Users;

class UsersController extends Controller
{
    public function index()
    {
    }

    /**
     * efetua o login do usuário
     *
     * @return void
     */
    public function login()
    {
        $array = [
            "error" => ""
        ];

        $method = $this->getMethod();
        $data   = $this->getRequestData();

        if ($method == "POST") {
            if (!empty($data["email"]) && !empty($data["pass"])) {
                $users = new Users();

                if ($users->checkCredentials($data["email"], $data["pass"])) {
                    //GERAR JWT  
                    $array["jwt"] = $users->createJwt();
                } else {
                    $array["error"] = "Acesso negado";
                }
            } else {
                $array["error"] = "Email e/ou senha  não preenchido!";
            }
        } else {
            $array["error"] = "Método de requisição incompatível.";
        }

        $this->returnJson($array);
    }

    /**
     * registra um novo usuário
     *
     * @return void
     */
    public function new_record()
    {
        $array = [
            "error" => ""
        ];

        $method = $this->getMethod();
        $data = $this->getRequestData();

        if ($method == "POST") {
            if (!empty($data["name"]) && !empty($data["email"]) && !empty($data["pass"])) {
                if (filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
                    $users = new Users();

                    if ($users->create($data["name"], $data["email"], $data["pass"])) {
                        $array["jwt"] = $users->createJwt();
                    } else {
                        $array["error"] = "Email já existe!";
                    }
                } else {
                    $array["error"] = "Email inválido!";
                }
            } else {
                $array["error"] = "Dados não preenchidos!";
            }
        } else {
            $array["error"] = "Método de requisição incompatível.";
        }

        $this->returnJson($array);
    }
}