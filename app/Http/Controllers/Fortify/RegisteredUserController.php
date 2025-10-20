<?php

declare(strict_types=1);

namespace App\Http\Controllers\Fortify;

use Dedoc\Scramble\Attributes\Group;

#[Group('Authentication')]
final class RegisteredUserController extends \Laravel\Fortify\Http\Controllers\RegisteredUserController {}
