<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;

class DispatchForgotPasswordEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $emailAddress, public $token) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::mailer('smtp')->to($this->emailAddress)->send(new ForgotPassword($this->token));
    }
}
