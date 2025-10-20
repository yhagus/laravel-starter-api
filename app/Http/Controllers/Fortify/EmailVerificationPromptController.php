<?php

declare(strict_types=1);

namespace App\Http\Controllers\Fortify;

use Dedoc\Scramble\Attributes\Group;
use Illuminate\Routing\Controller;

#[Group('Authentication')]
final class EmailVerificationPromptController extends Controller {}
