<?php
namespace Core;

class Model
{
    /**
     * instacia do PDO
     *
     * @var PDO
     */
    protected $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }
}