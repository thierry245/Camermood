<?php
define("SESSION_DUREE", 1800); // 30 minutes en secondes
define("CODE_VERIF_DUREE", 300); // 5 minutes pour le code de vérification

// Configuration sécurisée des sessions
ini_set("session.cookie_lifetime", SESSION_DUREE);
ini_set("session.use_cookies", 1);
ini_set("session.use_only_cookies", 1);
ini_set("session.use_strict_mode", 1);
ini_set("session.cookie_httponly", 1);
ini_set("session.cookie_secure", 1); // À activer en production HTTPS
ini_set("session.cookie_samesite", "Strict");
ini_set("session.cache_limiter", "nocache");
ini_set("session.hash_function", "sha256");

session_name("CAMERMOOD_SESSID");