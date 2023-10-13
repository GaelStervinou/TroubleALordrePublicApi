<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use App\Interface\BlameableEntityInterface;
use App\Interface\TimestampableEntityInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateAndUpdateStateProcessor implements ProcessorInterface
{

    public function __construct(private ProcessorInterface $persistProcessor, private Security $security)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof TimestampableEntityInterface) {
            if ($operation instanceof Post  && $data->getCreatedAt() === null) {
                $data->setCreatedAt(new \DateTimeImmutable());
            } elseif( ($operation instanceof Patch  || $operation instanceof Put)) {
                $data->setUpdatedAt(new \DateTimeImmutable());
            }
        }

        if ($data instanceof BlameableEntityInterface) {
            if ($operation instanceof Post  && $data->getCreatedBy() === null) {
                $data->setCreatedBy($this->security->getUser());
            } elseif( ($operation instanceof Patch  || $operation instanceof Put)) {
                $data->setUpdatedBy($this->security->getUser());
            }
        }

        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }

}
