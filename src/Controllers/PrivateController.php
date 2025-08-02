<?php

namespace Controllers;

abstract class PrivateController extends PublicController
{
    private function _isAuthorized()
    {
        $isAuthorized = \Utilities\Security::isAuthorized(
            \Utilities\Security::getUserId(),
            $this->name,
            'CTR'
        );
        if (!$isAuthorized) {
            throw new PrivateNoAuthException();
        }
    }

    private function _isAuthenticated()
    {
        if (!\Utilities\Security::isLogged()){
            throw new PrivateNoLoggedException();
        }
    }

    protected function isFeatureAutorized($feature) :bool
    {
        return \Utilities\Security::isAuthorized(
            \Utilities\Security::getUserId(),
            $feature
        );
    }

    public function __construct()
    {
        parent::__construct();
        
        // 1. Verifica si el usuario tiene sesión iniciada
        $this->_isAuthenticated();
        // 2. Verifica si el usuario tiene permiso para este controlador
        $this->_isAuthorized();
        \Utilities\Nav::setNavContext();
    }
}