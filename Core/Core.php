<?php
namespace Core;

class Core
{
    /**
     * Método responsável por parsear a url e definir o controller e action
     */
    public function run(): void
    {
        $url = "/";
        $params = [];
        $prefix = "\Controllers\\";

        if (isset($_GET["url"])) {
            $url .= $_GET["url"];
        }

        if (!empty($url) && $url != "/") {
            $url = explode("/", $url);
            array_shift($url);

            $currentController = $url[0]."Controller";
            array_shift($url);

            if (isset($url[0]) && !empty($url[0])) {
                $currentAction = $url[0];
                array_shift($url);
            } else {
                //CASO TIVER SÓ O CONTROLLER DEFINIDO
                $currentAction = "index";
            }

            if (count($url) > 0) {
                $params = $url;
            }
        } else {
            //CASO NÃO TIVER NADA DEFINIDO NA URL
            $currentController = "HomeController";
            $currentAction = "index";
        }

        //FORMATA O CONTROLLER PRA StudlyCaps
        $currentController = ucfirst($currentController);
        $currentController = $prefix.$currentController;

        $c = new $currentController();
        call_user_func_array([
            $c,
            $currentAction
        ], $params);
    }
}
