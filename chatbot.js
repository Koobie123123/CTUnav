// Wait for the document to fully load before running the script
document.addEventListener('DOMContentLoaded', function () {
    // Get HTML elements
    const userInput = document.getElementById('userInput');
    const sendButton = document.getElementById('sendButton');
    const chatlog = document.getElementById('chatlog');


    // Event: When user clicks the send button
    sendButton.addEventListener('click', function () {
        sendMessage();
    });

    // Event: When user presses Enter key in input field
    userInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Focus the input field when page loads
    userInput.focus();

    // Function to handle sending user messages
    function sendMessage() {
        const input = userInput.value.trim();
        if (input === '') return;

        addMessage(input, 'user-message');  // Show user message
        userInput.value = '';              // Clear input field
        showTypingIndicator();             // Show typing animation

        // Simulate bot typing delay
        setTimeout(() => {
            hideTypingIndicator();
            getBotResponse(input);         // Get and show bot reply
        }, 800);
    }

    // Function to append messages to chat log
    function addMessage(message, messageType) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${messageType}`;
        messageDiv.innerHTML = message;
        chatlog.appendChild(messageDiv);
        chatlog.scrollTop = chatlog.scrollHeight;
    }

    // Show typing dots (bot is typing...)
    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typing-indicator';
        typingDiv.className = 'message bot-message typing-indicator';
        typingDiv.innerHTML = '<span></span><span></span><span></span>';
        chatlog.appendChild(typingDiv);
        chatlog.scrollTop = chatlog.scrollHeight;
    }

    // Hide typing indicator
    function hideTypingIndicator() {
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    // Core function: Handles bot responses based on user input
    function getBotResponse(input) {
        input = input.toLowerCase();
        let response = "";

        // 1. PROGRAM-SPECIFIC REQUIREMENTS
        if (input.includes("requirements") && (input.includes("bsit") || input.includes("information technology"))) {
            response = `For BSIT enrollment, you need:<br>
            - Completed application form<br>
            - Original Grade 12 report card<br>
            - PSA birth certificate<br>
            - Good Moral Certificate<br>
            - 2×2 ID photos (white background)<br>
            - Certificate of Senior High School completion`;
        }
        else if (input.includes("requirements") && (input.includes("bscs") || input.includes("computer science"))) {
            response = `For BSCS requirements:<br>
            - Same as BSIT requirements plus:<br>
            - Math proficiency certificate (if available)<br>
            - Programming portfolio (optional for scholarships)`;
        }
        else if (input.includes("requirements") && input.includes("engineering")) {
            response = `Engineering program requirements:<br>
            - All standard documents plus:<br>
            - STEM strand completion (for SHS graduates)<br>
            - Passing score in Math and Science entrance exams`;
        }

        // 2. ENROLLMENT PROCESS
        else if (input.includes("online enrollment") || input.includes("how to enroll")) {
            response = `Online enrollment steps:<br>
            1. Create account at <a href="https://enroll.ctu.edu.ph" target="_blank">enroll.ctu.edu.ph</a><br>
            2. Complete application form<br>
            3. Upload required documents<br>
            4. Pay registration fee (if applicable)<br>
            5. Wait for confirmation email`;
        }
        else if (input.includes("enrollment schedule") || input.includes("registration period")) {
            response = `Enrollment periods for AY 2023-2024:<br>
            - 1st Semester: June 5 - July 15<br>
            - 2nd Semester: November 6 - December 1<br>
            - Summer Term: April 3 - April 15`;
        }

        // 3. ACADEMIC INFORMATION
        else if (input.includes("curriculum") && input.includes("bsit")) {
            response = `BSIT curriculum includes:<br>
            - Programming Fundamentals<br>
            - Database Systems<br>
            - Web Development<br>
            - Network Administration<br>
            - Capstone Project<br>
            <a href="https://www.ctu.edu.ph/programs/bsit" target="_blank">See full curriculum here</a>`;
        }
        else if (input.includes("subjects") || input.includes("courses offered")) {
            response = `You can view all offered courses at:<br>
            <a href="https://www.ctu.edu.ph/academics" target="_blank">www.ctu.edu.ph/academics</a>`;
        }

        // 4. SCHOLARSHIPS AND FEES
        else if (input.includes("scholarship") || input.includes("financial aid")) {
            response = `Available scholarships:<br>
            - Academic Excellence Scholarship (90+ average)<br>
            - Athletic Scholarship<br>
            - CHED Scholarship<br>
            - Dean's Grant<br>
            Apply at the Scholarship Office, 2F Administration Building`;
        }
        else if (input.includes("tuition fee") || input.includes("how much to pay")) {
            response = `Tuition fees per semester:<br>
            - BS Programs: ₱15,000-₱20,000<br>
            - Graduate Programs: ₱10,000-₱15,000<br>
            - Laboratory fees extra<br>
            <i>Free tuition available for qualified students under RA 10931</i>`;
        }

        // 5. DOCUMENT REQUESTS
        else if (input.includes("diploma") || input.includes("certificate of graduation")) {
            response = `Requesting graduation documents:<br>
            - Submit request at Registrar's Office<br>
            - Processing time: 7-10 working days<br>
            - Fee: ₱250 for diploma, ₱150 per certification`;
        }
        else if (input.includes("grade") && input.includes("copy")) {
            response = `For official transcripts:<br>
            - Submit request at Registrar's Office<br>
            - Processing time: 3-5 days<br>
            - Fee: ₱150 first page + ₱50 succeeding pages`;
        }

        // 6. CAMPUS FACILITIES
        else if (input.includes("library") || input.includes("research center")) {
            response = `Library hours:<br>
            - Monday-Friday: 8AM-7PM<br>
            - Saturday: 9AM-5PM<br>
            - Sunday: Closed<br>
            Access our digital library at <a href="https://library.ctu.edu.ph" target="_blank">library.ctu.edu.ph</a>`;
        }
        else if (input.includes("laboratory") || input.includes("computer lab")) {
            response = `Computer lab schedules:<br>
            - Open during class hours<br>
            - After-hours access requires faculty approval<br>
            - 24/7 access for thesis students with valid ID`;
        }

        // 7. CONTACT INFORMATION
        else if (input.includes("contact") || input.includes("email") || input.includes("phone")) {
            response = `Contact information:<br>
            - Registrar: registrar@ctu.edu.ph | (032) 123-4567<br>
            - Admissions: admissions@ctu.edu.ph | (032) 123-4568<br>
            - Cashier: cashier@ctu.edu.ph | (032) 123-4569<br>
            Visit us at: CTU Main Campus, M.J. Cuenco Ave, Cebu City`;
        }

        // 8. FALLBACK RESPONSE (when user input doesn't match any known category)
        else {
            response = `I couldn't find information about that. Here are topics I can help with:<br>
            - Program requirements (BSIT, BSCS, etc.)<br>
            - Enrollment process and schedules<br>
            - Scholarships and tuition fees<br>
            - Document requests<br>
            - Campus facilities and services<br>
            Try being more specific with your question.`;
        }

        // Show bot reply
        addMessage(response, 'bot-message');
    }
        // Handle click on suggested questions
    document.querySelectorAll('.suggested-question').forEach(button => {
        button.addEventListener('click', function () {
            const question = this.textContent;
            simulateUserQuestion(question);
        });
    });

    // Simulate typing and responding for predefined questions
    function simulateUserQuestion(question) {
        addMessage(question, 'user-message');
        showTypingIndicator();

        setTimeout(() => {
            hideTypingIndicator();
            getBotResponse(question);
        }, 800);
    }

});
