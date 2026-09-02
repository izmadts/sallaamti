<?php

namespace App\Exceptions;

// Thrown by UserDeletionService when the account holds an active
// operational role (teaching, hired-counselor clients) that would be left
// without a responsible staff member if deletion proceeded silently.
class UserDeletionBlockedException extends \RuntimeException
{
}
