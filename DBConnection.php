<?php

/**
 * Class Database
 */
class Database
{

    // Hold the class instance.
    /**
     * @var null
     */
    private static $instance = null;
    /**
     * @var mysqli
     */
    private $conn;
    /**
     * @var string
     */
    private $host = "localhost:8080";
    /**
     * @var string
     */
    private $db = "database";
    /**
     * @var string
     */
    private $user = "root@localhost";
    /**
     * @var string
     */
    private $pass = "";

    // The constructor is private
    // to prevent initiation with outer code.
    /**
     * Database constructor.
     */
    private function __construct()
    {
        // The expensive process (e.g.,db connection) goes here.
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
        if ($this-> conn -> connect_errno) {
            echo "Failed to connect to MySQL: " . $this -> conn -> connect_error;
            exit();
        }
    }

    // The object is created from within the class itself
    // only if the class has no instance.
    /**
     * getInstance function is used to instantiate object of singleton database class
     *
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * getConnection function is used for getting connection to MySQL
     *
     * @return mysqli
     */
    public function getConnection()
    {
        return $this->conn;
    }
}