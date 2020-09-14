<?php declare(strict_types=1);

namespace LDL\Http\Router\Plugin\LDL\Log\Exception;

use LDL\Http\Router\Handler\Exception\ExceptionHandlerInterface;
use LDL\Http\Router\Router;
use Psr\Log\LoggerInterface;

class LogExceptionHandler implements ExceptionHandlerInterface
{
    private const NAMESPACE = 'LDLPlugin';
    private const NAME = 'LogExceptionHandler';
    private const DEFAULT_IS_ACTIVE = true;
    private const DEFAULT_PRIORITY = 1;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LoggerInterface $logger,
        string $namespace = null,
        string $name = null,
        int $priority = null,
        bool $isActive = null
    )
    {
        $this->logger = $logger;
        $this->namespace = $namespace ?? self::NAMESPACE;
        $this->name = $name ?? self::NAME;
        $this->isActive = $isActive ?? self::DEFAULT_IS_ACTIVE;
        $this->priority = $priority ?? self::DEFAULT_PRIORITY;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function handle(Router $router, \Exception $exception): ?int
    {
        $msg = sprintf(
            'Route: %s , Message: %s',
            $router->getRequest()->getRequestUri(),
            $exception->getMessage()
        );

        $this->logger->error($msg);
        return null;
    }
}