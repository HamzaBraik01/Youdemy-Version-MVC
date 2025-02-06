<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Youdemy - Learning Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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

                <div class="flex items-center space-x-6">
                    <a href="course.php" class="text-gray-300 hover:text-white font-medium transition-colors">
                        Courses
                    </a>
                    <a href="login.php" class="text-gray-300 hover:text-white font-medium transition-colors">
                        Login
                    </a>
                    <a href="register.php" class="bg-white text-gray-900 px-6 py-3 rounded-full hover:bg-gray-100 transition-colors font-medium">
                        Get Started
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow container mx-auto px-4 py-12">
        <div class="space-y-24">
            <section class="text-center space-y-8 max-w-4xl mx-auto">
            <span class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-full text-sm font-medium">Transform Your Future</span>
            <h1 class="text-6xl font-bold text-gray-900 leading-tight">
                Learn Without Limits
            </h1>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto leading-relaxed">
                Join millions of learners worldwide in accessing high-quality courses from expert instructors. Start your learning journey today.
            </p>
            <div class="flex justify-center gap-4 pt-4">
                <a href="course.php" class="bg-indigo-600 text-white px-8 py-4 rounded-full hover:bg-indigo-700 transition-colors font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                Explore Courses
                </a>
                <a href="/register.html" class="bg-white text-indigo-600 px-8 py-4 rounded-full border-2 border-indigo-600 hover:bg-indigo-50 transition-colors font-medium">
                Become an Instructor
                </a>
            </div>
            </section>

            <section class="grid md:grid-cols-3 gap-12">
            <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                <div class="bg-indigo-50 p-3 rounded-2xl inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
                </div>
                <h3 class="text-xl font-semibold mt-6 mb-4">Expert-Led Courses</h3>
                <p class="text-gray-600 leading-relaxed">
                Learn from industry professionals and experienced educators who bring real-world expertise to every lesson.
                </p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                <div class="bg-indigo-50 p-3 rounded-2xl inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                </div>
                <h3 class="text-xl font-semibold mt-6 mb-4">Global Community</h3>
                <p class="text-gray-600 leading-relaxed">
                Join a diverse community of learners and educators from around the world, sharing knowledge and experiences.
                </p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-shadow">
                <div class="bg-indigo-50 p-3 rounded-2xl inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
                </div>
                <h3 class="text-xl font-semibold mt-6 mb-4">Interactive Learning</h3>
                <p class="text-gray-600 leading-relaxed">
                Engage with dynamic content, hands-on projects, and interactive assessments designed for optimal learning.
                </p>
            </div>
            </section>
        </div>
    </main>

    <footer class="bg-gray-900 text-gray-300 py-12 mt-24">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div>
                <h4 class="text-white font-semibold text-lg mb-4">About</h4>
                <ul class="space-y-2">
                <li><a href="#" class="hover:text-white transition-colors">About Us</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Press</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Blog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold text-lg mb-4">Solutions</h4>
                <ul class="space-y-2">
                <li><a href="#" class="hover:text-white transition-colors">For Students</a></li>
                <li><a href="#" class="hover:text-white transition-colors">For Teachers</a></li>
                <li><a href="#" class="hover:text-white transition-colors">For Business</a></li>
                <li><a href="#" class="hover:text-white transition-colors">For Schools</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold text-lg mb-4">Resources</h4>
                <ul class="space-y-2">
                <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Cookie Settings</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold text-lg mb-4">Contact</h4>
                <ul class="space-y-2">
                <li><a href="#" class="hover:text-white transition-colors">Contact Us</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Support</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Partners</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Feedback</a></li>
                </ul>
            </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center space-x-3 mb-4 md:mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
                <span class="text-xl font-bold text-white">Youdemy</span>
            </div>
            <p class="text-gray-500 text-sm">© 2024 Youdemy. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>