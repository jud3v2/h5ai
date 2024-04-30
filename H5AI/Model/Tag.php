<?php

include_once 'DB.php';

class Tag extends DB
{
        public function __construct()
        {
                parent::__construct();
        }

        public function getTags(): array
        {
                return $this->fetchAll('SELECT * FROM tags');
        }

        public function getTag(int|string $id): array
        {
                return $this->fetch('SELECT * FROM tags WHERE id = ?', [$id]);
        }

        public function findByPath(string $path): array
        {
                return $this->fetchAll('SELECT * FROM tags WHERE path = ?', [$path]);
        }

        public function addTag(string $path, string $name): string
        {
                return $this->insert('INSERT INTO tags (path, tag_name) VALUES (?,?)', [$path, $name]);
        }

        public function create(array $data): string
        {
                return $this->addTag($data['path'], $data['tag_name']);
        }

        public function updateTag(int|string $id, string $name): void
        {
                $this->update('UPDATE tags SET tag_name = ? WHERE id = ?', [$name, $id]);
        }

        public function deleteTag(int|string $name): void
        {
                $this->delete('DELETE FROM tags WHERE tag_name = ?', [$name]);
        }
}