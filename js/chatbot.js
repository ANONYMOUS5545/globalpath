// ============================================================
// Global Path Africa - PathBot AI Chatbot
// ============================================================

let chatOpen = false;
let chatHistory = [];
const SITE_URL = document.body?.dataset.siteUrl || document.querySelector('meta[name="site-url"]')?.content || '';

function toggleChatbot() {
    chatOpen = !chatOpen;
    const window_el = document.getElementById('chatbotWindow');
    const badge = document.getElementById('chatBadge');
    const icon = document.getElementById('chatIcon');
    
    window_el.style.display = chatOpen ? 'block' : 'none';
    if (badge) badge.style.display = 'none';
    
    if (icon) {
        icon.className = chatOpen ? 'fas fa-times' : 'fas fa-comments';
    }
    
    if (chatOpen) {
        document.getElementById('chatbotInput')?.focus();
    }
}

function quickReply(message) {
    document.getElementById('chatbotInput').value = message;
    sendChatMessage();
}

async function sendChatMessage() {
    const input = document.getElementById('chatbotInput');
    const message = input.value.trim();
    if (!message) return;
    
    input.value = '';
    
    // Display user message
    appendMessage('user', message);
    chatHistory.push({ role: 'user', content: message });
    
    // Show typing indicator
    const typingId = showTyping();
    
    try {
        const response = await fetch(SITE_URL + '/api/chatbot.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message, history: chatHistory.slice(-10) })
        });
        
        const data = await response.json();
        removeTyping(typingId);
        
        if (data.success && data.reply) {
            appendMessage('bot', data.reply, data.escalated);
            chatHistory.push({ role: 'assistant', content: data.reply });
            
            if (data.escalated) {
                appendEscalation();
            }
        } else {
            appendMessage('bot', 'I apologize, I\'m having trouble responding right now. Please try again or contact us via WhatsApp: +254 792 579 974');
        }
    } catch (error) {
        removeTyping(typingId);
        appendMessage('bot', '⚠️ Connection issue. Please check your internet or reach us on WhatsApp: +254 792 579 974');
    }
    
    scrollToBottom();
}

function appendMessage(role, content, escalated = false) {
    const messages = document.getElementById('chatbotMessages');
    const div = document.createElement('div');
    div.className = role === 'user' ? 'user-message' : 'bot-message';
    
    const bubble = document.createElement('div');
    bubble.className = role === 'user' ? 'user-bubble' : 'bot-bubble';
    bubble.innerHTML = formatMessage(content);
    div.appendChild(bubble);
    
    messages.appendChild(div);
    scrollToBottom();
}

function appendEscalation() {
    const messages = document.getElementById('chatbotMessages');
    const div = document.createElement('div');
    div.className = 'bot-message';
    div.innerHTML = `
        <div class="bot-bubble" style="background:#fff3cd;border-color:#ffc107;">
            <strong>🔔 Escalated to Support Team</strong><br>
            A human agent will follow up. For urgent help:<br>
            <a href="https://wa.me/254792579974?text=I+need+help+with+my+application" target="_blank" style="color:#25d366;font-weight:600;">
                <i class="fab fa-whatsapp"></i> Chat on WhatsApp
            </a>
        </div>
    `;
    messages.appendChild(div);
    scrollToBottom();
}

function showTyping() {
    const messages = document.getElementById('chatbotMessages');
    const div = document.createElement('div');
    const id = 'typing-' + Date.now();
    div.id = id;
    div.className = 'bot-message';
    div.innerHTML = `
        <div class="bot-bubble">
            <div class="typing-dots">
                <span></span><span></span><span></span>
            </div>
        </div>
    `;
    messages.appendChild(div);
    scrollToBottom();
    return id;
}

function removeTyping(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
}

function scrollToBottom() {
    const messages = document.getElementById('chatbotMessages');
    if (messages) messages.scrollTop = messages.scrollHeight;
}

function formatMessage(text) {
    // Convert markdown-ish formatting
    return text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/\n/g, '<br>')
        .replace(/https?:\/\/[^\s]+/g, url => `<a href="${url}" target="_blank" style="color:var(--primary)">${url}</a>`);
}

// Show welcome badge after delay
setTimeout(() => {
    const badge = document.getElementById('chatBadge');
    if (badge && !chatOpen) badge.style.display = 'flex';
}, 5000);
