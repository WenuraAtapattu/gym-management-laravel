<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Review System Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the review system.
    | You can customize various aspects of how reviews work in your application.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Review Moderation
    |--------------------------------------------------------------------------
    |
    | These options control whether reviews require admin approval before being
    | visible to other users.
    |
    */
    'moderation' => [
        'enabled' => env('REVIEWS_MODERATION_ENABLED', true),
        'notify_admin' => env('REVIEWS_NOTIFY_ADMIN', true),
        'admin_email' => env('REVIEWS_ADMIN_EMAIL', env('MAIL_FROM_ADDRESS')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Review Settings
    |--------------------------------------------------------------------------
    |
    | These options control various settings related to reviews.
    |
    */
    'settings' => [
        'min_rating' => 1,
        'max_rating' => 5,
        'allow_empty_comments' => true,
        'allow_guest_reviews' => false,
        'allow_editing' => true,
        'allow_deleting' => true,
        'allow_reporting' => true,
        'allow_images' => true,
        'max_images' => 5,
        'max_image_size' => 5120, // in KB
        'allowed_image_types' => ['jpeg', 'png', 'jpg', 'gif'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Review Display
    |--------------------------------------------------------------------------
    |
    | These options control how reviews are displayed.
    |
    */
    'display' => [
        'default_sort' => 'newest', // newest, highest, lowest, helpful
        'per_page' => 10,
        'show_avatars' => true,
        'show_verification_badge' => true,
        'show_helpful_button' => true,
        'show_report_button' => true,
        'show_share_buttons' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Review Notifications
    |--------------------------------------------------------------------------
    |
    | These options control the notification settings for reviews.
    |
    */
    'notifications' => [
        'new_review' => [
            'enabled' => true,
            'email' => true,
            'database' => true,
        ],
        'review_approved' => [
            'enabled' => true,
            'email' => true,
            'database' => true,
        ],
        'review_rejected' => [
            'enabled' => true,
            'email' => true,
            'database' => true,
        ],
        'review_reported' => [
            'enabled' => true,
            'email' => true,
            'database' => true,
        ],
        'review_replied' => [
            'enabled' => true,
            'email' => true,
            'database' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Review Requirements
    |--------------------------------------------------------------------------
    |
    | These options control the requirements for submitting a review.
    |
    */
    'requirements' => [
        'require_purchase' => true,
        'min_words' => 5,
        'max_words' => 1000,
        'min_title_length' => 5,
        'max_title_length' => 255,
    ],

    /*
    |--------------------------------------------------------------------------
    | Review Spam Protection
    |--------------------------------------------------------------------------
    |
    | These options help prevent spam reviews.
    |
    */
    'spam_protection' => [
        'enabled' => true,
        'honeypot_field' => 'website',
        'min_seconds_between_reviews' => 60,
        'max_reviews_per_day' => 5,
        'block_suspicious_ips' => true,
    ],
];
