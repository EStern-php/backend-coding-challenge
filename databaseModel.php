<?php


class databaseModel {

    private $host = 'localhost';
    private $db = 'states';
    private $user = 'root';
    private $password = '';
    private $pdo;

    public function connect() {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=UTF8";

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->password);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function insertInto ($query) {
        $q = "INSERT INTO queries SET query = :query, dateTime = NOW()";
        $stmt = $this->pdo->prepare($q);
        $stmt->execute([':query' => $query]);
    }

    public function getData ($startTime = null, $endTime = null) {
        if ($startTime && $endTime) {
            $q = "SELECT query, dateTime FROM queries WHERE dateTime BETWEEN :startTime AND :endTime";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute([':startTime' => $startTime, ':endTime' => $endTime]);
            $data = $stmt->fetchAll();
        }
        else {
            $q = "SELECT query, dateTime FROM queries";
            $stmt = $this->pdo->prepare($q);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        return $data;   
    }

}