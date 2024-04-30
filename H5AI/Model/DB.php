<?php
class DB
{
        protected PDO|null $db;
        public function __construct()
        {
                $this->db = new PDO('mysql:host=localhost;dbname=h5ai', 'root', '1234');
        }

        public function query($sql, $params = []): false|PDOStatement
        {
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                return $stmt;
        }

        public function fetch($sql, $params = [])
        {
                return $this->query($sql, $params)->fetch();
        }

        public function fetchAll($sql, $params = []): false|array
        {
                return $this->query($sql, $params)->fetchAll();
        }

        public function insert($sql, $params = []): false|string
        {
                $this->query($sql, $params);
                return $this->db->lastInsertId();
        }

        public function update($sql, $params = []): void
        {
                $this->query($sql, $params);
        }

        public function delete($sql, $params = []): void
        {
                $this->query($sql, $params);
        }

        public function beginTransaction(): void
        {
                $this->db->beginTransaction();
        }

        public function commit(): void
        {
                $this->db->commit();
        }

        public function __destruct()
        {
                $this->db = null;
        }
}