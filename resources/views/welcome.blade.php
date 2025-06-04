<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Project</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between">
                <div class="flex space-x-7">
                    <div>
                        <a href="/" class="flex items-center py-4">
                            <span class="font-semibold text-gray-500 text-lg">Shipping Project</span>
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="/shipping" class="py-2 px-4 text-gray-500 hover:text-gray-700">Shipping</a>
                    <a href="/shipping/tracking" class="py-2 px-4 text-gray-500 hover:text-gray-700">Tracking</a>
                    <a href="/shipping/rates" class="py-2 px-4 text-gray-500 hover:text-gray-700">Rates</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to Shipping Project</h1>
            <p class="text-xl text-gray-600 mb-8">Your complete shipping solution</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Track Shipments</h2>
                    <p class="text-gray-600 mb-4">Track your shipments in real-time</p>
                    <a href="/shipping/tracking" class="text-blue-500 hover:text-blue-700">Track Now →</a>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Calculate Rates</h2>
                    <p class="text-gray-600 mb-4">Get instant shipping rate quotes</p>
                    <a href="/shipping/rates" class="text-blue-500 hover:text-blue-700">Calculate →</a>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Shipping Services</h2>
                    <p class="text-gray-600 mb-4">Explore our shipping solutions</p>
                    <a href="/shipping" class="text-blue-500 hover:text-blue-700">Learn More →</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
