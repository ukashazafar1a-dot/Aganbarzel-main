<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Production checklist (tags on the order in Shopify Admin)
    |--------------------------------------------------------------------------
    |
    | Each step is complete when its `tag` appears on the order (case-insensitive).
    | `eta_days` is summed for steps not yet done (unless payment is blocked).
    |
    */
    'steps' => [
        [
            'key' => 'suppliers_ordered',
            'label_he' => 'הוזמנו חלקים אצל הספקים',
            'tag' => 'stage-suppliers-ordered',
            'eta_days' => 3,
        ],
        [
            'key' => 'aluminum_received',
            'label_he' => 'התקבל אלומיניום מהלייזר',
            'tag' => 'stage-aluminum-received',
            'eta_days' => 2,
        ],
        [
            'key' => 'mirror_received',
            'label_he' => 'התקבלה הזכוכית',
            'tag' => 'stage-mirror-received',
            'eta_days' => 2,
        ],
        [
            'key' => 'frame_sent_paint',
            'label_he' => 'המסגרת נשלחה לצביעה',
            'tag' => 'stage-frame-paint-sent',
            'eta_days' => 3,
        ],
        [
            'key' => 'frame_back_paint',
            'label_he' => 'המסגרת חזרה מהצביעה',
            'tag' => 'stage-frame-paint-back',
            'eta_days' => 2,
        ],
        [
            'key' => 'product_ready',
            'label_he' => 'המוצר מוכן',
            'tag' => 'stage-product-ready',
            'eta_days' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment gate
    |--------------------------------------------------------------------------
    |
    | When financial_status matches, customer sees payment_message_he and ETA
    | for remaining steps is not counted toward the "days left" summary.
    |
    */
    'payment_blocking_financial_statuses' => [
        'pending',
        'partially_paid',
        'authorized',
    ],

    'payment_message_he' => 'קיימת יתרת תשלום. שחרור המוצר או המשלוח יתאפשרו לאחר סגירת החשבון במלואו.',

    /*
    |--------------------------------------------------------------------------
    | Pickup vs delivery (tags on the order)
    |--------------------------------------------------------------------------
    |
    | Legacy staff tags (fulfillment-pickup / fulfillment-delivery) still work
    | for the ready banner. Flow choice tags below drive checklist row filtering.
    |
    */
    'pickup_tag' => 'fulfillment-pickup',
    'delivery_tag' => 'fulfillment-delivery',

    /*
    |--------------------------------------------------------------------------
    | Flow fulfillment choice → filter exclusive checklist rows (Q2–Q4)
    |--------------------------------------------------------------------------
    |
    | Shopify Flow adds one of these tags after checkout. When present on the
    | order, only the matching production_checklist row (fulfillment_choice or
    | show_when_order_tag) is shown; other conditional rows are hidden.
    |
    | If no choice tag is on the order yet, behavior is unchanged (all rows).
    | Set filter_fulfillment_options_by_flow_tag to false to disable filtering.
    |
    */
    'filter_fulfillment_options_by_flow_tag' => true,

    'fulfillment_choice_tags' => [
        'pickup' => 'אריה שנקר 26',
        'delivery' => 'משלוח עד הבית',
        'delivery_installation' => 'משלוח + התקנה',
    ],

    /*
    |--------------------------------------------------------------------------
    | When all checklist steps are done, payment is OK, and branch is known
    |--------------------------------------------------------------------------
    */
    'fulfillment_message_pickup_he' => 'ההזמנה מוכנה לאיסוף עצמי. תקבלו מהחנות הודעה עם הוראות לאיסוף.',

    'fulfillment_message_delivery_he' => 'ההזמנה מוכנה למשלוח. תקבלו עדכון כשהמשלוח יישלח.',

    'fulfillment_message_delivery_installation_he' => 'ההזמנה מוכנה למשלוח והתקנה. תקבלו עדכון כשהמשלוח ייצא.',

    /*
    |--------------------------------------------------------------------------
    | Product metafield checklist (per product in Admin)
    |--------------------------------------------------------------------------
    |
    | Each product can store JSON in a metafield: array of { key, label or
    | label_he, tag, optional eta_days, position, note (legacy), estimate_note,
    | auto_from_order, fulfillment_choice, show_when_order_tag }.
    |
    | fulfillment_choice — pickup | delivery | delivery_installation (maps to
    | Flow tags in fulfillment_choice_tags). Only one matching row is shown per order.
    | show_when_order_tag — alternative: exact order tag required to show the row.
    |
    | The order "done" state for each step is when `tag` exists on the order
    | (case-insensitive). Set namespace/key to match Settings → Metafields.
    |
    */
    'use_product_metafield_checklist' => true,

    'fallback_steps_when_no_metafield' => true,

    'product_checklist_metafield_namespace' => 'custom',

    'product_checklist_metafield_key' => 'production_checklist',

    /*
    |--------------------------------------------------------------------------
    | First step: auto-complete (order created) without requiring its tag
    |--------------------------------------------------------------------------
    |
    | When true, the first checklist row (lowest position, or flagged below)
    | is always "done" with completed_at from the Shopify order created_at.
    |
    */
    'auto_complete_first_step' => true,

    /*
    | position — row with smallest `position` in merged template.
    | flag — rows with auto_from_order: true in product checklist JSON (lowest position wins).
    |
    */
    'auto_complete_first_step_mode' => 'position',

    /*
    |--------------------------------------------------------------------------
    | Order metafield — manual delay / production update (per order)
    |--------------------------------------------------------------------------
    |
    | Namespace/key must match Settings → Custom data → Orders (e.g. Production update).
    |
    */
    'order_production_update_namespace' => 'custom',

    'order_production_update_key' => 'production_update',

];
