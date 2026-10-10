namespace App\Services;

class AuditScoringService
{
    /**
     * Define the point deductions for each severity level.
     */
    protected array $penalties = [
        'error' => 20,
        'warning' => 10,
        'info' => 2,
    ];

    /**
     * Calculate the final score based on an array of generated issues.
     */
    public function calculateScore(array $issues): int
    {
        $score = 100;

        foreach ($issues as $issue) {
            $severity = $issue['severity'] ?? 'info';
            $penalty = $this->penalties[$severity] ?? 0;
            
            $score -= $penalty;
        }

        // Ensure the score never drops below 0
        return max(0, $score);
    }
}