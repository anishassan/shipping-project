<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping - Shipping Project</title>
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
                    <a href="/shipping" class="py-2 px-4 text-blue-500 hover:text-blue-700">Shipping</a>
                    <a href="/shipping/tracking" class="py-2 px-4 text-gray-500 hover:text-gray-700">Tracking</a>
                    <a href="/shipping/rates" class="py-2 px-4 text-gray-500 hover:text-gray-700">Rates</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Create New Shipment</h1>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Sender Information</h2>
                            <div class="space-y-4">
                                <div>
                                    <label for="sender_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" id="sender_name" name="sender_name" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Enter sender's name">
                                </div>
                                <div>
                                    <label for="sender_address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <textarea id="sender_address" name="sender_address" rows="3"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Enter sender's address"></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="sender_city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                        <input type="text" id="sender_city" name="sender_city" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="Enter city">
                                    </div>
                                    <div>
                                        <label for="sender_postal" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                        <input type="text" id="sender_postal" name="sender_postal" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="Enter postal code">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Recipient Information</h2>
                            <div class="space-y-4">
                                <div>
                                    <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" id="recipient_name" name="recipient_name" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Enter recipient's name">
                                </div>
                                <div>
                                    <label for="recipient_address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                    <textarea id="recipient_address" name="recipient_address" rows="3"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                              placeholder="Enter recipient's address"></textarea>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="recipient_city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                        <input type="text" id="recipient_city" name="recipient_city" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="Enter city">
                                    </div>
                                    <div>
                                        <label for="recipient_postal" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                        <input type="text" id="recipient_postal" name="recipient_postal" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               placeholder="Enter postal code">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Package Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                                <input type="number" id="weight" name="weight" step="0.1" min="0"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Enter weight">
                            </div>
                            <div>
                                <label for="dimensions" class="block text-sm font-medium text-gray-700 mb-2">Dimensions (L x W x H cm)</label>
                                <div class="grid grid-cols-3 gap-2">
                                    <input type="number" name="length" step="0.1" min="0"
                                           class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="L">
                                    <input type="number" name="width" step="0.1" min="0"
                                           class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="W">
                                    <input type="number" name="height" step="0.1" min="0"
                                           class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="H">
                                </div>
                            </div>
                            <div>
                                <label for="service" class="block text-sm font-medium text-gray-700 mb-2">Service Type</label>
                                <select id="service" name="service" 
                                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Service</option>
                                    <option value="standard">Standard Shipping</option>
                                    <option value="express">Express Shipping</option>
                                    <option value="priority">Priority Shipping</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-blue-500 text-white py-2 px-6 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Create Shipment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 