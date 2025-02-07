<?php
session_start();
require_once '../../Controllers/AuthController.php';

$authController = new AuthController();
$authController->login();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - Youdemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
        font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-b from-gray-50 to-white min-h-screen flex flex-col">
    <nav class="bg-gray-900 border-b border-gray-800">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center h-20">
                    <a href="index.php" class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                        <span class="text-2xl font-bold text-white">Youdemy</span>
                    </a>
                </div>
            </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-12">
        <div class="max-w-md mx-auto">
            <div class="bg-white p-8 rounded-2xl shadow-lg">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Welcome back</h2>
                    <p class="text-gray-600 mt-2">Please enter your details to sign in</p>
                </div>
                <form class="space-y-6" action="" method="POST">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                        Email address
                        </label>
                        <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors"
                        placeholder="Enter your email"
                        />
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                        Password
                        </label>
                        <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors"
                        placeholder="Enter your password"
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full flex justify-center py-3 px-4 rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors font-medium"
                    >
                        Sign in
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
