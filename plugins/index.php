<!DOCTYPE html>
<html>
<head>
    <title>Plugins</title>
    <script src="../assets/js/jquery.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <h2 class="text-gray-500 px-8 pt-8 text-2xl">Installed Apps</h2>
    
    <div class="p-4 md:p-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <?php 
            $d = dir(".");
            while (false !== ($entry = $d->read())) {
                if (is_dir($entry) && ($entry != '.') && ($entry != '..')) {
                    if($entry){
                        $dir_name = $entry.'/';
                        $images = glob($dir_name."*.png");
                        foreach($images as $image) {
                            $tu = explode("/",$image);
                            $nk = preg_replace('/\\.[^.\\s]{3,4}$/', '', $tu[1]);
                            $manp = './'.$dir_name;

                            // Get the 'source' and 'field' parameters from the URL (if they exist)
                            $source = isset($_GET['source']) ? $_GET['source'] : '';
                            $field = isset($_GET['field']) ? $_GET['field'] : '';
                            
                            // Create the URL with the additional parameters
                            $url = $manp . "?source=" . urlencode($source) . "&field=" . urlencode($field);
                            ?>
                            <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300 p-6">
                                <a href="<?php echo $url; ?>" class="flex flex-col items-center">
                                    <img src="<?php echo $image;?>" class="w-32 h-32 object-contain mb-4">
                                    <span class="text-gray-700 text-lg"><?php echo $nk?></span>
                                </a>
                            </div>
                            <?php
                        }
                    }
                }
            }
            $d->close();
            ?>
        </div>
    </div>

    <script>
        $(document).ready(function() { 
            var height = Math.max(
                document.body.scrollHeight, 
                document.body.offsetHeight,
                document.documentElement.clientHeight, 
                document.documentElement.scrollHeight, 
                document.documentElement.offsetHeight
            );
            window.top.postMessage(height+300, '*');
        });
    </script>
</body>
</html>
