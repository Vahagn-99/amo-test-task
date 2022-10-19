<?php

namespace App\Console\Commands;

use App\Services\AmoApiService;
use Illuminate\Console\Command;

class AmoApiTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amo:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(AmoApiService $amo)
    {
        return Command::SUCCESS;
    }
}
