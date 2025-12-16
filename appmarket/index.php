<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plugin Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

    <!-- Header -->
    <header class="bg-gradient-to-r from-indigo-600 to-purple-700 py-6 px-8 shadow-md">
        <div class="container mx-auto flex items-center justify-between">
            <h1 class="text-4xl font-semibold text-white tracking-tight">
                Plugin Market
            </h1>
        </div>
        <!-- Centered Search Bar -->
        <div class="container mx-auto mt-4 flex justify-center">
            <div class="relative w-full max-w-md">
                <input type="text" id="pluginSearch" placeholder="Search plugins..." class="w-full py-3 px-6 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button class="absolute right-3 top-3 text-indigo-600 hover:text-indigo-800 transition duration-300">
                    <span role="img" aria-label="search">🔍</span>
                </button>
            </div>
        </div>
        <div class="container mx-auto mt-4 text-center">
            <p class="text-white text-lg max-w-lg mx-auto">
                Enhance your ERP with powerful plugins. Start exploring now!
            </p>
        </div>
    </header>

    <!-- Plugin Listings -->
    <main class="container mx-auto py-12 px-4">
        <h2 class="text-3xl font-semibold mb-8 text-gray-800 text-center">
            Available Plugins
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8" id="pluginList">
            <!-- PHP-generated Plugin Cards go here -->
            <?php
$plugin_dir = "./";
if (is_dir($plugin_dir)) {
    $plugins = scandir($plugin_dir);
    foreach ($plugins as $plugin) {
        if ($plugin != "." && $plugin != ".." && is_dir($plugin_dir . $plugin)) {
            // Initialize logo with default value
            $logo = "https://icons.iconarchive.com/icons/webalys/kameleon.pics/512/Vector-icon.png";
            $logo_file = $plugin_dir . $plugin . "/logo.png";
            
            // Scan for any image file
            $image_extensions = ['png', 'jpg', 'jpeg'];
            $plugin_files = scandir($plugin_dir . $plugin);
            foreach ($plugin_files as $file) {
                $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($file_ext, $image_extensions)) {
                    $logo = $plugin_dir . $plugin . "/" . $file;
                    $logo_file = $logo;
                    break;
                }
            }

            $readme_file = $plugin_dir . $plugin . "/readme.txt";
            $zip_file = $plugin . ".zip";
            
            $description = "No description available";
            if(file_exists($readme_file)) {
                $content = file_get_contents($readme_file);
                $lines = explode("\n", $content);
                $description = implode(" ", array_slice($lines, 0, 3));
            }
            
            $size = "N/A";
            if(file_exists($zip_file)) {
                $bytes = filesize($zip_file);
                if($bytes > 0) {
                    $size = round($bytes / (1024 * 1024), 2) . ' MB';
                }
            }

            echo "<div class='bg-white rounded-lg shadow-xl transform hover:scale-105 transition duration-300 hover:shadow-2xl plugin-card'>
                <img src='{$logo}' alt='{$plugin}' class='w-full h-48 object-contain rounded-t-lg p-4'>
                <div class='p-6'>
                    <h3 class='text-xl font-semibold text-gray-800 mb-2'>{$plugin}</h3>
                    <p class='text-gray-600 text-sm mb-4'>{$description}</p>
                    <div class='flex items-center justify-between'>
                        <div class='flex items-center'>
                            <span class='text-yellow-500 text-lg'>⭐⭐⭐⭐⭐</span>
                        </div>
                        <span class='text-gray-500 text-sm'>{$size}</span>
                    </div>
                    <a href='./install?q={$plugin}' class='mt-4 block w-full text-center bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition duration-300'>Install Now</a>
                </div>
            </div>";
        }
    }
}
?>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 px-4 mt-12">
        <div class="container mx-auto flex justify-between items-center">
            <p class="text-sm">
                &copy; 2024 Plugin Market. All rights reserved.
            </p>
            <div class="space-x-6">
                <a href="#" class="hover:text-gray-300">Privacy Policy</a>
                <a href="#" class="hover:text-gray-300">Terms of Service</a>
                <a href="#" class="hover:text-gray-300">Contact Us</a>
            </div>
        </div>
    </footer>

    <script>
        // Search functionality
        document.getElementById('pluginSearch').addEventListener('keyup', function () {
            let searchValue = this.value.toLowerCase();
            let pluginCards = document.querySelectorAll('.plugin-card');

            pluginCards.forEach(card => {
                let title = card.querySelector('h3').textContent.toLowerCase();
                let description = card.querySelector('p').textContent.toLowerCase();

                // Show card if title or description contains the search value
                if (title.includes(searchValue) || description.includes(searchValue)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>
