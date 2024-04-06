<?php

namespace App\Services;

use App\Repositories\QandARepository;

class QandAService
{
    private $repository;
    public function __construct()
    {
        $this->repository = new QandARepository();
    }
    public function addQandA($question, $answer): void
    {
        $this->repository->addQandA($question, $answer);
    }
    public function getQandAs(): array
    {
        return $this->repository->getQandAs();
    }
    public function editQandA(int $id, string $question, string $answer): void
    {
        $this->repository->editQandA($id, $question, $answer);
    }
    public function deleteQandA(int $id): void
    {
        $this->repository->deleteQandA($id);
    }
}