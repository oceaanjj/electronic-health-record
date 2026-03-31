@if (auth()->check() && strtolower((string) auth()->user()->role) === 'nurse')
    <div id="nurse-ai-chatbot" class="fixed bottom-10 right-5 z-[1000]">
        <button id="nurse-ai-chat-toggle" type="button"
            class="group pointer-events-auto relative bg-gradient-to-r from-white via-emerald-50 to-white border border-emerald-300 rounded-full h-14 w-14 hover:w-[150px] hover:scale-[1.03] transition-all duration-300 shadow-[0_10px_30px_rgba(16,185,129,0.35)] hover:shadow-[0_14px_36px_rgba(16,185,129,0.5)] cursor-pointer"
            aria-label="Open Nurse AI Assistant">
            <span
                class="absolute -top-1.5 -right-1.5 inline-flex h-3 w-3 rounded-full bg-emerald-500 ring-4 ring-emerald-100 animate-pulse"></span>

            <div
                class="absolute inset-0 flex items-center justify-center group-hover:static group-hover:flex group-hover:items-center group-hover:justify-start group-hover:pl-4 group-hover:pr-4 group-hover:gap-2">
                <span class="material-symbols-outlined text-[24px] text-emerald-700">smart_toy</span>
                <span
                    class="hidden group-hover:inline-block font-alte font-bold text-[11px] uppercase tracking-widest whitespace-nowrap text-emerald-700">
                    AI Chatbot
                </span>
            </div>
        </button>
    </div>

    <div id="nurse-ai-chat-panel" class="fixed bottom-24 right-5 z-[1000] w-[90%] md:w-[360px] hidden">
        <div
            class="pointer-events-auto bg-white/95 backdrop-blur-lg border border-emerald-200 rounded-xl p-4 shadow-[0_16px_44px_rgba(16,185,129,0.28)]">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2 text-emerald-700">
                    <div class="bg-emerald-100 p-1.5 rounded-lg shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">chat</span>
                    </div>
                    <h3 class="font-alte font-bold text-[11px] uppercase tracking-widest">AI Chatbot</h3>
                </div>
                <div class="flex gap-2">
                    <button id="nurse-ai-chat-clear" type="button"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-full px-3 py-1 text-[10px] font-semibold uppercase tracking-wide transition-colors cursor-pointer">
                        Clear
                    </button>
                    <button id="nurse-ai-chat-close" type="button"
                        class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-full h-7 w-7 flex items-center justify-center transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>
            </div>

            <div id="nurse-ai-chat-messages" class="h-[280px] overflow-y-auto pr-1 space-y-2 text-xs">
                <!-- Messages will be loaded from localStorage -->
            </div>

            <form id="nurse-ai-chat-form" class="mt-3 flex gap-2">
                <input id="nurse-ai-chat-input" type="text" maxlength="500" required
                    class="flex-1 border border-emerald-200 rounded-lg px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400"
                    placeholder="Type your question here..." />
                <button id="nurse-ai-chat-send" type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg px-3 py-2 text-xs font-semibold transition-colors cursor-pointer">
                    Send
                </button>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const toggleBtn = document.getElementById('nurse-ai-chat-toggle');
            const closeBtn = document.getElementById('nurse-ai-chat-close');
            const clearBtn = document.getElementById('nurse-ai-chat-clear');
            const panel = document.getElementById('nurse-ai-chat-panel');
            const form = document.getElementById('nurse-ai-chat-form');
            const input = document.getElementById('nurse-ai-chat-input');
            const messages = document.getElementById('nurse-ai-chat-messages');
            const sendBtn = document.getElementById('nurse-ai-chat-send');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            let pendingConfirmAction = null;
            const STORAGE_KEY = 'nurse-ai-chat-history';

            if (!toggleBtn || !closeBtn || !clearBtn || !panel || !form || !input || !messages || !sendBtn || !csrfToken) {
                return;
            }

            // Load chat history from localStorage
            const loadChatHistory = () => {
                try {
                    const history = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
                    if (history.length === 0) {
                        // Show welcome message if no history
                        messages.innerHTML = '<div class="bg-emerald-50 text-emerald-900 rounded-lg p-2.5">Hi there! I\'m here to help with anything you need in the app.</div>';
                    } else {
                        messages.innerHTML = ''; // Clear first
                        history.forEach(item => {
                            if (item.type === 'message') {
                                appendMessage(item.text, item.isUser, item.isAI, false); // Don't save again
                            } else if (item.type === 'actions') {
                                appendActions(item.actions, false); // Don't save again
                            }
                        });
                    }
                } catch (e) {
                    console.error('Failed to load chat history', e);
                    messages.innerHTML = '<div class="bg-emerald-50 text-emerald-900 rounded-lg p-2.5">Hi there! I\'m here to help with anything you need in the app.</div>';
                }
            };

            // Save chat history to localStorage
            const saveChatHistory = (item) => {
                try {
                    const history = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
                    history.push(item);
                    // Keep only last 50 items
                    if (history.length > 50) {
                        history.shift();
                    }
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(history));
                } catch (e) {
                    console.error('Failed to save chat history', e);
                }
            };

            // Clear chat history
            const clearChatHistory = () => {
                localStorage.removeItem(STORAGE_KEY);
                messages.innerHTML = '<div class="bg-emerald-50 text-emerald-900 rounded-lg p-2.5">Hi there! I\'m here to help with anything you need in the app.</div>';
                pendingConfirmAction = null;
            };

            const appendMessage = (text, isUser = false, isAI = false, save = true) => {
                const bubble = document.createElement('div');
                bubble.className = isUser
                    ? 'ml-6 bg-emerald-600 text-white rounded-lg p-3 shadow-sm'
                    : 'mr-6 bg-white text-gray-800 rounded-lg p-3 shadow-md border border-gray-200';
                
                if (isUser) {
                    bubble.textContent = text;
                } else {
                    // Format AI responses with better typography
                    const formatted = text
                        .replace(/\*\*(.*?)\*\*/g, '<strong class="font-semibold">$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em>$1</em>')
                        .replace(/`([^`]+)`/g, '<code class="bg-gray-100 px-1.5 py-0.5 rounded text-sm font-mono text-emerald-700">$1</code>')
                        .replace(/^- (.+)$/gm, '<li class="ml-4">$1</li>')
                        .replace(/^(\d+)\. (.+)$/gm, '<li class="ml-4">$2</li>')
                        .replace(/\n\n/g, '</p><p class="mt-2">');
                    
                    // Add AI indicator if it's an AI response
                    if (isAI) {
                        bubble.innerHTML = `
                            <div class="flex items-start gap-2">
                                <span class="material-symbols-outlined text-[18px] text-emerald-600 flex-shrink-0 mt-0.5">smart_toy</span>
                                <div class="flex-1">
                                    <p class="text-xs font-semibold text-emerald-600 mb-1">AI Response</p>
                                    <p class="text-sm leading-relaxed">${formatted}</p>
                                </div>
                            </div>
                        `;
                    } else {
                        bubble.innerHTML = '<p class="text-sm leading-relaxed">' + formatted + '</p>';
                    }
                }
                
                messages.appendChild(bubble);
                messages.scrollTop = messages.scrollHeight;
                
                // Save to history
                if (save) {
                    saveChatHistory({ type: 'message', text, isUser, isAI });
                }
                
                return bubble;
            };

            const appendActions = (actions, save = true) => {
                if (!Array.isArray(actions) || actions.length === 0) return;
                const wrap = document.createElement('div');
                wrap.className = 'mr-6 flex flex-wrap gap-2 mt-2';

                actions.forEach((action) => {
                    if (!action?.label || !action?.url) return;
                    const link = document.createElement('a');
                    link.href = action.url;
                    link.className = 'inline-flex items-center rounded-lg border-2 border-emerald-400 bg-emerald-50 px-3 py-2 text-sm text-emerald-700 font-semibold hover:bg-emerald-500 hover:text-white hover:border-emerald-500 transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md';
                    link.innerHTML = `<span class="material-symbols-outlined text-[18px] mr-1.5">arrow_forward</span>${action.label}`;
                    wrap.appendChild(link);
                });

                if (wrap.childElementCount > 0) {
                    messages.appendChild(wrap);
                    messages.scrollTop = messages.scrollHeight;
                    
                    // Save to history
                    if (save) {
                        saveChatHistory({ type: 'actions', actions });
                    }
                }
            };

            // Load chat history on page load
            loadChatHistory();

            toggleBtn.addEventListener('click', () => panel.classList.remove('hidden'));
            closeBtn.addEventListener('click', () => panel.classList.add('hidden'));
            clearBtn.addEventListener('click', () => {
                if (confirm('Clear all chat history?')) {
                    clearChatHistory();
                }
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                const message = input.value.trim();
                if (!message) return;

                appendMessage(message, true);
                input.value = '';

                if (pendingConfirmAction && /^(yes|yep|yeah|sure|ok|okay)$/i.test(message)) {
                    appendMessage('Great! Opening your requested page now.');
                    appendActions([{
                        label: pendingConfirmAction.label,
                        url: pendingConfirmAction.url
                    }]);
                    pendingConfirmAction = null;
                    input.focus();
                    return;
                }

                if (pendingConfirmAction && /^(no|nope|nah|cancel)$/i.test(message)) {
                    appendMessage('No problem. Tell me what you want to do and I will help.');
                    pendingConfirmAction = null;
                    input.focus();
                    return;
                }

                sendBtn.disabled = true;
                sendBtn.textContent = '...';

                // Show loading indicator
                const loadingBubble = document.createElement('div');
                loadingBubble.className = 'mr-6 bg-white text-gray-800 rounded-lg p-3 shadow-md border border-gray-200';
                loadingBubble.innerHTML = `
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-emerald-600 animate-pulse">smart_toy</span>
                        <span class="text-sm text-gray-600">AI is thinking...</span>
                    </div>
                `;
                messages.appendChild(loadingBubble);
                messages.scrollTop = messages.scrollHeight;

                try {
                    const response = await fetch("{{ route('nurse-ai-chat.ask') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ message })
                    });

                    // Remove loading indicator
                    loadingBubble.remove();

                    const data = await response.json();
                    if (!response.ok) {
                        appendMessage(data.message || 'Failed to get response.');
                    } else {
                        appendMessage(data.reply || 'No response received.', false, data.is_ai || false);
                        appendActions(data.actions || []);
                        pendingConfirmAction = data.confirm_action || null;
                    }
                } catch (error) {
                    appendMessage('Network error. Please try again.');
                } finally {
                    sendBtn.disabled = false;
                    sendBtn.textContent = 'Send';
                    input.focus();
                }
            });

            toggleBtn.classList.add('cursor-pointer');
            closeBtn.classList.add('cursor-pointer');
        })();
    </script>
@endif