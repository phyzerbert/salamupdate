<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;

class DeleteNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete:notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Notification::where('id', '>', 0)->delete();
    }
}
