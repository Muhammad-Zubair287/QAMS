<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignAutoZeroForMissedAssignments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qams:assign-auto-zero';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign zero marks for missed assignment submissions after deadline.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();

        try {
            $rows = DB::table('assignments as a')
                ->join('student_subjects as ss', 'ss.subject_id', '=', 'a.subject_id')
                ->leftJoin('assignment_submissions as asm', function ($join): void {
                    $join->on('asm.assignment_id', '=', 'a.id')
                        ->on('asm.student_id', '=', 'ss.student_id');
                })
                ->where('a.deadline_at', '<', $now)
                ->whereNull('asm.id')
                ->select([
                    'a.id as assignment_id',
                    'a.teacher_id as graded_by',
                    'ss.student_id as student_id',
                ])
                ->get()
                ->map(function ($row) use ($now): array {
                    return [
                        'assignment_id' => $row->assignment_id,
                        'student_id' => $row->student_id,
                        'score' => 0,
                        'feedback' => 'Auto-assigned zero due to missed deadline.',
                        'graded_by' => $row->graded_by,
                        'graded_at' => $now,
                        'published_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                })
                ->all();

            if (empty($rows)) {
                $this->info('No missed assignment submissions found.');
                return self::SUCCESS;
            }

            DB::table('assignment_submissions')->insertOrIgnore($rows);
            $count = count($rows);

            $this->info('Auto-zero records processed: ' . $count);
            Log::info('Auto-zero assignment scheduler executed', ['processed' => $count]);

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            Log::error('Auto-zero assignment scheduler failed', [
                'error' => $exception->getMessage(),
            ]);

            $this->error('Failed: ' . $exception->getMessage());
            return self::FAILURE;
        }
    }
}
