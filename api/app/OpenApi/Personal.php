<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'Online Store Personal API', version: 'v2')]
#[OA\Server(url: '/', description: 'Default')]
#[OA\Tag(name: 'Личный кабинет', description: 'Регистрация и профиль')]
#[OA\Tag(name: 'Session', description: 'Browser session and CSRF protection')]
final class Personal
{
}
