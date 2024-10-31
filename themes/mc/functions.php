<?php

function create_custom_post_type() {
    register_post_type('custom_info',
        array(
            'labels'      => array(
                'name'          => __('Custom Event'),
                'singular_name' => __('Custom Event'),
                'add_new_item'  => __('Add New Event'),
                'edit_item'     => __('Edit Info'),
                'all_items'     => __('All Info'),
            ),
            'public'      => true,
            'has_archive' => true,
            'menu_icon'   => 'dashicons-info', // Change the icon here
            'supports'    => array('title', 'editor', 'custom-fields'), // Include fields you want
            'taxonomies'  => array('category'), // This allows standard categories
            'rewrite'     => array('slug' => 'custom-info'),
        )
    );
}
add_action('init', 'create_custom_post_type');


function create_custom_taxonomy() {
    register_taxonomy(
        'Place',
        'custom_info',
        array(
            'label' => __('Place'),
            'rewrite' => array('slug' => 'custom-category'),
            'hierarchical' => true, // Set to true to have parent/child relationship
        )
    );

    // Add predefined categories
    $categories = array('Valtakunnallinen perusohjelma', 'Muut valtakunnalliset', 'Paikkalliset UUsimaa',
     'Paikkallliset Päijät-Häme', 'Paikalliset Pirkanmaa', 'Paikalliset Pohjanmaa', 'Paikalliset Lounais-Suomi');
    
    foreach ($categories as $category) {
        if (!term_exists($category, 'Place')) {
            wp_insert_term($category, 'Place');
        }
    }
}
add_action('init', 'create_custom_taxonomy');