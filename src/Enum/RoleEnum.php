<?php

declare(strict_types=1);

namespace App\Enum;

enum RoleEnum: string
{
	case user = 'ROLE_USER';
	case admin = 'ROLE_ADMIN';
	case seeker = 'ROLE_SEEKER';
	case recruiter = 'ROLE_RECRUITER';
}
