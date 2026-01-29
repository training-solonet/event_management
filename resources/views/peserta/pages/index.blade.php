
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EventHub - Platform Pendaftaran Event Premium</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- QR Code Generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    
    <!-- html2canvas -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    
    <style>
        :root {
            --primary: #1a365d;
            --secondary: #c5a572;
            --accent: #2d3748;
            --light: #f8f9fa;
            --dark: #1a202c;
            --success: #38a169;
            --warning: #d69e2e;
            --danger: #e53e3e;
        }
        
        .font-heading {
            font-family: 'Playfair Display', serif;
        }
        
        .font-body {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
            overflow-x: hidden;
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .qr-code-placeholder {
            width: 160px;
            height: 160px;
            background: linear-gradient(45deg, #f3f4f6 25%, transparent 25%), 
                        linear-gradient(-45deg, #f3f4f6 25%, transparent 25%), 
                        linear-gradient(45deg, transparent 75%, #f3f4f6 75%), 
                        linear-gradient(-45deg, transparent 75%, #f3f4f6 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .qr-code-img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--secondary);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #b89452;
        }
        
        /* Dark mode scrollbar */
        .dark ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .dark ::-webkit-scrollbar-thumb {
            background: #b89452;
        }
        
        /* Glass effect */
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .dark .glass {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        /* Gradient backgrounds */
        .gradient-primary {
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
        }
        
        .gradient-secondary {
            background: linear-gradient(135deg, #c5a572 0%, #d4b483 100%);
        }
        
        .gradient-accent {
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
        }
        
        /* Event type gradients */
        .gradient-online {
            background: linear-gradient(135deg, #3182ce 0%, #4c7cb8 100%);
        }
        
        .gradient-offline {
            background: linear-gradient(135deg, #805ad5 0%, #6b46c1 100%);
        }
        
        .gradient-hybrid {
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        }
        
        /* Shadow effects */
        .shadow-soft {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        .dark .shadow-soft {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }
        
        .shadow-elegant {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }
        
        .dark .shadow-elegant {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
        }
        
        /* Card hover effects */
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }
        
        .dark .card-hover:hover {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        }
        
        /* Button effects */
        .btn-gold {
            background: linear-gradient(135deg, #c5a572 0%, #d4b483 100%);
            color: #1a202c;
            transition: all 0.3s ease;
        }
        
        .btn-gold:hover {
            background: linear-gradient(135deg, #d4b483 0%, #e6c896 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(197, 165, 114, 0.3);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2d3748 0%, #3c4a5f 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(26, 54, 93, 0.3);
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        
        @keyframes slideInLeft {
            from { transform: translateX(-100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideInRight {
            from { transform: translateX(100px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        .animate-pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        .animate-slideInLeft {
            animation: slideInLeft 0.8s ease-out forwards;
        }
        
        .animate-slideInRight {
            animation: slideInRight 0.8s ease-out forwards;
        }
        
        /* Badge styles */
        .badge-gold {
            background: rgba(197, 165, 114, 0.15);
            color: #c5a572;
            border: 1px solid rgba(197, 165, 114, 0.3);
        }
        
        /* Form styles */
        .form-input {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .dark .form-input {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .form-input:focus {
            border-color: #c5a572;
            box-shadow: 0 0 0 3px rgba(197, 165, 114, 0.2);
        }
        
        /* Theme toggle */
        .theme-toggle {
            position: relative;
            width: 60px;
            height: 30px;
            border-radius: 15px;
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 100;
        }
        
        .theme-toggle::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #c5a572;
            transition: transform 0.3s ease;
        }
        
        .dark .theme-toggle::after {
            transform: translateX(30px);
        }
        
        /* Hero text improvements */
        .hero-text {
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .dark .hero-text {
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }
        
        /* Background decorative elements */
        .bg-decorative {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.15;
            z-index: 0;
        }
        
        .bg-decorative-1 {
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #c5a572 0%, #805ad5 100%);
            top: 10%;
            left: 5%;
            animation: float 15s ease-in-out infinite;
        }
        
        .bg-decorative-2 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #3182ce 0%, #38a169 100%);
            bottom: 15%;
            right: 10%;
            animation: float 18s ease-in-out infinite reverse;
        }
        
        .bg-decorative-3 {
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, #d4b483 0%, #e53e3e 100%);
            top: 60%;
            left: 15%;
            animation: float 12s ease-in-out infinite;
        }
        
        /* Geometric shapes */
        .geometric-shape {
            position: absolute;
            opacity: 0.05;
            z-index: 0;
        }
        
        .triangle {
            width: 0;
            height: 0;
            border-left: 50px solid transparent;
            border-right: 50px solid transparent;
            border-bottom: 100px solid #c5a572;
        }
        
        .circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #3182ce;
        }
        
        .square {
            width: 100px;
            height: 100px;
            background: #805ad5;
            transform: rotate(45deg);
        }
        
        /* Particle animation */
        @keyframes particleFloat {
            0% { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100vh) translateX(100px) rotate(360deg); opacity: 0; }
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: rgba(197, 165, 114, 0.6);
            z-index: 0;
        }
        
        /* Text highlight */
        .text-highlight {
            position: relative;
            display: inline-block;
        }
        
        .text-highlight::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 30%;
            background: rgba(197, 165, 114, 0.2);
            z-index: -1;
            transform: skew(-15deg);
        }
        
        /* Glitch effect for hero title */
        @keyframes glitch {
            0% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
            100% { transform: translate(0); }
        }
        
        .glitch-effect {
            position: relative;
        }
        
        .glitch-effect::before,
        .glitch-effect::after {
            content: attr(data-text);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.8;
        }
        
        .glitch-effect::before {
            color: #805ad5;
            animation: glitch 0.3s infinite;
            z-index: -1;
        }
        
        .glitch-effect::after {
            color: #3182ce;
            animation: glitch 0.3s infinite reverse;
            z-index: -2;
        }
        
        /* Shimmer effect */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        
        .shimmer-effect {
            background: linear-gradient(90deg, 
                rgba(255,255,255,0) 0%, 
                rgba(255,255,255,0.1) 50%, 
                rgba(255,255,255,0) 100%);
            background-size: 1000px 100%;
            animation: shimmer 3s infinite;
        }
        
        /* Typewriter effect */
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        
        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: #c5a572 }
        }
        
        .typewriter {
            overflow: hidden;
            border-right: .15em solid #c5a572;
            white-space: nowrap;
            animation: 
                typing 3.5s steps(40, end),
                blink-caret .75s step-end infinite;
        }
        
        /* Floating action button */
        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c5a572 0%, #d4b483 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a202c;
            font-size: 24px;
            box-shadow: 0 8px 30px rgba(197, 165, 114, 0.4);
            cursor: pointer;
            z-index: 100;
            transition: all 0.3s ease;
            animation: pulse-glow 2s infinite;
        }
        
        .floating-btn:hover {
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 12px 40px rgba(197, 165, 114, 0.6);
        }
        
        /* Neon border effect */
        .neon-border {
            position: relative;
            border: 2px solid transparent;
            background-clip: padding-box;
        }
        
        .neon-border::after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #c5a572, #805ad5, #3182ce, #38a169);
            border-radius: inherit;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .neon-border:hover::after {
            opacity: 1;
            animation: pulse-glow 1.5s infinite;
        }
    </style>
</head>
<body class="font-body bg-gray-50 text-gray-800 dark:bg-gray-900 dark:text-gray-200 transition-colors duration-300">
    <!-- Floating Action Button -->
    <div class="floating-btn" id="scrollToTop">
        <i class="fas fa-arrow-up"></i>
    </div>
    
    <!-- Background Decorative Elements -->
    <div class="bg-decorative bg-decorative-1"></div>
    <div class="bg-decorative bg-decorative-2"></div>
    <div class="bg-decorative bg-decorative-3"></div>
    
    <!-- Geometric Shapes -->
    <div class="geometric-shape triangle" style="top: 15%; right: 15%;"></div>
    <div class="geometric-shape circle" style="top: 70%; left: 5%;"></div>
    <div class="geometric-shape square" style="top: 20%; right: 5%;"></div>
    
    <!-- Particles Container -->
    <div id="particles-container"></div>
    
    {{-- <!-- Theme Toggle -->
    <div class="fixed top-6 right-6 z-50 flex items-center space-x-2">
        <i class="fas fa-sun text-yellow-500"></i>
        <div id="themeToggle" class="theme-toggle"></div>
        <i class="fas fa-moon text-indigo-400"></i>
    </div> --}}
    
    <!-- Navigation -->
    <nav class="sticky top-0 z-40 glass shadow-elegant">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-5">
                <div class="flex items-center animate-slideInLeft">
                    <div class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center mr-3 shadow-soft">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <span class="text-2xl font-heading font-bold text-gray-800 dark:text-white">Event<span class="text-secondary font-bold">Hub</span></span>
                </div>
                
                <div class="flex items-center space-x-6 animate-slideInRight">
                    <a href="#events" class="text-gray-700 dark:text-gray-300 hover:text-secondary dark:hover:text-secondary transition font-medium group">
                        <i class="fas fa-calendar-week mr-2"></i> 
                        <span class="relative">
                            Events
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-secondary group-hover:w-full transition-all duration-300"></span>
                        </span>
                    </a>
                    <button id="btnSearchModal" class="text-gray-700 dark:text-gray-300 hover:text-secondary dark:hover:text-secondary transition font-medium group neon-border px-4 py-2 rounded-lg">
                        <i class="fas fa-search mr-2"></i> 
                        <span class="relative">
                            Cek Pendaftaran
                            <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-secondary group-hover:w-full transition-all duration-300"></span>
                        </span>
                    </button>
                    @if(session('admin'))
                        <a href="{{ route('admin.index') }}" class="text-gray-700 dark:text-gray-300 hover:text-secondary dark:hover:text-secondary transition font-medium group">
                            <i class="fas fa-user-shield mr-2"></i> 
                            <span class="relative">
                                Admin Panel
                                <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-secondary group-hover:w-full transition-all duration-300"></span>
                            </span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative gradient-primary text-white py-20 overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="shimmer-effect absolute inset-0"></div>
            
            <!-- Animated gradient circles -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full bg-gradient-to-r from-secondary/10 to-indigo-500/10 animate-float"></div>
            <div class="absolute bottom-1/4 right-1/4 w-80 h-80 rounded-full bg-gradient-to-r from-indigo-500/10 to-green-500/10 animate-float" style="animation-delay: 1s;"></div>
            <div class="absolute top-1/3 right-1/3 w-64 h-64 rounded-full bg-gradient-to-r from-green-500/10 to-secondary/10 animate-float" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="container mx-auto px-4 text-center relative z-10">
            <!-- Improved Hero Title with Glitch Effect -->
            <h1 class="text-5xl md:text-6xl font-heading font-bold mb-6 leading-tight animate-fadeIn drop-shadow-lg">
                Temukan <span class="text-secondary">Event</span> Menarik<br>dan <span class="text-secondary">Eksklusif</span>
            </h1>
            
            <!-- Subtitle with Typewriter Effect -->
            <p class="text-xl md:text-2xl mb-10 max-w-3xl mx-auto opacity-90 hero-text">
                Bergabunglah dengan berbagai event seru dan tingkatkan pengetahuan serta jaringan Anda dalam platform premium terpercaya
            </p>
            
            <!-- Action Buttons with Improved Styling -->
            <div class="flex flex-col sm:flex-row justify-center gap-4 animate-fadeIn" style="animation-delay: 0.5s;">
                <a href="#events" class="px-8 py-4 btn-gold rounded-full font-semibold shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
                    <i class="fas fa-calendar-plus mr-2 group-hover:rotate-12 transition-transform"></i> 
                    <span class="relative">
                        Lihat Event Tersedia
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-white group-hover:w-full transition-all duration-300"></span>
                    </span>
                </a>
                <button id="btnSearchModalHero" class="px-8 py-4 bg-white/10 backdrop-blur-sm text-white rounded-full font-semibold border border-white/20 hover:bg-white/20 transition-all duration-300 transform hover:-translate-y-1 group neon-border">
                    <i class="fas fa-search mr-2 group-hover:scale-110 transition-transform"></i> 
                    <span class="relative">
                        Cek Pendaftaran
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-secondary group-hover:w-full transition-all duration-300"></span>
                    </span>
                </button>
            </div>
            
            <!-- Stats Section -->
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 max-w-3xl mx-auto animate-fadeIn" style="animation-delay: 0.8s;">
                <div class="glass p-6 rounded-2xl backdrop-blur-sm">
                    <div class="text-3xl font-bold text-secondary mb-2">{{ $events->count() }}</div>
                    <div class="text-sm opacity-80">Event Tersedia</div>
                </div>
                <div class="glass p-6 rounded-2xl backdrop-blur-sm">
                    <div class="text-3xl font-bold text-secondary mb-2">{{ $peserta->count() }}</div>
                    <div class="text-sm opacity-80">Peserta Terdaftar</div>
                </div>
                <div class="glass p-6 rounded-2xl backdrop-blur-sm">
                    <div class="text-3xl font-bold text-secondary mb-2">98%</div>
                    <div class="text-sm opacity-80">Kepuasan Peserta</div>
                </div>
                <div class="glass p-6 rounded-2xl backdrop-blur-sm">
                    <div class="text-3xl font-bold text-secondary mb-2">24/7</div>
                    <div class="text-sm opacity-80">Support Online</div>
                </div>
            </div>
        </div>
        
        <!-- Animated Scroll Indicator -->
        {{-- <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <a href="#events" class="flex flex-col items-center text-white/70 hover:text-white transition">
                <span class="text-sm mb-2">Scroll ke bawah</span>
                <i class="fas fa-chevron-down text-xl"></i>
            </a>
        </div> --}}
        
        <!-- Animated Wave Divider -->
        <div class="absolute bottom-0 left-0 right-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full">
                <path fill="#111827" fill-opacity="1" d="M0,160L48,176C96,192,192,224,288,213.3C384,203,480,149,576,138.7C672,128,768,160,864,165.3C960,171,1056,149,1152,138.7C1248,128,1344,128,1392,128L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
                    <animate attributeName="d" dur="10s" repeatCount="indefinite" values="
                        M0,160L48,176C96,192,192,224,288,213.3C384,203,480,149,576,138.7C672,128,768,160,864,165.3C960,171,1056,149,1152,138.7C1248,128,1344,128,1392,128L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z;
                        M0,192L48,186.7C96,181,192,171,288,160C384,149,480,139,576,144C672,149,768,171,864,181.3C960,192,1056,192,1152,176C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z;
                        M0,160L48,176C96,192,192,224,288,213.3C384,203,480,149,576,138.7C672,128,768,160,864,165.3C960,171,1056,149,1152,138.7C1248,128,1344,128,1392,128L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z
                    " />
                </path>
            </svg>
        </div>
    </section>

    <!-- Events Section -->
    <section id="events" class="py-20 bg-gray-50 dark:bg-gray-900 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-heading font-bold text-gray-800 dark:text-white mb-4 animate-fadeIn">
                   Event Tersedia
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto animate-fadeIn" style="animation-delay: 0.2s;">
                    Pilih event yang sesuai dengan minat dan kebutuhan Anda. Setiap event dirancang untuk memberikan pengalaman terbaik.
                </p>
            </div>
            
            <!-- Search Event -->
            <div class="mb-12 max-w-3xl mx-auto animate-fadeIn" style="animation-delay: 0.4s;">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-secondary text-lg"></i>
                    </div>
                    <input type="text" id="searchEventInput" 
                           class="w-full pl-12 pr-12 py-4 form-input rounded-2xl focus:outline-none focus:ring-0 text-gray-800 dark:text-gray-200 shadow-soft backdrop-blur-sm"
                           placeholder="Cari event berdasarkan nama, lokasi, deskripsi...">
                    <button id="clearSearch" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hidden transition transform hover:scale-110">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="flex items-center justify-between mt-2 ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i> Temukan event yang tepat untuk Anda
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400" id="eventCount">
                        Menampilkan {{ $events->count() }} event
                    </p>
                </div>
            </div>
            
            @if($events->isEmpty())
                <div class="text-center py-16 glass rounded-3xl shadow-soft backdrop-blur-sm animate-fadeIn">
                    <div class="w-24 h-24 gradient-primary rounded-full flex items-center justify-center mx-auto mb-6 shadow-soft animate-pulse-glow">
                        <i class="fas fa-calendar-times text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">Tidak ada event tersedia</h3>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">Mohon maaf, saat ini belum ada event yang tersedia. Silakan cek kembali nanti.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="eventsContainer">
                    @foreach($events as $index => $event)
                    <div class="event-card bg-white dark:bg-gray-800 rounded-2xl shadow-elegant overflow-hidden card-hover animate-fadeIn" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="h-56 relative overflow-hidden">
                            <div class="absolute inset-0 shimmer-effect">
                                <div class="absolute inset-0 
                                    @if($event->type == 'online') gradient-online
                                    @elseif($event->type == 'offline') gradient-offline
                                    @else gradient-hybrid
                                    @endif">
                                </div>
                            </div>
                            
                            <!-- Event type badge -->
                            <div class="absolute top-4 left-4">
                                @if($event->type == 'online')
                                <span class="glass text-white text-xs font-semibold px-4 py-2 rounded-full backdrop-blur-sm">
                                    <i class="fas fa-globe mr-1"></i> Online
                                </span>
                                @elseif($event->type == 'offline')
                                <span class="glass text-white text-xs font-semibold px-4 py-2 rounded-full backdrop-blur-sm">
                                    <i class="fas fa-map-marker-alt mr-1"></i> Offline
                                </span>
                                @else
                                <span class="glass text-white text-xs font-semibold px-4 py-2 rounded-full backdrop-blur-sm">
                                    <i class="fas fa-blend mr-1"></i> Hybrid
                                </span>
                                @endif
                            </div>
                            
                            <!-- Capacity badge -->
                            @if(!$event->canRegister())
                            <div class="absolute top-4 right-4">
                                <span class="bg-red-500/90 backdrop-blur-sm text-white text-xs font-semibold px-4 py-2 rounded-full animate-pulse-glow">
                                    <i class="fas fa-times-circle mr-1"></i> Penuh
                                </span>
                            </div>
                            @endif
                            
                            <!-- Event price -->
                            <div class="absolute bottom-4 right-4">
                                @if($event->price == 0)
                                <span class="glass text-white text-sm font-semibold px-4 py-2 rounded-full backdrop-blur-sm">
                                    <i class="fas fa-gift mr-1"></i> Gratis
                                </span>
                                @else
                                <span class="glass text-white text-sm font-semibold px-4 py-2 rounded-full backdrop-blur-sm">
                                    Rp {{ number_format($event->price, 0, ',', '.') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="mb-4">
                                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2 line-clamp-1">{{ $event->name }}</h3>
                                <div class="flex items-center text-gray-600 dark:text-gray-400 mb-1">
                                    <i class="far fa-calendar-alt mr-2 text-secondary"></i>
                                    <span>{{ \Carbon\Carbon::parse($event->date)->translatedFormat('d F Y') }}</span>
                                </div>
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-map-marker-alt mr-2 text-secondary"></i>
                                    <span class="line-clamp-1">{{ $event->location }}</span>
                                </div>
                            </div>
                            
                            <p class="text-gray-700 dark:text-gray-300 mb-6 line-clamp-3">{{ $event->description }}</p>
                            
                            <div class="flex justify-between items-center mb-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full gradient-accent flex items-center justify-center mr-3">
                                        <i class="fas fa-users text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $event->registered_count }} terdaftar</p>
                                        @if($event->available_slots !== null)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $event->available_slots - $event->registered_count }} kuota tersedia</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <button data-event-id="{{ $event->id }}" 
                                    class="w-full py-4 rounded-xl font-semibold transition-all duration-300 transform hover:-translate-y-1 group
                                    {{ $event->canRegister() ? 'btn-primary text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 cursor-not-allowed' }}"
                                    {{ !$event->canRegister() ? 'disabled' : '' }}>
                                <i class="fas {{ $event->canRegister() ? 'fa-user-plus mr-2 group-hover:rotate-12 transition-transform' : 'fa-ban mr-2' }}"></i> 
                                {{ $event->canRegister() ? 'Daftar Sekarang' : 'Event Penuh' }}
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
            
            <!-- No results message (hidden by default) -->
            <div id="noEventsMessage" class="hidden col-span-3 text-center py-16 glass rounded-3xl shadow-soft mt-4 backdrop-blur-sm">
                <div class="w-24 h-24 gradient-primary rounded-full flex items-center justify-center mx-auto mb-6 shadow-soft">
                    <i class="fas fa-search text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-3">Event tidak ditemukan</h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">Tidak ada event yang sesuai dengan pencarian Anda. Coba gunakan kata kunci yang berbeda.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 dark:bg-gray-900 text-white py-16 border-t border-gray-700 relative overflow-hidden">
        <!-- Footer Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23c5a572" fill-opacity="0.2"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div>
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 rounded-full gradient-secondary flex items-center justify-center mr-4 animate-pulse-glow">
                            <i class="fas fa-calendar-alt text-gray-900 text-xl"></i>
                        </div>
                        <span class="text-2xl font-heading font-bold">Event<span class="text-secondary">Hub</span></span>
                    </div>
                    <p class="text-gray-400 mb-6 max-w-md">Platform manajemen event premium terpercaya yang menghubungkan penyelenggara dengan peserta sejak 2023.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center hover:shadow-lg transition transform hover:-translate-y-1">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center hover:shadow-lg transition transform hover:-translate-y-1">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center hover:shadow-lg transition transform hover:-translate-y-1">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center hover:shadow-lg transition transform hover:-translate-y-1">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </div>
                </div>
                
<!-- Step by Step Registration -->
                <div>
                    <h4 class="text-xl font-heading font-bold mb-6 text-white">Langkah Pendaftaran</h4>
                    <ul class="space-y-4">
                        <li class="flex items-start group">
                            <span class="step-number text-sm">1</span>
                            <span class="text-gray-400 group-hover:text-secondary transition ml-3">Pilih event yang diinginkan</span>
                        </li>
                        <li class="flex items-start group">
                            <span class="step-number text-sm">2</span>
                            <span class="text-gray-400 group-hover:text-secondary transition ml-3">Klik tombol "Daftar Sekarang"</span>
                        </li>
                        <li class="flex items-start group">
                            <span class="step-number text-sm">3</span>
                            <span class="text-gray-400 group-hover:text-secondary transition ml-3">Isi formulir pendaftaran</span>
                        </li>
                        <li class="flex items-start group">
                            <span class="step-number text-sm">4</span>
                            <span class="text-gray-400 group-hover:text-secondary transition ml-3">Upload bukti pembayaran</span>
                        </li>
                        <li class="flex items-start group">
                            <span class="step-number text-sm">5</span>
                            <span class="text-gray-400 group-hover:text-secondary transition ml-3">Tunggu konfirmasi via Gmail</span>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-xl font-heading font-bold mb-6 text-white">Kontak Kami</h4>
                    <div class="space-y-4">
                        <div class="flex items-start group">
                            <i class="fas fa-phone text-secondary mt-1 mr-3 group-hover:scale-110 transition-transform"></i>
                            <div>
                                <p class="font-medium">Telepon</p>
                                <p class="text-gray-400">(021) 1234-5678</p>
                            </div>
                        </div>
                        <div class="flex items-start group">
                            <i class="fas fa-envelope text-secondary mt-1 mr-3 group-hover:scale-110 transition-transform"></i>
                            <div>
                                <p class="font-medium">Email</p>
                                <p class="text-gray-400">info@eventhub.com</p>
                            </div>
                        </div>
                        <div class="flex items-start group">
                            <i class="fas fa-map-marker-alt text-secondary mt-1 mr-3 group-hover:scale-110 transition-transform"></i>
                            <div>
                                <p class="font-medium">Alamat</p>
                                <p class="text-gray-400">Jl. Sudirman No. 123, Jakarta</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-12 pt-8 text-center">
                <p class="text-gray-400"> EventHub. All rights reserved. untuk pengalaman dan pengetahuan yang sesuai dengan minat anda.</p>
            </div>
        </div>
    </footer>

    <!-- Registration Modal -->
    <div id="registrationModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto animate-fadeIn">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="text-3xl font-heading font-bold text-gray-800 dark:text-white">Form Pendaftaran Event</h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">Lengkapi data di bawah untuk mendaftar event</p>
                    </div>
                    <button id="btnCloseRegistrationModal" class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center text-white hover:shadow-lg transition transform hover:rotate-90">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                
                <form id="registrationForm" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Personal Data -->
                        <div class="md:col-span-2">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 rounded-full gradient-secondary flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-gray-900"></i>
                                </div>
                                <h4 class="text-xl font-heading font-bold text-gray-800 dark:text-white">Data Pribadi</h4>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 mb-3 font-medium">Nama Lengkap <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-secondary"></i>
                                </div>
                                <input type="text" name="full_name" required 
                                       class="w-full pl-12 pr-4 py-4 form-input rounded-xl focus:outline-none focus:ring-0 text-gray-800 dark:text-gray-200" 
                                       placeholder="Masukkan nama lengkap">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 mb-3 font-medium">Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-secondary"></i>
                                </div>
                                <input type="email" name="email" required 
                                       class="w-full pl-12 pr-4 py-4 form-input rounded-xl focus:outline-none focus:ring-0 text-gray-800 dark:text-gray-200" 
                                       placeholder="contoh@email.com">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 mb-3 font-medium">Nomor HP/WhatsApp <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-secondary"></i>
                                </div>
                                <input type="tel" name="phone" required 
                                       class="w-full pl-12 pr-4 py-4 form-input rounded-xl focus:outline-none focus:ring-0 text-gray-800 dark:text-gray-200" 
                                       placeholder="0812-3456-7890">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 mb-3 font-medium">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-venus-mars text-secondary"></i>
                                </div>
                                <select name="gender" required 
                                        class="w-full pl-12 pr-4 py-4 form-input rounded-xl focus:outline-none focus:ring-0 text-gray-800 dark:text-gray-200 appearance-none">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-secondary"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300 mb-3 font-medium">NIK <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-id-card text-secondary"></i>
                                </div>
                                <input type="text" name="nik" required 
                                       class="w-full pl-12 pr-4 py-4 form-input rounded-xl focus:outline-none focus:ring-0 text-gray-800 dark:text-gray-200" 
                                       placeholder="16 digit NIK">
                            </div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 dark:text-gray-300 mb-3 font-medium">Alamat Lengkap <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute top-4 left-4">
                                    <i class="fas fa-map-marker-alt text-secondary"></i>
                                </div>
                                <textarea name="address" required rows="3" 
                                          class="w-full pl-12 pr-4 py-4 form-input rounded-xl focus:outline-none focus:ring-0 text-gray-800 dark:text-gray-200" 
                                          placeholder="Masukkan alamat lengkap"></textarea>
                            </div>
                        </div>
                        
                        <!-- Event Selection -->
                        <div class="md:col-span-2">
                            <div class="flex items-center mb-6 mt-4">
                                <div class="w-10 h-10 rounded-full gradient-secondary flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar-check text-gray-900"></i>
                                </div>
                                <h4 class="text-xl font-heading font-bold text-gray-800 dark:text-white">Pilihan Event</h4>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 dark:text-gray-300 mb-3 font-medium">Event yang Dipilih</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar-alt text-secondary"></i>
                                </div>
                                <input type="text" id="selected_event_name" 
                                       class="w-full pl-12 pr-4 py-4 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-800 dark:text-gray-200" 
                                       readonly>
                                <input type="hidden" name="event_id" id="selected_event_id">
                            </div>
                        </div>
                        
                        <!-- Payment Method -->
                        <div class="md:col-span-2">
                            <div class="flex items-center mb-6 mt-4">
                                <div class="w-10 h-10 rounded-full gradient-secondary flex items-center justify-center mr-3">
                                    <i class="fas fa-credit-card text-gray-900"></i>
                                </div>
                                <h4 class="text-xl font-heading font-bold text-gray-800 dark:text-gray-300">Metode Pembayaran</h4>
                            </div>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 dark:text-gray-300 mb-3 font-medium">Pilih Bank/E-Wallet <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-university text-secondary"></i>
                                </div>
                                <select id="paymentMethod" name="payment_method" required 
                                        class="w-full pl-12 pr-4 py-4 form-input rounded-xl focus:outline-none focus:ring-0 text-gray-800 dark:text-gray-500 appearance-none">
                                    <option value="">-- Pilih Metode Pembayaran --</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <i class="fas fa-chevron-down text-secondary"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Details -->
                        <div id="paymentDetails" class="md:col-span-2 glass p-6 rounded-2xl hidden">
                            <h5 class="font-heading font-bold text-gray-800 dark:text-white mb-4 text-lg">Instruksi Pembayaran:</h5>
                            <p class="text-gray-700 dark:text-gray-300 mb-4">Silakan transfer ke rekening berikut:</p>
                            <div class="bg-white/50 dark:bg-gray-700/50 p-5 rounded-xl border border-gray-200 dark:border-gray-600">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Bank</p>
                                        <p class="font-semibold text-gray-800 dark:text-white text-lg" id="bankName">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Nomor Rekening</p>
                                        <p class="font-bold text-gray-800 dark:text-white text-lg font-mono" id="accountNumber">-</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Atas Nama</p>
                                        <p class="font-semibold text-gray-800 dark:text-white text-lg" id="accountHolder">-</p>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Jumlah yang harus dibayar</p>
                                    <p class="font-bold text-red-600 text-2xl" id="paymentAmount">-</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">
                                <i class="fas fa-info-circle mr-2"></i> Upload bukti pembayaran setelah transfer untuk mempercepat proses verifikasi.
                            </p>
                        </div>
                        
                        <!-- Payment Proof Upload -->
                        <div id="paymentProofSection" class="md:col-span-2 mt-6 hidden">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 rounded-full gradient-secondary flex items-center justify-center mr-3">
                                    <i class="fas fa-file-upload text-gray-900"></i>
                                </div>
                                <h4 class="text-xl font-heading font-bold text-gray-800 dark:text-white">Bukti Pembayaran</h4>
                            </div>
                            
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-5 rounded-xl border border-yellow-200 dark:border-yellow-800 mb-6">
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Upload bukti pembayaran untuk mempercepat proses verifikasi. Format: JPG, PNG, atau PDF (max: 2MB)
                                </p>
                            </div>
                            
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-2xl p-8 text-center hover:border-secondary transition group">
                                <div class="w-16 h-16 gradient-primary rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-cloud-upload-alt text-white text-2xl"></i>
                                </div>
                                <p class="text-gray-700 dark:text-gray-300 mb-2 font-medium">Drag & drop file atau klik untuk upload</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Format yang diterima: JPG, PNG, PDF (Maksimal 2MB)</p>
                                <input type="file" name="payment_proof" id="payment_proof" 
                                       class="hidden"
                                       accept=".jpg,.jpeg,.png,.pdf">
                                <label for="payment_proof" class="px-6 py-3 btn-primary rounded-xl inline-block cursor-pointer hover:shadow-lg transition transform hover:-translate-y-1">
                                    <i class="fas fa-folder-open mr-2"></i> Pilih File
                                </label>
                                <p id="fileName" class="text-sm text-gray-600 dark:text-gray-400 mt-4"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-10 flex flex-col sm:flex-row justify-end space-y-4 sm:space-y-0 sm:space-x-4">
                        <button type="button" id="btnCancelRegistration" class="px-8 py-4 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition transform hover:-translate-y-1">
                            <i class="fas fa-times mr-2"></i> Batal
                        </button>
                        <button type="submit" class="px-8 py-4 btn-gold rounded-xl font-semibold hover:shadow-lg transition transform hover:-translate-y-1 group">
                            <i class="fas fa-paper-plane mr-2 group-hover:rotate-12 transition-transform"></i> Daftar & Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Search Participant Modal -->
    <div id="searchModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto animate-fadeIn">
            <div class="p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="text-3xl font-heading font-bold text-gray-800 dark:text-white">Cek Status Pendaftaran</h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">Cari data pendaftaran Anda dengan NIK, Email, atau Kode Transaksi</p>
                    </div>
                    <button id="btnCloseSearchModal" class="w-10 h-10 rounded-full gradient-primary flex items-center justify-center text-white hover:shadow-lg transition transform hover:rotate-90">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                
                <div class="mb-8">
                    <label class="block text-gray-700 dark:text-gray-300 font-medium mb-4 text-lg">Masukkan detail pendaftaran Anda</label>
                    <div class="flex">
                        <div class="relative flex-grow">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search text-secondary"></i>
                            </div>
                            <input type="text" id="searchInput" 
                                   placeholder="NIK, Email, atau Kode Transaksi" 
                                   class="w-full pl-12 pr-4 py-4 form-input rounded-l-2xl focus:outline-none focus:ring-0 text-gray-800 dark:text-gray-200">
                        </div>
                        <button id="btnSearchParticipant" class="px-8 btn-primary rounded-r-2xl font-semibold hover:shadow-lg transition transform hover:-translate-y-1 group">
                            <i class="fas fa-search mr-2 group-hover:rotate-12 transition-transform"></i> Cari
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3 ml-4">
                        <i class="fas fa-lightbulb mr-2"></i> Contoh: 3271XXXXXXXXXXXX, email@domain.com, atau EVENT-20231001-ABC123
                    </p>
                </div>
                
                <!-- Search Results -->
                <div id="searchResults" class="hidden">
                    <div id="searchResultsContent">
                        <!-- Results will be displayed here -->
                    </div>
                    
                    <div class="mt-8 p-5 glass rounded-2xl">
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            <i class="fas fa-info-circle text-secondary mr-2"></i>
                            Jika data tidak ditemukan atau terdapat kesalahan, silakan hubungi admin di 
                            <a href="mailto:admin@eventhub.com" class="text-secondary hover:underline font-medium">admin@eventhub.com</a>
                        </p>
                    </div>
                </div>
                
                <!-- Empty State -->
                <div id="emptyState" class="text-center py-12">
                    <div class="w-24 h-24 gradient-primary rounded-full flex items-center justify-center mx-auto mb-6 shadow-soft animate-pulse-glow">
                        <i class="fas fa-search text-white text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-3">Cari Status Pendaftaran</h4>
                    <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">Masukkan NIK, Email, atau Kode Transaksi Anda untuk mengecek status pendaftaran dan pembayaran.</p>
                </div>
                
                <!-- Loading State -->
                <div id="loadingState" class="text-center py-12 hidden">
                    <div class="w-24 h-24 gradient-primary rounded-full flex items-center justify-center mx-auto mb-6 shadow-soft">
                        <i class="fas fa-spinner fa-spin text-white text-3xl"></i>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400">Mencari data pendaftaran...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal with WhatsApp Integration -->
    <div id="successModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-2xl w-full animate-fadeIn">
            <div class="p-8 text-center">
                <div class="w-24 h-24 gradient-secondary rounded-full flex items-center justify-center mx-auto mb-6 shadow-soft animate-pulse-glow">
                    <i class="fas fa-check text-gray-900 text-3xl"></i>
                </div>
                <h3 class="text-3xl font-heading font-bold text-gray-800 dark:text-white mb-4">Pendaftaran Berhasil!</h3>
                <p id="successMessage" class="text-gray-600 dark:text-gray-400 mb-6 text-lg"></p>
                
                <div class="glass p-6 rounded-2xl mb-8 backdrop-blur-sm">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Kode Transaksi Anda:</p>
                    <p id="transactionCode" class="font-mono font-bold text-2xl text-gray-800 dark:text-white mb-6 bg-gray-100 dark:bg-gray-700 py-4 px-6 rounded-xl"></p>
                    
                    <!-- WhatsApp Countdown -->
                    <div id="whatsappCountdown" class="mb-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-5 rounded-xl border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center justify-center mb-3">
                                <i class="fab fa-whatsapp text-green-500 text-2xl mr-3 animate-pulse-glow"></i>
                                <p class="text-blue-700 dark:text-blue-300 font-medium">
                                    Anda akan diarahkan ke WhatsApp dalam 
                                    <span id="countdownTimer" class="font-bold text-2xl mx-2">5</span> 
                                    detik
                                </p>
                            </div>
                            <p class="text-sm text-blue-600 dark:text-blue-400">
                                Jika tidak otomatis terhubung, klik tombol "Kirim ke WhatsApp" di bawah.
                            </p>
                        </div>
                    </div>
                    
                    <!-- QR Code Container -->
                    <div id="qrCodeContainer" class="mb-6 hidden">
                        <p class="text-gray-700 dark:text-gray-300 mb-4 font-medium">QR Code Tiket:</p>
                        <div class="flex justify-center">
                            <div id="successQRCode" class="w-64 h-64 bg-white p-6 rounded-2xl border border-gray-300 dark:border-gray-600 flex items-center justify-center shadow-soft">
                                <!-- QR Code will be generated here -->
                            </div>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                            <i class="fas fa-info-circle mr-2"></i> QR Code ini untuk keperluan check-in event
                        </p>
                    </div>

                    <!-- Payment Pending Message -->
                    <div id="paymentPendingMessage" class="mb-6 hidden">
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 p-5 rounded-xl border border-yellow-200 dark:border-yellow-800">
                            <div class="flex items-center justify-center mb-3">
                                <i class="fas fa-clock text-yellow-500 text-2xl mr-3 animate-pulse-glow"></i>
                                <p class="text-yellow-700 dark:text-yellow-300 font-medium">
                                    QR Code akan tersedia setelah pembayaran diverifikasi
                                </p>
                            </div>
                            <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                Status pembayaran Anda saat ini: <span class="font-semibold">Menunggu Verifikasi</span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Download Button -->
                    <div id="downloadButtonContainer" class="mb-6 hidden">
                        <button id="btnDownloadTicket" class="px-6 py-3 btn-primary rounded-xl font-medium hover:shadow-lg transition transform hover:-translate-y-1 group">
                            <i class="fas fa-download mr-2 group-hover:rotate-12 transition-transform"></i> Download Tiket
                        </button>
                    </div>
                </div>
                
                <div class="text-left bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl mb-8">
                    <h4 class="font-heading font-bold text-blue-800 dark:text-blue-300 mb-4 text-lg">
                        <i class="fas fa-info-circle mr-2"></i> Instruksi Selanjutnya:
                    </h4>
                    <ul class="text-blue-700 dark:text-blue-300 space-y-3">
                        <li class="flex items-start group">
                            <span class="bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5 group-hover:scale-110 transition-transform">1</span>
                            <span>Simpan kode transaksi ini untuk referensi Anda</span>
                        </li>
                        <li class="flex items-start group">
                            <span class="bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5 group-hover:scale-110 transition-transform">2</span>
                            <span>Bukti pembayaran akan diverifikasi oleh admin dalam 1x24 jam</span>
                        </li>
                        <li class="flex items-start group">
                            <span class="bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5 group-hover:scale-110 transition-transform">3</span>
                            <span>Setelah verifikasi, QR Code akan tersedia untuk check-in di event</span>
                        </li>
                        <li class="flex items-start group">
                            <span class="bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5 group-hover:scale-110 transition-transform">4</span>
                            <span>Anda akan menerima konfirmasi via WhatsApp setelah verifikasi</span>
                        </li>
                        <li class="flex items-start group">
                            <span class="bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mr-3 mt-0.5 group-hover:scale-110 transition-transform">5</span>
                            <span>Gunakan fitur "Cek Pendaftaran" untuk mengecek status kapan saja</span>
                        </li>
                    </ul>
                </div>
                
                <div class="flex flex-col space-y-4">
                    <button id="btnWhatsApp" class="w-full px-8 py-4 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition hover:shadow-lg transform hover:-translate-y-1 group">
                        <i class="fab fa-whatsapp mr-2 group-hover:rotate-12 transition-transform"></i> Kirim ke WhatsApp
                    </button>
                    <button id="btnCloseSuccessModal" class="w-full px-8 py-4 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition transform hover:-translate-y-1">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden WhatsApp Data Container -->
    <div id="whatsappData" class="hidden"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('EventHub Premium - DOM Content Loaded');
        
        let currentEvent = null;
        let paymentMethods = [];
        let whatsappTimer = null;

        // Element references
        const themeToggle = document.getElementById('themeToggle');
        const btnSearchModal = document.getElementById('btnSearchModal');
        const btnSearchModalHero = document.getElementById('btnSearchModalHero');
        const btnSearchModalFooter = document.getElementById('btnSearchModalFooter');
        const btnCloseSearchModal = document.getElementById('btnCloseSearchModal');
        const searchModal = document.getElementById('searchModal');
        const btnSearchParticipant = document.getElementById('btnSearchParticipant');
        const searchInput = document.getElementById('searchInput');
        const emptyState = document.getElementById('emptyState');
        const loadingState = document.getElementById('loadingState');
        const searchResults = document.getElementById('searchResults');
        const searchResultsContent = document.getElementById('searchResultsContent');
        const scrollToTopBtn = document.getElementById('scrollToTop');
        
        const registrationModal = document.getElementById('registrationModal');
        const btnCloseRegistrationModal = document.getElementById('btnCloseRegistrationModal');
        const btnCancelRegistration = document.getElementById('btnCancelRegistration');
        const registrationForm = document.getElementById('registrationForm');
        const paymentMethodSelect = document.getElementById('paymentMethod');
        const paymentDetails = document.getElementById('paymentDetails');
        const paymentProofSection = document.getElementById('paymentProofSection');
        const searchEventInput = document.getElementById('searchEventInput');
        const clearSearchBtn = document.getElementById('clearSearch');
        const paymentProofInput = document.getElementById('payment_proof');
        const fileNameDisplay = document.getElementById('fileName');
        const eventCount = document.getElementById('eventCount');
        
        const successModal = document.getElementById('successModal');
        const successMessage = document.getElementById('successMessage');
        const transactionCode = document.getElementById('transactionCode');
        const successQRCode = document.getElementById('successQRCode');
        const qrCodeContainer = document.getElementById('qrCodeContainer');
        const paymentPendingMessage = document.getElementById('paymentPendingMessage');
        const downloadButtonContainer = document.getElementById('downloadButtonContainer');
        const btnCloseSuccessModal = document.getElementById('btnCloseSuccessModal');
        const btnDownloadTicket = document.getElementById('btnDownloadTicket');
        const btnWhatsApp = document.getElementById('btnWhatsApp');
        const whatsappCountdown = document.getElementById('whatsappCountdown');
        const countdownTimer = document.getElementById('countdownTimer');
        
        const whatsappData = document.getElementById('whatsappData');
        const noEventsMessage = document.getElementById('noEventsMessage');

        // Initialize theme
        const savedTheme = localStorage.getItem('theme') || 'dark';
        if (savedTheme === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }

        // Initialize particle animation
        function createParticles() {
            const container = document.getElementById('particles-container');
            if (!container) return;
            
            const particleCount = 30;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random position
                const left = Math.random() * 100;
                const top = Math.random() * 100;
                
                // Random size
                const size = Math.random() * 3 + 1;
                
                // Random color
                const colors = [
                    'rgba(197, 165, 114, 0.6)',
                    'rgba(49, 130, 206, 0.6)',
                    'rgba(128, 90, 213, 0.6)',
                    'rgba(56, 161, 105, 0.6)'
                ];
                const color = colors[Math.floor(Math.random() * colors.length)];
                
                // Random animation duration and delay
                const duration = Math.random() * 10 + 10;
                const delay = Math.random() * 5;
                
                // Apply styles
                particle.style.left = `${left}%`;
                particle.style.top = `${top}%`;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.backgroundColor = color;
                particle.style.animation = `particleFloat ${duration}s linear infinite`;
                particle.style.animationDelay = `${delay}s`;
                
                container.appendChild(particle);
            }
        }

        // Event Listeners
        if (themeToggle) {
            themeToggle.addEventListener('click', toggleTheme);
        }
        
        if (btnSearchModal) btnSearchModal.addEventListener('click', openSearchModal);
        if (btnSearchModalHero) btnSearchModalHero.addEventListener('click', openSearchModal);
        if (btnSearchModalFooter) btnSearchModalFooter.addEventListener('click', openSearchModal);
        
        if (btnCloseSearchModal) btnCloseSearchModal.addEventListener('click', closeSearchModal);
        if (btnSearchParticipant) btnSearchParticipant.addEventListener('click', searchParticipant);
        
        if (btnCloseRegistrationModal) btnCloseRegistrationModal.addEventListener('click', closeRegistrationModal);
        if (btnCancelRegistration) btnCancelRegistration.addEventListener('click', closeRegistrationModal);
        
        if (btnCloseSuccessModal) btnCloseSuccessModal.addEventListener('click', closeSuccessModal);
        if (btnDownloadTicket) btnDownloadTicket.addEventListener('click', downloadTicket);
        
        if (scrollToTopBtn) {
            scrollToTopBtn.addEventListener('click', function() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            // Show/hide scroll to top button
            window.addEventListener('scroll', function() {
                if (window.scrollY > 500) {
                    scrollToTopBtn.style.display = 'flex';
                } else {
                    scrollToTopBtn.style.display = 'none';
                }
            });
        }
        
        if (btnWhatsApp) {
            btnWhatsApp.addEventListener('click', function() {
                if (whatsappData.dataset.message) {
                    sendToWhatsApp(whatsappData.dataset.message);
                    clearInterval(whatsappTimer);
                    if (whatsappCountdown) whatsappCountdown.classList.add('hidden');
                }
            });
        }
        
        // Registration button listeners
        document.querySelectorAll('.btn-register').forEach(button => {
            button.addEventListener('click', function() {
                const eventId = this.getAttribute('data-event-id');
                openRegistrationModal(eventId);
            });
        });
        
        // Payment method change listener
        if (paymentMethodSelect) {
            paymentMethodSelect.addEventListener('change', function(e) {
                updatePaymentDetails(e.target.value);
            });
        }
        
        // Form submission
        if (registrationForm) {
            registrationForm.addEventListener('submit', submitRegistration);
        }
        
        // Search event listener
        if (searchEventInput) {
            searchEventInput.addEventListener('input', filterEvents);
        }
        
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                if (searchEventInput) {
                    searchEventInput.value = '';
                    filterEvents();
                    clearSearchBtn.classList.add('hidden');
                }
            });
        }
        
        // File upload display
        if (paymentProofInput && fileNameDisplay) {
            paymentProofInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    fileNameDisplay.textContent = `File dipilih: ${this.files[0].name}`;
                } else {
                    fileNameDisplay.textContent = '';
                }
            });
        }
        
        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target === registrationModal) closeRegistrationModal();
            if (e.target === searchModal) closeSearchModal();
            if (e.target === successModal) closeSuccessModal();
        });

        // Enter key for search
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchParticipant();
                }
            });
        }

        // Initialize particles
        createParticles();

        // ========== FUNGSI UTAMA ==========
        
        // Theme Toggle Function
        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
        
        // 1. Search Event Functionality
        function filterEvents() {
            const searchTerm = searchEventInput.value.toLowerCase().trim();
            const eventCards = document.querySelectorAll('.event-card');
            let visibleCount = 0;
            
            if (clearSearchBtn) {
                if (searchTerm) {
                    clearSearchBtn.classList.remove('hidden');
                } else {
                    clearSearchBtn.classList.add('hidden');
                }
            }
            
            eventCards.forEach(card => {
                const eventName = card.querySelector('h3').textContent.toLowerCase();
                const eventLocation = card.querySelectorAll('p')[1]?.textContent.toLowerCase() || '';
                const eventDescription = card.querySelector('.line-clamp-3')?.textContent.toLowerCase() || '';
                const eventType = card.querySelector('span')?.textContent.toLowerCase() || '';
                
                if (eventName.includes(searchTerm) || 
                    eventLocation.includes(searchTerm) || 
                    eventDescription.includes(searchTerm) ||
                    eventType.includes(searchTerm)) {
                    card.style.display = 'block';
                    card.classList.add('animate-fadeIn');
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Update event count
            if (eventCount) {
                eventCount.textContent = `Menampilkan ${visibleCount} event`;
            }
            
            // Show message if no events found
            if (visibleCount === 0 && searchTerm && eventCards.length > 0) {
                if (noEventsMessage) noEventsMessage.classList.remove('hidden');
            } else if (noEventsMessage) {
                noEventsMessage.classList.add('hidden');
            }
        }

        // 2. Generate QR Code
        function generateQRCode(elementId, code) {
            if (!code || !elementId) return;
            
            console.log('Generating QR Code for:', code, 'in element:', elementId);
            
            const element = document.getElementById(elementId);
            if (!element) {
                console.error('Element not found:', elementId);
                return;
            }
            
            // Clear previous content
            element.innerHTML = '';
            
            // Show loading indicator
            element.innerHTML = '<div class="qr-code-placeholder"><div class="text-gray-400 text-sm">Membuat QR Code...</div></div>';
            
            try {
                // Gunakan qrcode-generator library
                const qr = qrcode(4, 'M');
                qr.addData(code);
                qr.make();
                
                setTimeout(() => {
                    try {
                        const qrImageUrl = qr.createDataURL(4, 0);
                        
                        const img = document.createElement('img');
                        img.src = qrImageUrl;
                        img.alt = `QR Code for ${code}`;
                        img.className = 'qr-code-img';
                        
                        element.innerHTML = '';
                        element.appendChild(img);
                        console.log('QR Code generated successfully');
                    } catch (innerError) {
                        console.error('Error creating QR Code image:', innerError);
                        showTextFallback(element, code);
                    }
                }, 100);
                
            } catch (error) {
                console.error('Error in QRCode generation:', error);
                showTextFallback(element, code);
            }
        }
        
        function showTextFallback(element, code) {
            element.innerHTML = `
                <div class="text-center p-6 bg-gray-100 dark:bg-gray-700 rounded-2xl">
                    <p class="font-mono text-lg break-all font-bold text-gray-800 dark:text-white">${code}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">(Kode Transaksi)</p>
                </div>
            `;
        }

        // 3. WhatsApp Functions
        function composeWhatsAppMessage(participantData, eventData, transactionCode) {
            if (!eventData) {
                console.error('Event data is null or undefined in composeWhatsAppMessage');
                return `*KONFIRMASI PENDAFTARAN EVENT*
                
Kode Transaksi: *${transactionCode}*
Nama: ${participantData.full_name}
Email: ${participantData.email}
Nomor: ${participantData.phone}
Alamat: ${participantData.address}
Event: Event tidak tersedia
Tanggal Event: Tanggal tidak tersedia
Lokasi: Lokasi tidak tersedia
Status: Menunggu Verifikasi Pembayaran

Selamat kamu telah berhasil mendaftar selanjutnya tunggu konfirmasi pembayaran dan lakukan pengecekan berkala di situs web dengan menggunakan kode transaksi yang sudah di dapatkan.

*PENTING:* Jangan lupa untuk melakukan pengecekan berkala di situs web menggunakan kode transaksi yang sudah didapatkan.

Terima kasih telah mendaftar!`;
            }
            
            let eventDateStr = 'Tanggal tidak tersedia';
            try {
                if (eventData.date) {
                    const eventDate = new Date(eventData.date);
                    if (!isNaN(eventDate.getTime())) {
                        eventDateStr = eventDate.toLocaleDateString('id-ID', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        });
                    }
                }
            } catch (error) {
                console.error('Error formatting date:', error);
            }
            
            return `*KONFIRMASI PENDAFTARAN EVENT*
            
Kode Transaksi: *${transactionCode}*
Nama: ${participantData.full_name}
Email: ${participantData.email}
Nomor: ${participantData.phone}
Alamat: ${participantData.address}
Event: ${eventData.name || 'Event tidak tersedia'}
Tanggal Event: ${eventDateStr}
Lokasi: ${eventData.location || 'Lokasi tidak tersedia'}
Status: Menunggu Verifikasi Pembayaran

Selamat kamu telah berhasil mendaftar selanjutnya tunggu konfirmasi pembayaran dan lakukan pengecekan berkala di situs web dengan menggunakan kode transaksi yang sudah di dapatkan.

*PENTING:* Jangan lupa untuk melakukan pengecekan berkala di situs web menggunakan kode transaksi yang sudah didapatkan.

Terima kasih telah mendaftar!`;
        }

        function sendToWhatsApp(message) {
            const phoneNumber = '6285934554395';
            const encodedMessage = encodeURIComponent(message);
            const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
            
            // Buka WhatsApp di tab baru
            window.open(whatsappUrl, '_blank');
            
            // Juga buka di jendela yang sama untuk mobile
            setTimeout(() => {
                window.location.href = whatsappUrl;
            }, 500);
        }

        function startWhatsAppCountdown(message) {
            if (!whatsappCountdown || !countdownTimer) return;
            
            whatsappCountdown.classList.remove('hidden');
            let seconds = 5;
            
            // Clear existing timer
            if (whatsappTimer) {
                clearInterval(whatsappTimer);
            }
            
            countdownTimer.textContent = seconds;
            
            whatsappTimer = setInterval(() => {
                seconds--;
                countdownTimer.textContent = seconds;
                
                if (seconds <= 0) {
                    clearInterval(whatsappTimer);
                    if (message) {
                        sendToWhatsApp(message);
                    }
                    whatsappCountdown.classList.add('hidden');
                }
            }, 1000);
        }

        // 4. Search Participant Functions
        function getStatusMessage(paymentStatus, notes) {
            const defaultNote = 'Pendaftaran berhasil. Silakan tunggu verifikasi pembayaran.';
            
            if (notes === defaultNote) {
                switch(paymentStatus) {
                    case 'pending':
                        return 'Menunggu pembayaran. Silakan lakukan pembayaran dan upload bukti pembayaran untuk mempercepat proses verifikasi.';
                    case 'paid':
                        return 'Pembayaran sudah diterima. Sedang dalam proses verifikasi oleh admin. Status akan berubah menjadi "Terverifikasi" setelah proses selesai.';
                    case 'verified':
                        return 'Pembayaran sudah diverifikasi. Anda telah terdaftar resmi sebagai peserta event. QR Code sudah dapat digunakan untuk check-in.';
                    default:
                        return notes;
                }
            }
            
            return notes;
        }

        function openSearchModal() {
            console.log('Opening search modal');
            if (searchModal) {
                searchModal.classList.remove('hidden');
                searchModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
                if (searchInput) searchInput.focus();
            }
        }
        
        function closeSearchModal() {
            if (searchModal) {
                searchModal.classList.add('hidden');
                searchModal.classList.remove('flex');
                document.body.style.overflow = 'auto';
                resetSearch();
            }
        }
        
        function resetSearch() {
            if (searchInput) searchInput.value = '';
            if (emptyState) emptyState.classList.remove('hidden');
            if (loadingState) loadingState.classList.add('hidden');
            if (searchResults) searchResults.classList.add('hidden');
        }
        
        async function searchParticipant() {
            const searchValue = searchInput ? searchInput.value.trim() : '';
            if (searchValue === '') {
                alert('Silakan masukkan NIK, Email, atau Kode Transaksi terlebih dahulu');
                return;
            }
            
            try {
                // Show loading state
                if (emptyState) emptyState.classList.add('hidden');
                if (loadingState) loadingState.classList.remove('hidden');
                if (searchResults) searchResults.classList.add('hidden');
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    alert('Token CSRF tidak ditemukan');
                    return;
                }
                
                const response = await fetch('/search-participant', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ search: searchValue })
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Terjadi kesalahan saat mencari data');
                }
                
                // Hide loading state
                if (loadingState) loadingState.classList.add('hidden');
                
                displaySearchResults(data.participants);
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mencari data: ' + error.message);
                if (loadingState) loadingState.classList.add('hidden');
                if (emptyState) emptyState.classList.remove('hidden');
            }
        }
        
        function displaySearchResults(participants) {
            if (!searchResultsContent) return;
            
            if (!participants || participants.length === 0) {
                searchResultsContent.innerHTML = `
                    <div class="text-center py-12">
                        <div class="w-24 h-24 gradient-primary rounded-full flex items-center justify-center mx-auto mb-6 shadow-soft">
                            <i class="fas fa-search text-white text-3xl"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 dark:text-white mb-3">Data tidak ditemukan</h4>
                        <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">Pastikan NIK, Email, atau Kode Transaksi yang Anda masukkan benar</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">Jika Anda yakin data sudah benar, silakan hubungi admin</p>
                    </div>
                `;
            } else {
                let html = '';
                participants.forEach((participant, index) => {
                    let statusColor = '';
                    let statusText = '';
                    let badgeColor = '';
                    
                    switch(participant.payment_status) {
                        case 'verified':
                            statusColor = 'text-green-600 dark:text-green-400';
                            statusText = 'Terverifikasi';
                            badgeColor = 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800';
                            break;
                        case 'paid':
                            statusColor = 'text-blue-600 dark:text-blue-400';
                            statusText = 'Sudah Bayar';
                            badgeColor = 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800';
                            break;
                        case 'pending':
                        default:
                            statusColor = 'text-yellow-600 dark:text-yellow-400';
                            statusText = 'Menunggu Pembayaran';
                            badgeColor = 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800';
                            break;
                    }
                    
                    // Check if payment proof exists
                    const hasProof = participant.payment_proof ? 
                        `<a href="/storage/payment_proofs/${participant.payment_proof}" target="_blank" class="text-secondary hover:underline font-medium">
                            <i class="fas fa-file-invoice mr-2"></i> Lihat Bukti Pembayaran
                        </a>` : 
                        '<span class="text-gray-500 dark:text-gray-400"><i class="fas fa-times-circle mr-2"></i> Belum upload bukti</span>';
                    
                    // Create unique ID for QR code
                    const qrCodeId = `qrcode-${participant.id}`;
                    
                    // Determine if QR code should be shown (only for verified/paid status)
                    const showQRCode = participant.payment_status === 'verified' || participant.payment_status === 'paid';
                    
                    // DAPATKAN PESAN BERDASARKAN STATUS
                    const statusMessage = getStatusMessage(participant.payment_status, participant.notes || '');
                    
                    html += `
                        <div class="mb-8 animate-fadeIn" style="animation-delay: ${index * 0.1}s">
                            <div class="border-l-4 ${participant.payment_status === 'verified' ? 'border-green-500' : participant.payment_status === 'paid' ? 'border-blue-500' : 'border-yellow-500'} bg-gray-50 dark:bg-gray-800/50 p-6 rounded-r-2xl">
                                <div class="flex flex-col md:flex-row justify-between items-start mb-6">
                                    <div class="flex-1 mb-4 md:mb-0">
                                        <h4 class="text-xl font-heading font-bold text-gray-800 dark:text-white mb-2">${participant.full_name}</h4>
                                        <p class="text-gray-700 dark:text-gray-300 font-medium mb-3">${participant.event.name}</p>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                                                <p class="font-medium">${participant.email}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">No. HP</p>
                                                <p class="font-medium">${participant.phone}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Tanggal Daftar</p>
                                                <p class="font-medium">${participant.created_at}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Kode Transaksi</p>
                                                <p class="font-mono font-bold text-gray-800 dark:text-white">${participant.transaction_code}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-6">
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Status Pembayaran</p>
                                                <p class="font-semibold ${statusColor}">${statusText}</p>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">Bukti Bayar</p>
                                                <div class="mt-1">${hasProof}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="${badgeColor} px-4 py-2 rounded-full font-semibold">
                                        ${participant.payment_status === 'verified' ? 'Lunas' : participant.payment_status === 'paid' ? 'Sudah Bayar' : 'Pending'}
                                    </span>
                                </div>
                                
                                <!-- Status Message -->
                                <div class="mb-6 p-4 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600">
                                    <p class="text-gray-700 dark:text-gray-300">
                                        <strong>Status Pendaftaran:</strong> ${statusMessage}
                                    </p>
                                </div>
                                
                                ${showQRCode ? `
                                <!-- QR Code Section -->
                                <div class="mb-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                                    <h5 class="font-heading font-bold text-gray-800 dark:text-white mb-4 text-lg">QR Code Tiket</h5>
                                    <div class="flex flex-col md:flex-row items-center md:items-start">
                                        <div class="bg-white dark:bg-gray-700 p-6 rounded-2xl border border-gray-300 dark:border-gray-600 mb-6 md:mb-0 md:mr-8 shadow-soft">
                                            <div id="${qrCodeId}" class="w-48 h-48"></div>
                                            <p class="text-center text-xs text-gray-500 dark:text-gray-400 mt-4">${participant.transaction_code}</p>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-gray-700 dark:text-gray-300 mb-4">
                                                <i class="fas fa-info-circle text-secondary mr-2"></i>
                                                Gunakan QR Code ini untuk check-in saat event berlangsung.
                                            </p>
                                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl">
                                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                                    <strong>Catatan:</strong> Pastikan Anda membawa QR Code ini (cetak atau di smartphone) saat menghadiri event.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                ` : `
                                <!-- Pending Payment Message -->
                                <div class="mb-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-5 rounded-xl border border-yellow-200 dark:border-yellow-800">
                                        <div class="flex items-center mb-3">
                                            <i class="fas fa-clock text-yellow-500 text-xl mr-3"></i>
                                            <p class="text-yellow-700 dark:text-yellow-300 font-medium">
                                                QR Code akan tersedia setelah pembayaran diverifikasi oleh admin.
                                            </p>
                                        </div>
                                        <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                            Silakan tunggu verifikasi pembayaran Anda. Anda akan menerima notifikasi via WhatsApp setelah pembayaran diverifikasi.
                                        </p>
                                    </div>
                                </div>
                                `}
                                
                                <!-- Admin Notes (if any) -->
                                ${(participant.notes && participant.notes !== 'Pendaftaran berhasil. Silakan tunggu verifikasi pembayaran.') ? `
                                <div class="mt-6 p-5 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                                    <p class="text-sm text-blue-700 dark:text-blue-300">
                                        <strong><i class="fas fa-comment-alt mr-2"></i> Catatan Admin:</strong> ${participant.notes}
                                    </p>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                });
                
                searchResultsContent.innerHTML = html;
                
                // Generate QR codes for each participant with verified/paid status
                participants.forEach(participant => {
                    if (participant.payment_status === 'verified' || participant.payment_status === 'paid') {
                        const qrCodeId = `qrcode-${participant.id}`;
                        setTimeout(() => {
                            generateQRCode(qrCodeId, participant.transaction_code);
                        }, 300);
                    }
                });
            }
            
            if (emptyState) emptyState.classList.add('hidden');
            if (loadingState) loadingState.classList.add('hidden');
            if (searchResults) searchResults.classList.remove('hidden');
        }
        
        // 5. Registration Modal Functions
        async function openRegistrationModal(eventId) {
            try {
                console.log('Fetching event details for ID:', eventId);
                const response = await fetch(`/event/${eventId}/details`);
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Gagal memuat data event');
                }
                
                currentEvent = data.event;
                paymentMethods = data.payment_methods;
                
                console.log('Current event data:', currentEvent);
                
                // Populate form
                const selectedEventId = document.getElementById('selected_event_id');
                const selectedEventName = document.getElementById('selected_event_name');
                
                if (selectedEventId) selectedEventId.value = currentEvent.id;
                if (selectedEventName) selectedEventName.value = currentEvent.name;
                
                // Store event data in form data attributes for later use
                if (registrationForm) {
                    registrationForm.dataset.eventName = currentEvent.name;
                    registrationForm.dataset.eventDate = currentEvent.date;
                    registrationForm.dataset.eventLocation = currentEvent.location;
                    console.log('Event data stored in form:', {
                        name: currentEvent.name,
                        date: currentEvent.date,
                        location: currentEvent.location
                    });
                }
                
                // Populate payment methods dropdown
                if (paymentMethodSelect) {
                    paymentMethodSelect.innerHTML = '<option value="">-- Pilih Metode Pembayaran --</option>';
                    
                    paymentMethods.forEach(method => {
                        const option = document.createElement('option');
                        option.value = method.name;
                        option.textContent = method.name;
                        option.setAttribute('data-account-number', method.account_number);
                        option.setAttribute('data-account-name', method.account_name);
                        paymentMethodSelect.appendChild(option);
                    });
                }
                
                // Reset payment proof section
                if (paymentProofSection) {
                    paymentProofSection.classList.add('hidden');
                }
                if (paymentProofInput && fileNameDisplay) {
                    paymentProofInput.value = '';
                    fileNameDisplay.textContent = '';
                }
                
                // Show modal
                if (registrationModal) {
                    registrationModal.classList.remove('hidden');
                    registrationModal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                }
                
            } catch (error) {
                console.error('Error:', error);
                alert('Gagal memuat data event. Silakan coba lagi. Error: ' + error.message);
            }
        }
        
        function closeRegistrationModal() {
            if (registrationModal) {
                registrationModal.classList.add('hidden');
                registrationModal.classList.remove('flex');
                document.body.style.overflow = 'auto';
            }
            if (registrationForm) {
                registrationForm.reset();
                // Clear stored event data
                delete registrationForm.dataset.eventName;
                delete registrationForm.dataset.eventDate;
                delete registrationForm.dataset.eventLocation;
            }
            if (paymentDetails) paymentDetails.classList.add('hidden');
            if (paymentProofSection) paymentProofSection.classList.add('hidden');
            if (paymentProofInput && fileNameDisplay) {
                paymentProofInput.value = '';
                fileNameDisplay.textContent = '';
            }
            currentEvent = null;
        }
        
        function updatePaymentDetails(selectedMethod) {
            if (!selectedMethod) {
                if (paymentDetails) paymentDetails.classList.add('hidden');
                if (paymentProofSection) paymentProofSection.classList.add('hidden');
                return;
            }
            
            if (paymentDetails) paymentDetails.classList.remove('hidden');
            
            const selectedOption = paymentMethodSelect.querySelector(`option[value="${selectedMethod}"]`);
            if (!selectedOption) return;
            
            const bankName = document.getElementById('bankName');
            const accountNumber = document.getElementById('accountNumber');
            const accountHolder = document.getElementById('accountHolder');
            const paymentAmount = document.getElementById('paymentAmount');
            
            if (bankName) bankName.textContent = selectedMethod;
            if (accountNumber) accountNumber.textContent = selectedOption.getAttribute('data-account-number') || '-';
            if (accountHolder) accountHolder.textContent = selectedOption.getAttribute('data-account-name') || '-';
            
            if (currentEvent) {
                if (currentEvent.price > 0) {
                    if (paymentAmount) paymentAmount.textContent = 'Rp ' + parseFloat(currentEvent.price).toLocaleString('id-ID');
                    if (paymentProofSection) paymentProofSection.classList.remove('hidden');
                } else {
                    if (paymentAmount) paymentAmount.textContent = 'Gratis';
                    if (paymentProofSection) paymentProofSection.classList.add('hidden');
                }
            }
        }
        
        // 6. Success Modal Functions
        function showSuccessModal(message, code, paymentStatus = 'pending', whatsappMessage = '') {
            if (successMessage) successMessage.textContent = message;
            if (transactionCode) transactionCode.textContent = code;
            
            // Store WhatsApp message in hidden container
            if (whatsappMessage && whatsappData) {
                whatsappData.dataset.message = whatsappMessage;
            }
            
            // Determine if QR code should be shown based on payment status
            const showQRCode = paymentStatus === 'verified' || paymentStatus === 'paid';
            
            if (showQRCode) {
                if (qrCodeContainer) {
                    qrCodeContainer.classList.remove('hidden');
                    setTimeout(() => {
                        if (successQRCode && code) {
                            generateQRCode('successQRCode', code);
                        }
                    }, 500);
                }
                if (paymentPendingMessage) {
                    paymentPendingMessage.classList.add('hidden');
                }
                if (downloadButtonContainer) {
                    downloadButtonContainer.classList.remove('hidden');
                }
                if (whatsappCountdown) {
                    whatsappCountdown.classList.add('hidden');
                }
            } else {
                if (qrCodeContainer) {
                    qrCodeContainer.classList.add('hidden');
                }
                if (paymentPendingMessage) {
                    paymentPendingMessage.classList.remove('hidden');
                }
                if (downloadButtonContainer) {
                    downloadButtonContainer.classList.add('hidden');
                }
                if (whatsappCountdown) {
                    whatsappCountdown.classList.remove('hidden');
                    // Start WhatsApp countdown
                    startWhatsAppCountdown(whatsappMessage);
                }
            }
            
            if (successModal) {
                successModal.classList.remove('hidden');
                successModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }
        }
        
        function closeSuccessModal() {
            // Clear WhatsApp timer
            if (whatsappTimer) {
                clearInterval(whatsappTimer);
                whatsappTimer = null;
            }
            
            if (successModal) {
                successModal.classList.add('hidden');
                successModal.classList.remove('flex');
                document.body.style.overflow = 'auto';
            }
        }
        
        // 7. Download Ticket Function
        async function downloadTicket() {
            try {
                const ticketContent = document.querySelector('#successModal .glass');
                if (!ticketContent) return;
                
                // Use html2canvas to capture the ticket
                const canvas = await html2canvas(ticketContent, {
                    scale: 2,
                    backgroundColor: null,
                    useCORS: true,
                    logging: false
                });
                
                // Convert canvas to data URL
                const dataURL = canvas.toDataURL('image/png');
                
                // Create download link
                const link = document.createElement('a');
                link.href = dataURL;
                link.download = `tiket-${transactionCode.textContent}.png`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                // Show success message
                alert('Tiket berhasil didownload!');
            } catch (error) {
                console.error('Error downloading ticket:', error);
                alert('Gagal mendownload tiket. Silakan coba lagi.');
            }
        }
        
        // 8. Submit Registration Function
        async function submitRegistration(e) {
            e.preventDefault();
            
            // Get event data from form data attributes (safer than relying on currentEvent variable)
            const eventData = {
                name: registrationForm.dataset.eventName,
                date: registrationForm.dataset.eventDate,
                location: registrationForm.dataset.eventLocation
            };
            
            console.log('Form event data:', eventData);
            
            if (!eventData.name || !eventData.date || !eventData.location) {
                alert('Data event tidak tersedia. Silakan tutup form dan coba lagi.');
                return;
            }
            
            // Validate required fields
            const requiredFields = ['full_name', 'email', 'phone', 'gender', 'nik', 'address', 'payment_method'];
            for (const field of requiredFields) {
                const input = registrationForm.querySelector(`[name="${field}"]`);
                if (input && !input.value.trim()) {
                    alert(`Field ${field.replace('_', ' ')} wajib diisi`);
                    input.focus();
                    return;
                }
            }
            
            // Get event price from currentEvent or use default
            const eventPrice = currentEvent ? currentEvent.price : 0;
            
            // Validate file if event is paid
            const paymentProofInput = document.getElementById('payment_proof');
            if (eventPrice > 0 && paymentProofInput && !paymentProofInput.files[0]) {
                if (!confirm('Anda belum mengupload bukti pembayaran. Lanjutkan pendaftaran tanpa bukti?')) {
                    return;
                }
            }
            
            // Validate file size and type
            if (paymentProofInput && paymentProofInput.files[0]) {
                const file = paymentProofInput.files[0];
                const validTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!validTypes.includes(file.type)) {
                    alert('Format file tidak didukung. Gunakan JPG, PNG, atau PDF.');
                    return;
                }
                
                if (file.size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    return;
                }
            }
            
            // Tampilkan loading state
            const submitButton = registrationForm.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
            submitButton.disabled = true;
            
            const formData = new FormData(registrationForm);
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    alert('Token CSRF tidak ditemukan');
                    submitButton.innerHTML = originalButtonText;
                    submitButton.disabled = false;
                    return;
                }
                
                const response = await fetch('/daftar', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (!response.ok) {
                    throw new Error(result.message || 'Terjadi kesalahan saat mendaftar');
                }
                
                if (result.success) {
                    closeRegistrationModal();
                    
                    // Prepare WhatsApp message
                    const participantData = {
                        full_name: formData.get('full_name'),
                        email: formData.get('email'),
                        phone: formData.get('phone'),
                        address: formData.get('address')
                    };
                    
                    console.log('Creating WhatsApp message with event data:', eventData);
                    
                    const whatsappMessage = composeWhatsAppMessage(
                        participantData,
                        eventData,
                        result.transaction_code
                    );
                    
                    // Show success modal with WhatsApp integration
                    showSuccessModal(
                        result.message, 
                        result.transaction_code, 
                        'pending', 
                        whatsappMessage
                    );
                    
                } else {
                    alert('Terjadi kesalahan: ' + result.message);
                    submitButton.innerHTML = originalButtonText;
                    submitButton.disabled = false;
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mendaftar: ' + error.message);
                submitButton.innerHTML = originalButtonText;
                submitButton.disabled = false;
            }
        }
        
        // Add event listeners to all registration buttons
        document.querySelectorAll('[data-event-id]').forEach(button => {
            button.addEventListener('click', function() {
                const eventId = this.getAttribute('data-event-id');
                if (eventId) {
                    openRegistrationModal(eventId);
                }
            });
        });
        
        // Test library availability
        console.log('qrcode library available:', typeof qrcode !== 'undefined' ? 'Yes' : 'No');
        console.log('html2canvas library available:', typeof html2canvas !== 'undefined' ? 'Yes' : 'No');
    });
    </script>
</body>
</html>