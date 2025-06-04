<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Shipment - Shipping Project</title>
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
                    <a href="/shipping/tracking" class="py-2 px-4 text-blue-500 hover:text-blue-700">Tracking</a>
                    <a href="/shipping/rates" class="py-2 px-4 text-gray-500 hover:text-gray-700">Rates</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Track Your Shipment</h1>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <form class="space-y-4">
                    <div>
                        <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">Tracking Number</label>
                        <input type="text" id="tracking_number" name="tracking_number" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter your tracking number">
                    </div>
                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Track Shipment
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Tracking Information</h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Package Delivered</p>
                            <p class="text-sm text-gray-500">Delivered to recipient</p>
                            <p class="text-xs text-gray-400">March 15, 2024 - 2:30 PM</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Out for Delivery</p>
                            <p class="text-sm text-gray-500">Package is out for delivery</p>
                            <p class="text-xs text-gray-400">March 15, 2024 - 8:15 AM</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Arrived at Local Facility</p>
                            <p class="text-sm text-gray-500">Package arrived at local delivery facility</p>
                            <p class="text-xs text-gray-400">March 14, 2024 - 11:45 PM</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">In Transit</p>
                            <p class="text-sm text-gray-500">Package is in transit to destination</p>
                            <p class="text-xs text-gray-400">March 14, 2024 - 3:20 PM</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">Package Picked Up</p>
                            <p class="text-sm text-gray-500">Package has been picked up by carrier</p>
                            <p class="text-xs text-gray-400">March 13, 2024 - 9:30 AM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 