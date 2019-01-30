<?php
/**
 * Created by PhpStorm.
 * User: avdesign
 * Date: 29/01/19
 * Time: 11:10
 */

namespace CodeShopping\Exceptions;

//extends Exception do PHP

class ChatInvitationUserException extends \Exception
{
    const ERROR_NOT_INVITATION = 1;
    const ERROR_HAS_SELLER = 2;
    const ERROR_IS_MEMBER = 3;
    const ERROR_HAS_STORED = 4;
}