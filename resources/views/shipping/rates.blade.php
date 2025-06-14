<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Rates - Shipping Project</title>
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
                    <a href="/shipping/rates" class="py-2 px-4 text-blue-500 hover:text-blue-700">Rates</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Calculate Shipping Rates</h1>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <form class="space-y-6" action="{{ route('orders.calculate-shipping') }}" method="post">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="from_country" class="block text-sm font-medium text-gray-700 mb-2">From Country</label>
                            <select id="from_country" name="from_country" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Country</option>
                                <option value="US">United States</option>
                                <option value="CA">Canada</option>
                                <option value="UK">United Kingdom</option>
                                <option value="DE">Germany</option>
                                <option value="FR">France</option>
                            </select>
                        </div>
                        <div>
                            <label for="to_country" class="block text-sm font-medium text-gray-700 mb-2">To Country</label>
                            <select id="to_country" name="to_country" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Country</option>
                                <option value="US">United States</option>
                                <option value="CA">Canada</option>
                                <option value="UK">United Kingdom</option>
                                <option value="DE">Germany</option>
                                <option value="FR">France</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                            <input type="number" id="weight" name="weight" step="0.1" min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter weight">
                        </div>
                        <div>
                            <label for="length" class="block text-sm font-medium text-gray-700 mb-2">Length (cm)</label>
                            <input type="number" id="length" name="length" step="0.1" min="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter length">
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

                    <button type="submit" 
                            class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Calculate Rate
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Available Shipping Services</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Standard Shipping</h3>
                        <p class="text-sm text-gray-500 mb-4">3-5 business days</p>
                        <p class="text-2xl font-bold text-blue-500">$15.99</p>
                        <p class="text-xs text-gray-400">Starting from</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Express Shipping</h3>
                        <p class="text-sm text-gray-500 mb-4">1-2 business days</p>
                        <p class="text-2xl font-bold text-blue-500">$29.99</p>
                        <p class="text-xs text-gray-400">Starting from</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Priority Shipping</h3>
                        <p class="text-sm text-gray-500 mb-4">Next business day</p>
                        <p class="text-2xl font-bold text-blue-500">$49.99</p>
                        <p class="text-xs text-gray-400">Starting from</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 