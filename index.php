<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTU Tuburan Interactive Campus Tour</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Global Styles */
        :root {
            --primary: #1a5276;
            --secondary: #2e86c1;
            --accent: #f39c12;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; */
              font-family:'Roboto',sans-serif;

        }
        
        body {
            color: #333;
            line-height: 1.6;
            background-color: #f9f9f9;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: var(--accent);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            parse_str(strstr($file, '?'), $params);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background-color: #e67e22;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .btn-secondary {
            background-color: var(--secondary);
        }
        
        .btn-secondary:hover {
            background-color: #1a5276;
        }
        
        section {
            padding: 80px 0;
        }
        
        h1, h2, h3 {
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        p {
            margin-bottom: 15px;
        }
        
    /* NAVBAR */

       .navbar {
      height: 60px;
      background-color: #f3f3f3ff;
      color: white;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 20px;
      box-shadow: 0 2px 4px rgb(0 0 0 / 0.1);
      border-bottom: 1px solid #ff7a00;
    }
    .navbar-left { display: flex; align-items: center; gap: 12px; }
    .navbar-left img { height: 40px; width: 40px; border-radius: 50%; }
    .navbar-left .logo-text { font-weight: bold; font-size: 16px; white-space: nowrap; color: #ff7a00;}
    .navbar-right { display: flex; align-items: center; gap: 20px; }
    .navbar-right img.profile {
      width: 34px; height: 34px; border-radius: 50%; object-fit: cover;
    }


/* Navigation links container */
.nav-links {
    list-style: none;
    display: flex; /* makes li go horizontal */
    gap: 20px;     /* space between links */
    margin: 0;
    padding: 0;
}

/* Individual link styling */
.nav-links li a {
    text-decoration: none;
    color: black;
    font-weight: 500;
    transition: color 0.3s ease;
}

/* Hover effect */
.nav-links li a:hover {
    color: #ff7a00; /* change highlight color */
}

/* Login button special style */
.login-btn {
    border-radius: 5px;
    color: black;!important;
    transition: 0.3s ease;
}


        /* Hero Section */
        .hero {
          
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 150px 0 100px;
            margin-top: 80px;
            height:85vh;
        }

                /* Video Background */
        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
        }

        /* Optional overlay for readability */
        .hero::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(31, 33, 33, 0.55); /* transparent black overlay */
            z-index: -1;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: white;
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
        }
        
        .hero-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        
        /* Map Section */
        .map-section {
            background-color: white;
            text-align: center;
        }

        .map-section h2 {
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .map-container {
            position: relative;
            width: 100%;
            height: 600px;
            margin: 30px auto;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background-color: #eaf2f8;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .map-placeholder {
            width: 90%;
            height: 90%;
            background-color: #d4e6f1;
            border: 2px dashed var(--secondary);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: var(--primary);
        }
        
        .map-features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
        }
        
        .feature-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            width: 250px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 15px;
        }
        
        /* About Section */
        .about {
            background-color: var(--light);
            text-align: center;
        }
        
        /* Features Section */
        .features {
            background-color: white;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .feature-item {
            text-align: center;
            padding: 30px;
            border-radius: 8px;
            background-color: var(--light);
            transition: transform 0.3s ease;
        }
        
        .feature-item:hover {
            transform: translateY(-5px);
        }
        
        .feature-item i {
            font-size: 3rem;
            color: var(--secondary);
            margin-bottom: 20px;
        }
        
        /* Footer */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-logo {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .footer-logo img {
            height: 40px;
            margin-right: 10px;
        }
        
        .footer-links h3 {
            color: white;
            margin-bottom: 20px;
        }
        
        .footer-links ul {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: #ddd;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: var(--accent);
        }
        
        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .social-links a {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255,255,255,0.1);
            border-radius: 50%;
            color: white;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background-color: var(--accent);
            transform: translateY(-3px);
            color: white;
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        /* Chatbot */
        .chatbot {
            position: fixed;
            bottom: 50px;
            right: 30px;
            z-index: 1000;
            
        }
        
        .chat-icon {
            width: 60px;
            height: 60px;
            background-color: var(--accent);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .chat-icon:hover {
            background-color: #e67e22;
            transform: scale(1.1);
        }
        
        .chat-window {
            position: absolute;
            bottom: 70px;
            right: 0;
            width: 350px;
            height: 450px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        
        .chat-header {
            background-color: var(--accent);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chat-header h3 {
            color: white;
            margin: 0;
        }
        
        .close-chat {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        .chat-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
        }
        
        .message {
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 18px;
            max-width: 80%;
        }
        
        .bot-message {
            background-color: #f0f0f0;
            border-top-left-radius: 4px;
            align-self: flex-start;
        }
        
        .user-message {
            background-color: var(--accent);
            color: white;
            border-top-right-radius: 4px;
            align-self: flex-end;
            margin-left: auto;
        }
        
        .chat-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #eee;
        }
        
        .chat-input input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
        }
        
        .send-button {
            background-color: var(--accent);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-left: 10px;
            cursor: pointer;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .map-container {
                height: 300px;
            }
            
            .chat-window {
                width: 300px;
                height: 400px;
            }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar"> 
    <div class="navbar-left">
        <!-- Logo clickable -->
        <a href="index.php">
            <img src="images/Logo.png" alt="Logo" />
        </a>
        <span class="logo-text">CTU-TUBURAN</span>
    </div>
    
    <div class="navbar-right">
        <ul class="nav-links">                    
            <li><a href="user_dashboard.php">Campus Map</a></li>  
            <li><a href="virtual_tour.php">Virtual Tour</a></li> 
            <li><a href="login.php" class="login-btn">Login</a></li>
        </ul>
    </div>
</nav>

    <!-- Hero Section -->
    <section class="hero">
    <!-- Video Background -->
    <video autoplay muted loop playsinline class="hero-video">
        <source src="images/CTU promotional Video- bsit.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Hero Content -->
    <h1>Explore CTU Tuburan Like Never Before</h1>
    <p>Interactive 3D Campus • Find Buildings & Offices • Get Directions • CTU Chatbot</p>
    <div class="hero-buttons">
        <a href="#map" class="btn">Start Virtual Tour</a>
        <a href="#" class="btn btn-secondary" id="open-chat">CTU Chatbot</a>
    </div>
</section>


    <!-- Interactive 3D Map Section -->
    <section class="map-section" id="map">
        <div class="container">
            <h2>Interactive 3D Campus Map</h2>
            <p>Navigate through our campus with our interactive 3D map. Find buildings, offices, and get directions instantly.</p>
            
            <div class="map-container">
                <div class="map-placeholder">
                    <iframe width="100%" height="640" frameborder="0" allow="xr-spatial-tracking; gyroscope; accelerometer" allowfullscreen scrolling="no" src="https://kuula.co/share/collection/7DbC3?logo=1&info=0&logosize=105&fs=1&vr=0&zoom=1&sd=1&gyro=0&thumbs=-1&margin=7"></iframe>
                </div>
            </div>
            
            <div class="map-features">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Building Information</h3>
                    <p>Hover or click on any building to see details about offices and departments.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Smart Search</h3>
                    <p>Type the name of any office or department to find its location instantly.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <h3>Get Directions</h3>
                    <p>Find the best route from your current location to any destination on campus.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <h2>About CTU Tuburan Virtual Tour</h2>
            <p>The CTU Tuburan Virtual Interactive Tour helps students, visitors, and staff explore the campus with ease. No more getting lost—just click, search, and go.</p>
            <p>Our mission is to make campus navigation simple and intuitive for everyone, whether you're a first-time visitor or a returning student.</p>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2>Key Features</h2>
            <p>Discover all the ways our virtual campus tour can enhance your CTU experience.</p>
            
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-vr-cardboard"></i>
                    <h3>Virtual Tour</h3>
                    <p>Walk through campus buildings in immersive 3D from anywhere.</p>
                </div>
                
                <div class="feature-item">
                    <i class="fas fa-search-location"></i>
                    <h3>Smart Search & Directions</h3>
                    <p>Quickly find services and locations with our intelligent search system.</p>
                </div>
                
                <div class="feature-item">
                    <i class="fas fa-robot"></i>
                    <h3>Chatbot Guide</h3>
                    <p>Get instant answers to common questions with our AI assistant.</p>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo">
                        <img src="admin/Logo.png" alt="CTU Logo">
                        <span style="color: white; font-weight: bold;">CTU Tuburan</span>
                    </div>
                    <p>Excellence in Service, Quality Education</p>
                    <p>National Highway, Poblacion. 8, Tuburan, Cebu, Philippines 6043</p>
                </div>
                
                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#about">About</a></li>
                        <li><a href="#map">Campus Map</a></li>
                        <li><a href="#">Virtual Tour</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#contact">Contact Us</a></li>
                        <li><a href="#chatbot">Chatbot</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h3>Contact Us</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> Poblacion 8, Tuburan, Cebu</li>
                        <li><i class="fas fa-phone"></i> (+93) 9123 456 789</li>
                        <li><i class="fas fa-envelope"></i> ctutuburan@ctu.edu.ph</li>
                    </ul>
                    
                    <div class="social-links">
                        <a href="https://www.facebook.com/share/19jQTGq7BH/"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.ctu.edu.ph/tuburan/"><i class="fas fa-globe"></i></a>
                        <a href="mailto:ctutuburan@ctu.edu.ph"><i class="fas fa-envelope"></i></a>
                        <a href="https://www.youtube.com/@ctutuburan" target="_blank"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2025 CTU Tuburan. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Chatbot -->
     <section class="chatbot" id="chatbot">
    <div class="chatbot">
        <div class="chat-icon" id="chat-icon">
            <i class="fas fa-comment-dots"></i>
        </div>
        
        <div class="chat-window" id="chat-window">
            <div class="chat-header">
                <h3>CTU Assistant</h3>
                <button class="close-chat" id="close-chat">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="chat-messages" id="chat-messages">
                <div class="message bot-message">
                    Hello! I'm CTU Assistant. How can I help you today?
                </div>
            </div>
            
            <div class="chat-input">
                <input type="text" id="user-input" placeholder="Type your message...">
                <button class="send-button" id="send-button">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
    </section>

    <script>
        // Chatbot functionality
        document.addEventListener('DOMContentLoaded', function() {
            const chatIcon = document.getElementById('chat-icon');
            const chatWindow = document.getElementById('chat-window');
            const closeChat = document.getElementById('close-chat');
            const openChatBtn = document.getElementById('open-chat');
            const userInput = document.getElementById('user-input');
            const sendButton = document.getElementById('send-button');
            const chatMessages = document.getElementById('chat-messages');
            
            // Open chat window
            chatIcon.addEventListener('click', function() {
                chatWindow.style.display = 'flex';
            });
            
            openChatBtn.addEventListener('click', function(e) {
                e.preventDefault();
                chatWindow.style.display = 'flex';
                userInput.focus();
            });
            
            // Close chat window
            closeChat.addEventListener('click', function() {
                chatWindow.style.display = 'none';
            });
            
            // Send message
            function sendMessage() {
                const message = userInput.value.trim();
                if (message === '') return;
                
                // Add user message
                const userMessage = document.createElement('div');
                userMessage.classList.add('message', 'user-message');
                userMessage.textContent = message;
                chatMessages.appendChild(userMessage);
                
                // Clear input
                userInput.value = '';
                
                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
                
                // Generate bot response
// Generate bot response from database
setTimeout(function() {
    fetch('chatbot_backend.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'message=' + encodeURIComponent(message)
    })
    .then(response => response.json())
    .then(data => {
        const botMessage = document.createElement('div');
        botMessage.classList.add('message', 'bot-message');
        botMessage.textContent = data.reply;
        chatMessages.appendChild(botMessage);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    })
    .catch(error => {
        console.error('Error:', error);
    });
}, 500);

            }
            
            sendButton.addEventListener('click', sendMessage);
            
            userInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        });
    </script>
</body>
</html>
</html>