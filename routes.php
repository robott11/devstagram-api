<?php
global $routes;

$routes = [
    "/users/login"         => "/users/login",
    "/users/new"           => "/users/new_record",
    "/users/{id}"          => "/users/view/:id",
    "/users/{id}/feed"     => "/users/feed/:id",
    "/users/{id}/photos"   => "/users/photos/:id",
    "/users/{id}/follow"   => "/users/follow/:id",
    "/photos/random"       => "/photos/random",
    "/photos/new"          => "/photos/new_record",
    "/photos/{id}"         => "/photos/view/:id",
    "/photos/{id}/comment" => "/photos/comment/:id",
    "/photos/{id}/like"    => "/photos/like/:id"
];