<?php
namespace Core;

class Core
{
    /**
     * Método responsável por parsear a url e definir o controller e action
     * 
     * @return void
     */
    public function run(): void
    {
        $url = "/";
        $params = [];
        $prefix = "\Controllers\\";

        if (isset($_GET["url"])) {
            $url .= $_GET["url"];
        }

        $url = $this->checkRoutes($url);

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

        //ERRO 404
        if (
            !file_exists(__DIR__."/../Controllers/".$currentController.".php") ||
            !method_exists($prefix.$currentController, $currentAction)
        ) {
            $currentController = "NotFoundController";
            $currentAction = "index";
        }

        $currentController = $prefix.$currentController;
        $c = new $currentController();
        call_user_func_array([
            $c,
            $currentAction
        ], $params);
    }

    /**
     * encontra uma rota e substitui a url pela url da rota
     *
     * @param string $url
     * @return string
     */
    public function checkRoutes(string $url): string
    {
        global $routes;

        foreach ($routes as $pt => $newUrl) {
            //IDETEIFICAR OS ARGUMENTOS E SUBSTITUIR POR REGEX
            $pattern = preg_replace("(\{[a-z0-9]{1,}\})", "([a-z0-9-]{1,})", $pt);
            
            //FAZ O MATCH DA URL
            if (preg_match("#^(".$pattern.")*$#i", $url, $matches) === 1) {
                array_shift($matches);
                array_shift($matches);

                //PEGA TODOS OS ARGUMENTOS PARA ASSOCIAR
                $itens = [];
                if (preg_match_all("(\{[a-z0-9]{1,}\})", $pt, $m)) {
                    $itens = preg_replace("(\{|\})", "", $m[0]);
                }

                //FAZ A ASSOCIAÇÃO
                $arg = [];
                foreach ($matches as $key => $match) {
                    $arg[$itens[$key]] = $match;
                }

                //MONTA A NOVA URL
                foreach ($arg as $argkey => $argvalue) {
                    $newUrl = str_replace(":".$argkey, $argvalue, $newUrl);
                }

                $url = $newUrl;

                break;
            }
        }

        return $url;
    }

}
