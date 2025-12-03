<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Poll;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Poll>
 */
class PollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ulid' => Str::ulid(),
            'team_id' => Team::factory(),
            'name' => fake()->sentence(3),
            'question' => fake()->sentence().'?',
        ];
    }

    /**
     * Indicate that the poll should have answers.
     */
    public function withAnswers(int $count = 3, ?array $answers = null): static
    {
        return $this->afterCreating(function (Poll $poll) use ($count, $answers) {
            if ($answers === null) {
                $answers = [];
                for ($i = 0; $i < $count; $i++) {
                    $answers[] = fake()->sentence(2);
                }
            }

            foreach ($answers as $index => $answerText) {
                Answer::factory()->create([
                    'poll_id' => $poll->id,
                    'text' => $answerText,
                    'sort_order' => $index,
                ]);
            }
        });
    }
}
