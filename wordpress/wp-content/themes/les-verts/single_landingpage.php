<?php
/*
 * Template Name: Landing Page
 * Template Post Type: post, page
 */

use SUPT\ACFPost;
use Timber\Timber;

$context = Timber::context();
$post = Timber::get_post();
$context['post'] = $post;
$context['distraction_free'] = true;

if (post_password_required($post->ID)) {
    Timber::render('single-password.twig', $context);
} else {
    Timber::render(array('single-' . $post->ID . '.twig', 'single-' . $post->post_type . '.twig', 'single.twig'), $context);
}
