<?php

return [

    'lead_bot_token' => env('TELEGRAM_LEAD_BOT_TOKEN'),
    'lead_channel_id' => env('TELEGRAM_LEAD_CHANNEL_ID'),

    'send_message_url' => 'https://api.telegram.org/bot'
        . env('TELEGRAM_LEAD_BOT_TOKEN')
        . '/sendMessage',

];
