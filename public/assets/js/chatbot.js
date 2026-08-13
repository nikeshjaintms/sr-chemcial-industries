/**
 * SR Chemicals Industries - AI Chatbot Engine Frontend Script
 * Modern, Production-Ready, AJAX/Fetch API driven Chatbot
 */

(function () {
  'use strict';

  // State Management
  const state = {
    sessionId: localStorage.getItem('src_chat_session') || generateSessionId(),
    isOpen: false,
    theme: localStorage.getItem('src_chat_theme') || 'light',
    history: []
  };

  localStorage.setItem('src_chat_session', state.sessionId);

  // DOM Elements
  let triggerBtn, widgetContainer, chatBody, chatInput, sendBtn, themeBtn, clearBtn;

  // Initialize Chatbot Widget on DOM ready
  document.addEventListener('DOMContentLoaded', () => {
    injectWidgetMarkup();
    bindEvents();
    applyTheme(state.theme);
    loadHistory();
  });

  function generateSessionId() {
    return 'session_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
  }

  function injectWidgetMarkup() {
    const markup = `
      <button id="srcChatTrigger" class="src-chat-trigger" aria-label="Open SR Chemicals AI Assistant">
        <div class="src-trigger-pulse"></div>
        <span class="src-badge-pulse"></span>
        <div class="src-trigger-icon-wrap">
          <svg class="src-icon-chat" viewBox="0 0 24 24" width="30" height="30" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.477 2 2 6.477 2 12C2 13.89 2.525 15.66 3.438 17.172L2.05 21.328C1.94 21.658 2.025 22.02 2.27 22.264C2.514 22.508 2.876 22.593 3.206 22.483L7.362 21.094C8.875 22.008 10.645 22.532 12.535 22.532C18.058 22.532 22.535 18.055 22.535 12.532C22.535 7.009 18.058 2.486 12.535 2.486H12Z" fill="url(#botGrad)"/>
            <circle cx="8.5" cy="11.5" r="1.5" fill="#0F5286"/>
            <circle cx="15.5" cy="11.5" r="1.5" fill="#0F5286"/>
            <path d="M8.5 15.5C9.5 16.8 11 17.2 12 17.2C13 17.2 14.5 16.8 15.5 15.5" stroke="#0F5286" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M19 4L19.8 5.8L21.6 6.6L19.8 7.4L19 9.2L18.2 7.4L16.4 6.6L18.2 5.8L19 4Z" fill="#FFD700"/>
            <defs>
              <linearGradient id="botGrad" x1="2" y1="2" x2="22" y2="22" gradientUnits="userSpaceOnUse">
                <stop stop-color="#FFFFFF"/>
                <stop offset="1" stop-color="#E2F1FF"/>
              </linearGradient>
            </defs>
          </svg>
          <i class="fa-solid fa-xmark src-icon-close"></i>
        </div>
      </button>

      <div id="srcChatWidget" class="src-chat-widget" data-sr-theme="${state.theme}">
        <div class="src-chat-header">
          <div class="src-header-info">
            <div class="src-avatar-wrap">
              <img src="assets/img/added/blue-logo.png" alt="SRCIL Bot">
              <span class="src-online-dot"></span>
            </div>
            <div>
              <h3 class="src-header-title">SR Chemicals AI</h3>
              <p class="src-header-status"><i class="fa-solid fa-bolt" style="color:#67B346; font-size:10px;"></i> Verified Knowledge Base</p>
            </div>
          </div>
          <div class="src-header-actions">
            <button id="srcClearChat" class="src-btn-icon" title="Clear History">
              <i class="fa-solid fa-rotate-right"></i>
            </button>
          </div>
        </div>

        <div id="srcChatBody" class="src-chat-body"></div>

        <div class="src-chat-footer">
          <form id="srcInputForm" class="src-input-form" onsubmit="return false;">
            <textarea id="srcChatInput" class="src-chat-input" placeholder="Ask about Caustic Soda, purity, export, MSDS..." rows="1"></textarea>
            <button id="srcSendBtn" class="src-send-btn" type="submit" title="Send Message">
              <i class="fa-solid fa-paper-plane"></i>
            </button>
          </form>
        </div>
      </div>
    `;

    const div = document.createElement('div');
    div.innerHTML = markup;
    document.body.appendChild(div);

    triggerBtn = document.getElementById('srcChatTrigger');
    widgetContainer = document.getElementById('srcChatWidget');
    chatBody = document.getElementById('srcChatBody');
    chatInput = document.getElementById('srcChatInput');
    sendBtn = document.getElementById('srcSendBtn');
    clearBtn = document.getElementById('srcClearChat');
  }

  function bindEvents() {
    triggerBtn.addEventListener('click', toggleChat);
    sendBtn.addEventListener('click', handleUserSubmit);
    if (clearBtn) clearBtn.addEventListener('click', clearHistory);

    chatInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        handleUserSubmit();
      }
    });

    chatInput.addEventListener('input', () => {
      chatInput.style.height = 'auto';
      chatInput.style.height = Math.min(chatInput.scrollHeight, 100) + 'px';
    });
  }

  function toggleChat() {
    state.isOpen = !state.isOpen;
    triggerBtn.classList.toggle('open', state.isOpen);
    widgetContainer.classList.toggle('active', state.isOpen);

    if (state.isOpen) {
      chatInput.focus();
      if (chatBody.children.length === 0) {
        showWelcomeMessage();
      }
    }
  }

  function toggleTheme() {
    state.theme = state.theme === 'light' ? 'dark' : 'light';
    localStorage.setItem('src_chat_theme', state.theme);
    applyTheme(state.theme);
  }

  function applyTheme(theme) {
    widgetContainer.setAttribute('data-sr-theme', theme);
    themeBtn.innerHTML = theme === 'dark' ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
  }

  function showWelcomeMessage() {
    const welcome = `Hello! Welcome to **SR Chemical Industries Limited**.\n\nI can help you with product details, purity specs, export countries, water treatment solutions, MSDS sheets, and bulk orders. Please type your query below:`;
    
    appendMessage({
      sender: 'bot',
      text: welcome,
      time: getCurrentTime()
    });
  }

  async function handleUserSubmit() {
    const text = chatInput.value.trim();
    if (!text) return;

    chatInput.value = '';
    chatInput.style.height = 'auto';

    appendMessage({
      sender: 'user',
      text: text,
      time: getCurrentTime()
    });

    showTypingIndicator();

    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const response = await fetch('/api/chatbot/chat', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          message: text,
          session_id: state.sessionId
        })
      });

      const data = await response.json();
      removeTypingIndicator();

      if (data && data.status === 'success') {
        appendMessage({
          sender: 'bot',
          text: data.message,
          time: getCurrentTime(),
          cardType: data.card_type,
          product: data.product,
          products: data.products,
          blogs: data.blogs,
          contact: data.contact,
          suggestions: data.suggestions
        });
      } else {
        appendMessage({
          sender: 'bot',
          text: data && data.message ? data.message : "I couldn't process your request right now. Please contact support at sales@srchemical.com",
          time: getCurrentTime()
        });
      }
    } catch (err) {
      console.error('Chatbot API Error:', err);
      removeTypingIndicator();
      appendMessage({
        sender: 'bot',
        text: "Connection error. Please try again or contact our team at sales@srchemical.com",
        time: getCurrentTime()
      });
    }
  }

  function appendMessage(msg) {
    const row = document.createElement('div');
    row.className = `src-message-row ${msg.sender}`;

    const bubble = document.createElement('div');
    bubble.className = 'src-msg-bubble';
    bubble.innerHTML = renderMarkdown(msg.text);

    // Append Product Card if payload available (Strictly deduplicated by Product ID)
    if (msg.cardType === 'product' && msg.product) {
      bubble.appendChild(createProductCard(msg.product));
    } else if (msg.cardType === 'product_list' && Array.isArray(msg.products)) {
      const seenCardIds = new Set();
      msg.products.forEach(p => {
        if (p && p.id && !seenCardIds.has(p.id)) {
          seenCardIds.add(p.id);
          bubble.appendChild(createProductCard(p));
        } else if (p && !p.id) {
          bubble.appendChild(createProductCard(p));
        }
      });
    }

    // Message Metadata (Timestamp & Copy Button)
    const meta = document.createElement('div');
    meta.className = 'src-msg-meta';
    meta.innerHTML = `
      <span>${msg.time}</span>
      ${msg.sender === 'bot' ? `<button class="src-copy-btn" title="Copy response"><i class="fa-regular fa-copy"></i> Copy</button>` : ''}
    `;

    if (msg.sender === 'bot') {
      const copyBtn = meta.querySelector('.src-copy-btn');
      copyBtn.addEventListener('click', () => {
        navigator.clipboard.writeText(msg.text);
        copyBtn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
        setTimeout(() => copyBtn.innerHTML = '<i class="fa-regular fa-copy"></i> Copy', 2000);
      });
    }

    row.appendChild(bubble);
    row.appendChild(meta);

    // Append Suggested Question Chips if available
    if (msg.suggestions && msg.suggestions.length > 0) {
      const sugWrap = document.createElement('div');
      sugWrap.className = 'src-suggestions-wrap';
      msg.suggestions.forEach(s => {
        const chip = document.createElement('button');
        chip.className = 'src-suggestion-chip';
        chip.innerText = s;
        chip.addEventListener('click', () => {
          chatInput.value = s;
          handleUserSubmit();
        });
        sugWrap.appendChild(chip);
      });
      row.appendChild(sugWrap);
    }

    chatBody.appendChild(row);
    scrollToBottom();
  }

  function createProductCard(p) {
    const card = document.createElement('div');
    card.className = 'src-card-product';
    card.innerHTML = `
      <div class="src-card-header">
        <img src="${p.image_url || 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg'}" alt="${p.name}" class="src-card-img">
        <div class="src-card-title-wrap">
          <h4 class="src-card-title">${p.name}</h4>
          <span class="src-card-badge">${p.purity || 'High Purity'}</span>
        </div>
      </div>
      <div class="src-card-actions">
        <a href="${p.product_url || '#'}" class="src-btn-card src-btn-card-primary">View Product Page</a>
        ${p.msds_url && p.msds_url !== '#' ? `<a href="${p.msds_url}" target="_blank" class="src-btn-card src-btn-card-outline">Download MSDS</a>` : ''}
      </div>
    `;
    return card;
  }

  function showTypingIndicator() {
    removeTypingIndicator();
    const row = document.createElement('div');
    row.className = 'src-message-row bot';
    row.id = 'srcTypingRow';
    row.innerHTML = `
      <div class="src-typing-indicator">
        <span></span><span></span><span></span>
      </div>
    `;
    chatBody.appendChild(row);
    scrollToBottom();
  }

  function removeTypingIndicator() {
    const typing = document.getElementById('srcTypingRow');
    if (typing) typing.remove();
  }

  function scrollToBottom() {
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  function getCurrentTime() {
    const d = new Date();
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  async function loadHistory() {
    try {
      const res = await fetch(`/api/chatbot/history?session_id=${state.sessionId}`);
      const data = await res.json();
      if (data && data.status === 'success' && data.history && data.history.length > 0) {
        chatBody.innerHTML = '';
        data.history.forEach(item => {
          appendMessage({
            sender: 'user',
            text: item.user_query,
            time: new Date(item.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
          });
          appendMessage({
            sender: 'bot',
            text: item.bot_response,
            time: new Date(item.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
          });
        });
      }
    } catch (e) {
      console.log('No chat history loaded');
    }
  }

  async function clearHistory() {
    try {
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      await fetch(`/api/chatbot/clear`, {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ session_id: state.sessionId })
      });
      chatBody.innerHTML = '';
      showWelcomeMessage();
    } catch (e) {
      console.log('Failed to clear history');
    }
  }

  function renderMarkdown(text) {
    if (!text) return '';
    let html = text
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/### (.*)/g, '<h3>$1</h3>')
      .replace(/#### (.*)/g, '<h4>$1</h4>')
      .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
      .replace(/`([^`]+)`/g, '<code>$1</code>')
      .replace(/\n• (.*)/g, '<li>$1</li>')
      .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank">$1</a>')
      .replace(/\n\n/g, '<br><br>')
      .replace(/\n/g, '<br>');
    return html;
  }
})();
