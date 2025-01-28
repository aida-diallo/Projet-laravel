
<?php



use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSurveyEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $evenement;

    public function __construct($evenement)
    {
        $this->evenement = $evenement;
    }

    public function handle()
    {
        Evenement::envoyerEmailsSondage($this->evenement);
    }
}