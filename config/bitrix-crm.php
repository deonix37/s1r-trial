<?php

return [

    'dashboard_url' => env('BITRIX_CRM_DASHBOARD_URL'),
    'lead_url' => env('BITRIX_CRM_DASHBOARD_URL') . 'crm/deal/details/%s/',

    'webhook_url' => env('BITRIX_CRM_WEBHOOK_URL'),
    'webhook_lead_add_url' => env('BITRIX_CRM_WEBHOOK_URL') . 'crm.lead.add.json',

];
