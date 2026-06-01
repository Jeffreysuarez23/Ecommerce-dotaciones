<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Orden;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $orden;

    public function __construct(Orden $orden)
    {
        // Eager-load all needed relationships
        $this->orden = $orden->load([
            'usuario',
            'direccion',
            'items.variante.producto.imagenes',
            'pago'
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Confirmación de Pedido #' . $this->orden->numero,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
        );
    }
}
