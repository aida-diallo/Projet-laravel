<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Evenement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InscriptionConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $evenement;
    public $qrCodePath;

    public function __construct(User $user, Evenement $evenement, string $qrCodePath)
    {
        $this->user = $user;
        $this->evenement = $evenement;
        $this->qrCodePath = $qrCodePath;
    }

    public function build()
    {
        return $this->subject('Confirmation d\'inscription - ' . $this->evenement->titre)
            ->view('emails.confirmation');
    }
}
