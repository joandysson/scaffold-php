<?php

use Config\router\Router;

Router::get("/", "TestController:index");
Router::post("/test", "TestController:create");
Router::get("/home/{nome}", "TestController:create");
Router::get("/contact", "ContactController:create");
Router::post("/contact", "ContactController:test");

Router::run();

if (Router::error()) {
    var_dump(Router::error());
    // Router::redirect("/error/".Router::error());
    // Router::redirect("/error/".Router::error());
}