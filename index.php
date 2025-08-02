<?php
require_once 'classes/Auth.php';

// Redirect if already logged in
if (Auth::isLoggedIn()) {
    Auth::redirectBasedOnRole();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Project - Course Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#1e40af'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-16">
        <div class="text-center mb-12">
            <h1 class="text-5xl font-bold text-gray-800 mb-4">Course Management System</h1>
            <p class="text-xl text-gray-600 mb-8">Streamline course registration and management</p>
        </div>

        <div class="max-w-4xl mx-auto">
            <!-- Hero Section -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-12 text-center">
                    <h2 class="text-3xl font-bold text-white mb-4">Welcome to Our Platform</h2>
                    <p class="text-blue-100 text-lg">Connect students with courses and teachers with ease</p>
                </div>

                <div class="p-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Student Section -->
                        <div class="text-center">
                            <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-green-600 text-2xl">🎓</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">For Students</h3>
                            <ul class="text-gray-600 space-y-2 mb-6">
                                <li>• Browse available courses</li>
                                <li>• Register for classes</li>
                                <li>• Track registration status</li>
                                <li>• View course details</li>
                            </ul>
                            <div class="space-y-3">
                                <a href="login.php"
                                   class="block bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition duration-200">
                                    Student Login
                                </a>
                                <a href="register.php"
                                   class="block border border-green-600 text-green-600 py-3 px-6 rounded-lg font-semibold hover:bg-green-50 transition duration-200">
                                    Register as Student
                                </a>
                            </div>
                        </div>

                        <!-- Teacher Section -->
                        <div class="text-center">
                            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-blue-600 text-2xl">👨‍🏫</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">For Teachers</h3>
                            <ul class="text-gray-600 space-y-2 mb-6">
                                <li>• View student registrations</li>
                                <li>• Approve or suggest courses</li>
                                <li>• Manage enrollment</li>
                                <li>• Track student progress</li>
                            </ul>
                            <div class="space-y-3">
                                <a href="login.php"
                                   class="block bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                                    Teacher Login
                                </a>
                                <a href="register.php"
                                   class="block border border-blue-600 text-blue-600 py-3 px-6 rounded-lg font-semibold hover:bg-blue-50 transition duration-200">
                                    Register as Teacher
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Demo Credentials -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Demo Credentials</h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-medium text-green-800 mb-2">Student Account</h4>
                        <p class="text-sm text-green-700">Username: <code class="bg-green-100 px-2 py-1 rounded">student1</code></p>
                        <p class="text-sm text-green-700">Password: <code class="bg-green-100 px-2 py-1 rounded">password</code></p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-medium text-blue-800 mb-2">Teacher Account</h4>
                        <p class="text-sm text-blue-700">Username: <code class="bg-blue-100 px-2 py-1 rounded">john_teacher</code></p>
                        <p class="text-sm text-blue-700">Password: <code class="bg-blue-100 px-2 py-1 rounded">password</code></p>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">System Features</h3>
                <div class="grid md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="bg-purple-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-purple-600 text-xl">🔐</span>
                        </div>
                        <h4 class="font-medium text-gray-800 mb-2">Secure Authentication</h4>
                        <p class="text-sm text-gray-600">Role-based access with session management</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-yellow-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-yellow-600 text-xl">📊</span>
                        </div>
                        <h4 class="font-medium text-gray-800 mb-2">CRUD Operations</h4>
                        <p class="text-sm text-gray-600">Full database operations with raw SQL</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-pink-100 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                            <span class="text-pink-600 text-xl">🎨</span>
                        </div>
                        <h4 class="font-medium text-gray-800 mb-2">Modern UI</h4>
                        <p class="text-sm text-gray-600">Clean, responsive design with Tailwind CSS</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
