
jQuery(document).ready(function($) {
    if (typeof worknoonChatSettings === 'undefined') return;

    const settings = worknoonChatSettings;
    let socket = null;
    let currentConversationId = 'wp_sync_room_global'; 
    let isTyping = false;
    let typingTimeout = null;


    $('#worknoon-chat-trigger').on('click', function() {
        $('#worknoon-chat-panel').toggleClass('hidden');
        $('#worknoon-widget-badge').addClass('hidden').text('0'); // Reset unread bubble counters on look
        
        if (!socket) {
            initializeRealTimeSocketConnection();
        }
    });

    $('#worknoon-panel-close').on('click', function() {
        $('#worknoon-chat-panel').addClass('hidden');
    });

    function initializeRealTimeSocketConnection() {

        socket = io(settings.nodeServer, { transports: ['websocket'] });

        socket.on('connect', function() {
            console.log('📡 [WordPress Widget] Successfully paired with Node clusters.');
            socket.emit('join_channel', { channelId: currentConversationId });
        });


        socket.on('receive_message', function(payload) {
            const feed = $('#worknoon-widget-feed');
            const isMe = payload.senderName === settings.currentUser;
            

            const timeStr = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            const chatBubble = `
                <div class="flex flex-col w-full ${isMe ? 'items-end' : 'items-start'} mb-1 animate-fadeIn">
                    <div class="p-2.5 rounded-2xl border max-w-[85%] leading-relaxed ${isMe ? 'bg-black border-zinc-950 text-white rounded-tr-none' : 'bg-white border-gray-100 text-gray-800 rounded-tl-none'}">
                        <p class="m-0">${payload.text || payload.messageBody}</p>
                    </div>
                    <span class="text-[8px] text-gray-400 font-bold mt-0.5 px-1">${payload.senderName} • ${timeStr}</span>
                </div>
            `;

            feed.append(chatBubble);
            feed.scrollTop(feed[0].scrollHeight); 


            if ($('#worknoon-chat-panel').hasClass('hidden')) {
                let currentBadgeCount = parseInt($('#worknoon-widget-badge').text()) || 0;
                $('#worknoon-widget-badge').removeClass('hidden').text(currentBadgeCount + 1);
            }

            // Sync down to custom database tables via custom REST endpoints if message originates from client widget
            if (isMe) {
                dispatchBackupPostPayloadToWordPressDatabase(payload);
            }
        });

        // 🚀 BONUS: Typing Indicator Broadcast Observer Handlers
        socket.on('user_typing_broadcast', function(payload) {
            if (payload.channelId === currentConversationId && payload.senderName !== settings.currentUser) {
                if (payload.isTyping) {
                    $('#worknoon-widget-typing').removeClass('hidden').text(payload.userName + ' is typing...');
                } else {
                    $('#worknoon-widget-typing').addClass('hidden');
                }
            }
        });
    }


    $('#worknoon-widget-input').on('keypress', function() {
        if (!socket) return;
        
        if (!isTyping) {
            isTyping = true;
            socket.emit('user_typing', { channelId: currentConversationId, userName: settings.currentUser, isTyping: true });
        }

        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(function() {
            isTyping = false;
            socket.emit('user_typing', { channelId: currentConversationId, userName: settings.currentUser, isTyping: false });
        }, 2000);
    });

    // Form data processing submission loop block
    $('#worknoon-widget-form').on('submit', function(e) {
        e.preventDefault();
        const inputField = $('#worknoon-widget-input');
        const textMessageValue = inputField.val().trim();

        if (!textMessageValue || !socket) return;

        const outboundPayload = {
            channelId: currentConversationId,
            senderName: settings.currentUser,
            senderRole: settings.currentRole,
            text: textMessageValue,
            messageBody: textMessageValue,
            productId: settings.currentProductId,
            orderId: settings.currentOrderId
        };

        socket.emit('send_message', outboundPayload);
        inputField.val('');
    });

    function dispatchBackupPostPayloadToWordPressDatabase(payload) {

        $.ajax({
            url: settings.rootUrl + '/sync-session',
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', settings.nonce);
                xhr.setRequestHeader('Authorization', 'Bearer worknoon_wordpress_secret_handshake_key');
            },
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify(payload)
        });
    }
});
