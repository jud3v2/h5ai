<?php

class H5AI
{
        /**
         * @var array|null
         */
        public ?array $tree;
        /**
         * @var mixed
         */
        public mixed $path;

        public function __construct($path)
        {
                $this->path = $path;
                $this->tree = $this->getTree();
        }

        public function getFiles(): array
        {
                $files = array();
                $dir = dir($this->path);
                while (false !== ($entry = $dir->read()))
                {
                        if ($entry != "." && $entry != ".." && $entry != "H5AI.php" && $entry != "index.php" && is_file($this->path . "/" . $entry))
                        {
                                $files[] = $entry;
                        }
                }
                $dir->close();
                return $files;
        }

        public function getDirectories(): array
        {
                $directories = array();
                $dir = dir($this->path);
                while (false !== ($entry = $dir->read()))
                {
                        if ($entry != "." && $entry != ".." && is_dir($this->path . "/" . $entry))
                        {
                                $directories[] = $entry;
                        }
                }
                $dir->close();
                return $directories;
        }

        /**
         * @description Recursive function to get the tree of the directory
         * @return array
         */
        public function getTree(): array
        {
                $tree = array();
                $files = $this->getFiles();
                $directories = $this->getDirectories();
                foreach ($files as $file)
                {
                        $tree[] = array(
                                "type" => "file",
                                "path" => $this->path . "/" . $file,
                                "name" => $file,
                                "size" => filesize($this->path . "/" . $file),
                                "updated_at" => filemtime($this->path . "/" . $file),
                                "mime" => mime_content_type($this->path . "/" . $file),
                        );
                }
                foreach ($directories as $directory)
                {
                        $tree[] = array(
                                "type" => "directory",
                                "path" => $this->path . "/" . $directory,
                                "name" => $directory,
                                "tree" => (new H5AI($this->path . "/" . $directory))->getTree(),
                                "size" => filesize($this->path . "/" . $directory),
                                "updated_at" => filemtime($this->path . "/" . $directory),
                        );
                }
                return $tree;
        }
}