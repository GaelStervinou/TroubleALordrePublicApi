<?php

namespace App\State;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\State\ProcessorInterface;
use App\Interface\SoftDeleteInterface;

final readonly class SoftDeleteStateProcessor implements ProcessorInterface
{
    public function __construct(private ProcessorInterface $persistProcessor, private CreateAndUpdateStateProcessor $createAndUpdateStateProcessor)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof SoftDeleteInterface && $operation instanceof Patch) {
            if ($this->isDeleting($context, $data) === true) {
                $data->setDeletedAt(new \DateTimeImmutable());
                $data->delete();
            } else if ($context[ 'previous_data' ]->isDeleted() === true) {
                throw new \Exception('You can\'t restore a deleted resource');
            }
            $this->createAndUpdateStateProcessor->process($data, $operation, $uriVariables, $context);

        } else {
            $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }
    }

    private function isDeleting($context, mixed $data): bool
    {
        return ($context[ 'previous_data' ]?->isDeleted() === false
            && $data->isDeleted() === true);
    }
}
