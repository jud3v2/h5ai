<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My H5AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<?php

include_once "H5AI.php";

$sortBy = $_GET['sortBy'] ?? null; // 'name', 'date' or 'size'
$sortOrder = $_GET['sortOrder'] ?? null; // 'asc' or 'desc'

// If the action is not set, we will display the file explorer
if(!isset($_GET['action'])) {
        $my_H5AI = new H5AI($_GET['path'] ?? __DIR__);
        $tree = $my_H5AI->getTree();
        $files = $my_H5AI->getFiles();
        $directories = $my_H5AI->getDirectories();
        $sidebarTree = $my_H5AI->getTree();
}

/**
 * Take an array as references and sort it by name or unsort it
 * @param array $a
 * @return void
 */
function sortByName(array &$a): void
{
        if(isset($_GET['sortOrder']) && $_GET['sortOrder'] === 'desc') {
                usort($a, function ($a, $b) {
                        return $b['name'] <=> $a['name'];
                });
        } else {
                usort($a, function ($a, $b) {
                        return $a['name'] <=> $b['name'];
                });
        }
}

/**
 * Take an array as references and sort it by name or unsort it
 * @param array $a
 * @return void
 */
function sortByDate(array &$a): void
{
        if(isset($_GET['sortOrder']) && $_GET['sortOrder'] === 'desc') {
                usort($a, function ($a, $b) {
                        return $b['updated_at'] <=> $a['updated_at'];
                });
        } else {
                usort($a, function ($a, $b) {
                        return $a['updated_at'] <=> $b['updated_at'];
                });
        }
}

/**
 * Take an array as references and sort it by name or unsort it
 * @param array $a
 * @return void
 */
function sortBySize(array &$a): void
{
        if (isset($_GET['sortOrder']) && $_GET['sortOrder'] === 'desc') {
                usort($a, function ($a, $b) {
                        return $b['size'] <=> $a['size'];
                });
        } else {
                usort($a, function ($a, $b) {
                        return $a['size'] <=> $b['size'];
                });
        }
}

function handleSearch(array &$a): void {
        if(isset($_GET['search'])) {
                $a = array_filter($a, function ($file) {
                        return strpos($file['name'], $_GET['search']) !== false;
                });
        }
}

/**
 * This will show the header of the file explorer
 * @return void
 */
function my_header() : void
{
        echo "<div class='flex justify-between gap-5 items-center'>";
        echo "<h1 class='text-3xl font-bold'>My H5AI</h1>";
        echo "<div class='flex'>";
        // Breadcrumb
        foreach (explode('/', $_GET['path'] ?? __DIR__) as $key => $path) {
                if ($key == 0) {
                        echo "<a href='?path={$path}' class='text-blue-500'>Home</a>";
                } else {
                        echo "<a href='?path={$path}' class='text-blue-500'>{$path}</a>";
                }
                echo "<span class='mx-2'>/</span>";
        }
        echo "</div>";
        echo "</div>";
}

/**
 * This will show the body of the file explorer
 * @return void
 */
function my_body() : void
{
        global $tree;

        if(isset($_GET['search'])){
                handleSearch($tree);
        }

        if(isset($_GET['sortBy']) && $_GET['sortBy'] === 'name'){
                sortByName($tree);
        } elseif(isset($_GET['sortBy']) && $_GET['sortBy'] === 'date'){
                sortByDate($tree);
        } elseif(isset($_GET['sortBy']) && $_GET['sortBy'] === 'size') {
                sortBySize($tree);
        } else {
            sortByName($tree);
        }
        require_once "../Model/Tag.php";
        $tag = new Tag();
        foreach ($tree as &$file) {
            $tags = $tag->findByPath($file['path']);
            $file['tags'] = $tags;
        }

        echo "<div class='flex min-w-full'>";
        // Left bar
        echo '<div class="flex-1 w-1/4">';
            echo '<ul class="mx-1">';
                foreach ($tree as &$file) {
                        if($file['type'] == "directory"){
                                $path = $file['path'];
                                $name= $file['name'];
                                echo "<li class='px-3.5 py-0.5 mr-3.5 mt-3.5 border rounded-lg relative w-full' id='$path' aria-label='$name'>";
                                echo '<a href="?path=' . $file['path'] . '">';
                                echo '<i class="fas fa-folder"></i>';
                                echo '<span class="text-sm font-bold ml-2">';
                                echo $file['name'];
                                echo '</span>';
                                echo '</a>';
                                echo "</li>";
                                foreach ($file['tree'] as $child) {
                                        if($child['type'] == "directory" || $child['type'] == "file") {
                                                $path = $child['path'];
                                                $name = $child['name'];
                                                echo "<li class='px-3.5 py-0.5 mr-1 mt-3.5 border rounded-lg relative w-full' id='$path' aria-label='$name'>";
                                                echo $child['type'] == "directory" ? '<a href="?path=' . $child['path'] . '">' : '<a href="?path=' . $child['path'] . '&action=edit">';

                                                if($child['type'] == "directory"){
                                                        echo '<i class="fas fa-folder"></i>';
                                                } else {
                                                        $mime = explode('.', $file['name'])[1] ?? 'txt';
                                                        echo match ($mime){
                                                                'pdf' => '<i class="fas fa-file-pdf"></i>',
                                                                'php' => '<span class="material-symbols-outlined">php</span>',
                                                                'js' => '<span class="material-symbols-outlined">javascript</span>',
                                                                'html' => '<span class="material-symbols-outlined">html</span>',
                                                                'css' => '<span class="material-symbols-outlined">css</span>',
                                                                'py' => '<span class="material-symbols-outlined">code</span>',
                                                                'rs', 'json', 'go','c' => '<i class="fa-solid fa-code"></i>',
                                                                default => '<i class="fas fa-file"></i>',
                                                        };
                                                }

                                                echo '<span class="text-sm font-bold ml-2">';
                                                echo $child['name'];
                                                echo '</span>';
                                                echo '</a>';
                                                echo "</li>";
                                        }
                                }
                        }
                }
            echo '</ul>';
        echo '</div>';
        // END Left bar
        // Content
        echo '<div class="flex-initial w-3/4">';
        echo '<ul>';
        foreach ($tree as $file) {
                $path = $file['type'] === 'directory' ? "?path={$file['path']}" : "?path={$file['path']}&action=edit";
                //TODO: make the application work with /my_H5AI/anotherDirectory to display directory inside.
                //TODO: instead of parameter ?path=/Users/jud3v/Project/H5AI/my_H5AI/anotherDirectory
                //$path = str_replace("?path=/Users/jud3v/Project/H5AI/my_H5AI/", "", $path);
                echo "<a href='$path'>";
                    echo '<li class="px-4 py-1.5 m-4 border relative w-full rounded-lg">';
                    echo '<div class="flex justify-between items-center w-full">';
                    if($file['type'] == "file"){
                            // here if no extension, it will be considered as txt file
                            $mime = explode('.', $file['name'])[1] ?? 'txt';
                            echo match ($mime){
                                    'pdf' => '<i class="fas fa-file-pdf"></i>',
                                    'php' => '<span class="material-symbols-outlined">php</span>',
                                    'js' => '<span class="material-symbols-outlined">javascript</span>',
                                    'html' => '<span class="material-symbols-outlined">html</span>',
                                    'css' => '<span class="material-symbols-outlined">css</span>',
                                    'py' => '<span class="material-symbols-outlined">code</span>',
                                    'rs', 'json', 'go','c' => '<i class="fa-solid fa-code"></i>',
                                    default => '<i class="fas fa-file"></i>',
                            };
                    } else {
                            echo '<i class="fas fa-folder"></i>';
                    }

                    echo '<span class="text-sm font-bold">';
                    echo $file['name'];
                    echo '</span>';

                    echo '<span class="text-sm">';
                    echo date("d-m-Y H:i:s", $file['updated_at']);
                    echo ' ';
                    echo 'size: ' . round($file['size'] / 1024, 2) . 'KB';
                    echo '</span>';
                    echo '</div>';
                        echo '<div class="">';
                            echo '<ul class="flex">';
                                foreach ($file['tags'] as $tag) {
                                        echo "<li class='px-2 py-0.5 cursor-pointer bg-blue-500 text-white rounded-lg mr-2 text-sm' id='".$file['path']."&tag_name=".$tag['tag_name']."'>";
                                        echo $tag['tag_name'];
                                        echo "</li>";
                                        // make a modal to delete tag or update tag
                                        echo "<div class='hidden absolute bg-white border rounded-lg p-2' id='".$file['path']."&tag_name=".$tag['tag_name']."&modal'>";
                                        echo "<a href='?path=" . dirname($file['path']) . "&action=delete_tag&tag_name=".$tag['tag_name']."' class='text-sm bg-red-500 text-white p-2 rounded-lg ml-2'>Delete</a>";
                                        echo "</div>";
                                }
                echo "<a href='{$path}&action=create_tag' class='text-blue-500 cursor-pointer text-sm'>Add Tag</a>";

                echo '</ul>';
                        echo '</div>';
                    echo "</li>";
                echo "</a>";
        }
        echo '</ul>';
        echo '</div>';
        echo "</div>";
        // END content
}

/**
 * This will show the action button with filter
 * @return void
 */
function actionButtonWithFilter() : void
{
        echo "<div class='flex justify-between items-center'>";
        echo "<div class='flex'>";
        echo "<button id='back' class='text-sm bg-blue-500 text-white p-2 rounded-lg'><i class='fa-solid fa-arrow-left'></i> Back</button>";
        echo "<button id='forward' class='text-sm bg-blue-500 text-white p-2 rounded-lg ml-2'>Forward <i class='fa-solid fa-arrow-right'></i></button>";
        echo "</div>";
        echo "<div class='flex'>";
        echo "<form action='' method='get'>";
        echo "<input type='text' class='border p-2 rounded-lg' name='search' placeholder='Search...'>";
        echo "<button type='submit' class='text-sm bg-green-500 text-white p-2 rounded-lg ml-2'>Search <i class='fa-solid fa-magnifying-glass'></i></button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
}

/**
 * This will show the sort button
 * @return void
 */
function sortButton() : void
{
        echo "<div class='flex justify-between items-center'>";
        echo "<div class='flex'>";
        echo "<button id='sortByName' class='text-sm bg-blue-500 text-white p-2 rounded-lg'>Sort by Name</button>";
        echo "<button id='sortByDate' class='text-sm bg-blue-500 text-white p-2 rounded-lg ml-2'>Sort by Date</button>";
        echo "<button id='sortBySize' class='text-sm bg-blue-500 text-white p-2 rounded-lg ml-2'>Sort by Size</button>";
        echo "<button id='resetFilter' class='text-sm bg-red-500 text-white p-2 rounded-lg ml-2'>Reset Filter</button>";
        echo "</div>";
        echo "<div class='flex'>";
        echo "<button id='sortByAsc' class='text-sm bg-blue-500 text-white p-2 rounded-lg'><i class='fa-solid fa-chevron-up'></i> Ascending</button>";
        echo "<button id='sortByDesc' class='text-sm bg-blue-500 text-white p-2 rounded-lg ml-2'><i class='fa-solid fa-chevron-down'></i> Descending</button>";
        echo "</div>";
        echo "</div>";
}

// show editor when action is edit or show file explorer
if(isset($_POST['tag'])) {
    require_once "../Model/Tag.php";
    $tag = new Tag();
        // handle post of create tag
        $tags = [
                'path' => $_GET['path'],
                'tag_name' => $_POST['tag']
        ];

        $tag->create($tags);

        header("Location: ?path=" . dirname($_GET['path']));

} elseif(isset($_POST['content'])) {
        // handle post of update files
        file_put_contents($_GET['path'], $_POST['content']);
        header("Location: ?path=" . dirname($_GET['path']));
} elseif(isset($_GET['action']) && $_GET['action'] === 'delete_tag') {
        require_once "../Model/Tag.php";
        $tag = new Tag();
        $tag->deleteTag($_GET['tag_name']);
        header("Location: ?path=" . dirname($_GET['path']));
} elseif(isset($_GET['path']) && isset($_GET['action']) && $_GET['action'] === 'delete') {
        // handle delete file
        unlink($_GET['path']);
        header("Location: ?path=" . dirname($_GET['path']));
} elseif (isset($_GET['action']) && $_GET['action'] === 'edit'){
        echo "<div class='container mx-auto mt-5'>";
        echo "<h1 class='text-3xl font-bold'>Edit file</h1>";
        echo "<hr class='my-5'>";
        echo "<form action='' method='post'>";
        echo "<textarea name='content' class='border p-2 rounded-lg w-full h-96'>";
        echo file_get_contents($_GET['path']);
        echo "</textarea>";
        echo "<button type='submit' class='text-sm bg-blue-500 text-white p-2 rounded-lg mt-2'>Save</button>";
        echo "<a href='?path=" . dirname($_GET['path']) . "' class='text-sm bg-red-500 text-white p-2 rounded-lg ml-2'>Cancel</a>";
        echo "</form>";
        echo "</div>";
} elseif(isset($_GET['action']) && $_GET['action'] === 'create_tag') {
        echo "<div class='container mx-auto mt-5'>";
        echo "<h1 class='text-3xl font-bold'>Create Tag for file: ".str_replace($_SERVER['DOCUMENT_ROOT'].'/my_H5AI/', '', $_GET['path'])."</h1>";
        echo "<hr class='my-5'>";
        echo "<form action='' method='post'>";
        echo "<input type='text' placeholder='Enter the name of the new tag' name='tag' class='border p-2 rounded-lg w-full'>";
        echo "<button type='submit' class='text-sm bg-blue-500 text-white p-2 rounded-lg mt-2'>Save</button>";
        echo "<a href='?path=" . dirname($_GET['path']) . "' class='text-sm bg-red-500 text-white p-2 rounded-lg ml-2'>Cancel</a>";
        echo "</form>";
        echo "</div>";
}  else {
        // Default view
        echo "<div class='container mx-auto mt-5'>";
        my_header();
        echo "<hr class='my-5'>";
        actionButtonWithFilter();
        echo "<hr class='my-5'>";
        sortButton();
        echo "<hr class='my-5'>";
        my_body();
        echo "</div>";
}

// load js when php send the response
echo "<script src='/script/index.js' type='application/javascript'></script>"
?>
</body>
</html>