<?php
namespace App\Repositories;

use PDO;
use App\Models\QuestionAndAnswer;

class QandARepository extends Repository
{

    public function addQandA($question, $answer): void
    {
        $stmt = $this->connection->prepare("INSERT INTO QuestionAndAnswer (question, answer) VALUES (:question, :answer)");
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':answer', $answer);
        $stmt->execute();
    }

    public function getQandAs(): array
    { // ar putea merge
        $stmt = $this->connection->prepare("SELECT * FROM QuestionAndAnswer");
        $stmt->execute();
        $QandAs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($element) {
            return new QuestionAndAnswer(
                $element['infoId'],
                $element['question'],
                $element['answer']
            );
        }, $QandAs);
    }

    public function editQandA(int $id, string $question, string $answer): void
    {
        $stmt = $this->connection->prepare("UPDATE QuestionAndAnswer SET question = :question, answer = :answer WHERE infoId = :id");
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':answer', $answer);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function deleteQandA(int $id): void
    {
        $stmt = $this->connection->prepare("DELETE FROM QuestionAndAnswer WHERE infoId = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }



}