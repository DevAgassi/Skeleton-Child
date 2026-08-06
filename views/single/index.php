<?php

use Timber\Timber;
use App\Models\Post\Post;

$post = Post::getOrFail();

$context = Timber::context([
    'post'               => $post,
    'share_sidebar'      => Timber::get_widgets('single_share_zone'),
    'comments_open'      => comments_open($post->ID) && !post_password_required($post->ID),
    'post_comments'      => $post->comments(),
]);

Timber::render('single/view.twig', $context);