<?php
/**
 * 📁 File: templates/chat-widget-template.php
 * 🚀 UX FEATURE: Dribbble-inspired Floating Layout Chat Widget
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>

<!-- FLOATING TRIGGER ACTION ELEMENT ICON ACCENTED BADGE -->
<div id="worknoon-chat-trigger" class="fixed bottom-6 right-6 h-14 w-14 bg-black text-white rounded-2xl flex items-center justify-center cursor-pointer shadow-lg hover:scale-105 transition-all z-50 select-none border border-zinc-800">
    <svg xmlns="http://w3.org" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
    </svg>
    <!-- Live Unread Badge Bubble -->
    <span id="worknoon-widget-badge" class="hidden absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white font-black text-[9px] rounded-full flex items-center justify-center animate-bounce">0</span>
</div>

<!-- CONTAINER CANVAS SHEETS WRAPPER LAYOUT PANEL -->
<div id="worknoon-chat-panel" class="hidden fixed bottom-24 right-6 w-80 h-96 bg-white border border-gray-100 rounded-3xl shadow-2xl flex flex-col overflow-hidden z-50 select-none transform transition-transform duration-300">
    
    <!-- Header panel bar segment -->
    <div class="p-4 bg-black text-white flex items-center justify-between shrink-0">
        <div>
            <h3 class="text-xs font-black tracking-tight">Worknoon Terminal Desk</h3>
            
            <!-- 🚀 FIXED SAFELY: Wrapped in a function_exists check to clear line 34 fatal crashes completely -->
            <?php if ( function_exists( 'is_product' ) && is_product() ) : ?>
                <p class="text-[9px] text-amber-300 font-bold mt-0.5">🛒 Product Sync: #<?php echo get_the_ID(); ?></p>
            <?php endif; ?>
        </div>
        <button id="worknoon-panel-close" class="text-gray-400 hover:text-white transition-colors text-xs font-bold focus:outline-none">✕</button>
    </div>

    <!-- Live message scroll boundaries wrapper layer -->
    <div id="worknoon-widget-feed" class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/50 text-[11px] font-medium flex flex-col">
        <div class="my-auto text-center text-gray-400 text-[10px] leading-relaxed px-4">
            Welcome to the multi-role transmission array layer. Communication channel online.
        </div>
    </div>

    <!-- Active live typing placeholder slat row -->
    <div id="worknoon-widget-typing" class="hidden px-4 py-1.5 text-[9px] font-black italic tracking-wide text-gray-400 bg-white border-t border-gray-100/60 animate-pulse">
        Support agent is typing...
    </div>

    <!-- Submission input action form block tray -->
    <form id="worknoon-widget-form" class="p-2 border-t border-gray-100 bg-white flex items-center gap-2 shrink-0">
        <input 
            type="text" 
            id="worknoon-widget-input"
            placeholder="Type your message..." 
            className="flex-1 bg-gray-50 text-[11px] font-semibold text-gray-900 px-3 py-2 rounded-xl focus:outline-none placeholder-gray-400 border border-gray-100"
            autocomplete="off"
        />
        <button type="submit" class="bg-black hover:bg-zinc-800 text-white font-black text-[10px] px-3 py-2 rounded-xl transition shadow-xs">Send</button>
    </form>
</div>
