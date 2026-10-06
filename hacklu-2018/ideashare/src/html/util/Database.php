<?php


class DatabaseException extends Exception {}


/** Wrapper around that shitty PDO API. */
class Database
{
    private $pdo_string;
    private $username;
    private $password;
    private $connection;
    private $current_db;

    public function __construct($pdo_string = '', $username = '', $password = '')
    {
        $this->pdo_string = $pdo_string;
        $this->username = $username;
        $this->password = $password;
        $this->connection = null;  // will lazily connect later
        $this->current_db = null;
    }

    /** Build a Database instance for MySQL. */
    static public function buildMySQL($host, $dbname, $username, $password)
    {
        return new Database("mysql:host=$host;dbname=$dbname", $username,
                            $password);
    }

    /** Build a Database instance for SQLite. */
    static public function buildSQLite($dbpath)
    {
        return new Database("sqlite:$dbpath");
    }

    /** Put a new record into the database and return its id. */
    public function put()
    {
        $stmt = $this->_query(func_get_args());
        return $this->connection->lastInsertID();
    }

    public function fetch()
    {
        $stmt = $this->_query(func_get_args());
        return $stmt->fetch();
    }

    public function fetchOne()
    {
        $stmt = $this->_query(func_get_args());
        $row = $stmt->fetch();
        if (!$row) {
            throw new DatabaseException('Expected result from query.');
        }
        return $row;
    }

    public function fetchAll()
    {
        $stmt = $this->_query(func_get_args());
        return $stmt->fetchAll();
    }

    /** Create a database. */
    public function create($name)
    {
        $sanitized_name = Database::sanitizeName($name);
        return $this->execute("CREATE DATABASE `$sanitized_name`");
    }

    public function select($name)
    {
        if ($this->current_db !== $name) {
            $sanitized_name = Database::sanitizeName($name);
            $this->execute("USE `$sanitized_name`");
            $this->current_db = $name;
        }
    }

    public function drop($name)
    {
        $sanitized_name = Database::sanitizeName($name);
        $this->execute("DROP DATABASE `$sanitized_name`");
    }

    /** Sanitize a `table_name` or `database_name`. */
    static public function sanitizeName($name)
    {
        return preg_replace('/\W/', '', $name);
    }

    /** Execute a database query and return the number of affected rows. */
    public function execute($sql)
    {
        $this->connect();
        return $this->connection->exec($sql);
    }

    /** Very raw query function for outside consumers. */
    public function query($sql, array $args=array())
    {
        array_unshift($args, $sql);
        return $this->_query($args);
    }

    /** Query the database and receive a result. */
    private function _query(array $args=array())
    {
        $this->connect();
        $sql = array_shift($args);
        $stmt = $this->connection->prepare($sql);
        for ($i = 0; $i < count($args); $i++) {
            $stmt->bindParam($i + 1, $args[$i]);
        }
        $stmt->execute();
        return $stmt;
    }

    /** Connect to the database. */
    public function connect()
    {
        if ($this->isConnected()) {
            return;
        }
        try {
            $this->connection = new PDO(
                $this->pdo_string,
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
            $this->connection->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_OBJ
            );
        } catch (PDOException $error) {
            $msg = 'Could not connect to Database: ' . $error->getMessage();
            throw new DatabaseException($msg);
        }
    }

    public function isConnected()
    {
        return !is_null($this->connection);
    }
}
