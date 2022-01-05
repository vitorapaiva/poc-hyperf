<?php declare(strict_types=1);

namespace App\Controller;

use App\Service;
use Hyperf\HttpServer\Contract\RequestInterface;

class GetKeyListController
{
    public function __construct(
        private Service $service,
    ) {}

    public function index(RequestInterface $request): array
    {
        return $this->service->index($request);
    }
}
