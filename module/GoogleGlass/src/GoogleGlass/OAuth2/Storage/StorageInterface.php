<?php
namespace GoogleGlass\OAuth2\Storage;

use GoogleGlass\Entity\OAuth2\Token;

interface StorageInterface
{
    public function store(Token $token);
    public function retrieve();
}

?>