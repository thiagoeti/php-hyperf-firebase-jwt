<?php

Router::addRoute(['GET', 'POST'], '/generate', 'App\Controller\ControllerJWT@generate');
Router::addRoute(['GET', 'POST'], '/decode', 'App\Controller\ControllerJWT@decode');
