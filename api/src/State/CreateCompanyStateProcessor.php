<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Company;
use Symfony\Bundle\SecurityBundle\Security;

class CreateCompanyStateProcessor implements ProcessorInterface
{
    private ?Security $security = null;
    private ?CreateAndUpdateStateProcessor $createAndUpdateStateProcessor = null;
    public function __construct(
       Security $security,
        CreateAndUpdateStateProcessor $createAndUpdateStateProcessor
    ){
        $this->security = $security;
        $this->createAndUpdateStateProcessor = $createAndUpdateStateProcessor;
    }
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation instanceof Post) {
            /**@var $data Company*/
            $data->setOwner($this->security->getUser());
            $this->createAndUpdateStateProcessor->process($data, $operation, $uriVariables, $context);
        }
    }
}
