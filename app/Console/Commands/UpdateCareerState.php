<?php

namespace App\Console\Commands;

use App\Models\Career;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateCareerState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update career status when deadline';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $career = Career::where('deadline', '<', $now)
            ->update(['status' => "inactive"]);

        $this->info("Career statuses updated successfully.");

        return 0;
    }
}
