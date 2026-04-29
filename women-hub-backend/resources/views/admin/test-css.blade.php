<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #F8FAFE;
            font-family: system-ui, 'Segoe UI', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        .test-card {
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(0,0,0,0.03), 0 2px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .test-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px -12px rgba(0, 0, 0, 0.15);
        }
        .sidebar-test {
            background: #874179;
            border-right: 1px solid #6d3661;
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="flex h-screen">
        <!-- Test Sidebar -->
        <div class="w-64 sidebar-test p-6">
            <div class="text-white">
                <h1 class="text-2xl font-bold">Tithandizane</h1>
                <p class="text-xs opacity-90">Women Hub</p>
            </div>
            <nav class="mt-6 space-y-2">
                <a href="#" class="flex items-center px-4 py-3 rounded-lg text-white bg-blue-600">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                <a href="#" class="flex items-center px-4 py-3 rounded-lg text-gray-200 hover:bg-blue-700">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3">Users</span>
                </a>
            </nav>
        </div>
        
        <!-- Test Content -->
        <div class="flex-1 p-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">CSS Test Page</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="test-card p-6 rounded-2xl border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Mentors</p>
                            <p class="text-3xl font-bold text-gray-900">24</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-chalkboard-user text-2xl text-green-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="test-card p-6 rounded-2xl border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Users</p>
                            <p class="text-3xl font-bold text-gray-900">1,245</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                    </div>
                </div>
                
                <div class="test-card p-6 rounded-2xl border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Reports</p>
                            <p class="text-3xl font-bold text-gray-900">8</p>
                        </div>
                        <div class="p-3 bg-purple-100 rounded-full">
                            <i class="fas fa-flag text-2xl text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 test-card p-6 rounded-2xl">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Test Elements</h3>
                <div class="space-y-4">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i>Add New
                    </button>
                    <input type="text" placeholder="Search..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <div class="flex gap-2">
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">Active</span>
                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">Pending</span>
                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">Urgent</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        console.log('CSS Test Page Loaded Successfully');
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Ready - All styles should be applied');
        });
    </script>
</body>
</html>
