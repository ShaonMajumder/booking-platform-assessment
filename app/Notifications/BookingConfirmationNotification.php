<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ?Booking $booking = null;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;

        Log::info('BookingConfirmationNotification created', [
            'booking_id' => $booking->id,
            'service_id' => $booking->service_id,
            'name' => $booking->name,
            'phone' => $booking->phone,
            'schedule_date_time' => $booking->schedule_date_time,
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (!$this->booking) {
            Log::error('Booking is not set in BookingConfirmationNotification.');
            return (new MailMessage)
                ->subject('Booking Confirmation Error')
                ->line('An error occurred while generating your booking confirmation.');
        }

        $formattedDate = $this->booking->schedule_date_time ? \Carbon\Carbon::parse($this->booking->schedule_date_time)->format('l, F j, Y \a\t g:i A') : 'N/A';
        $bookingService = $this->booking ?
            ($this->booking->service ? $this->booking->service->name : 'N/A') : 'N/A';
        $bookingName = $this->booking ? $this->booking->name : 'N/A';

        return (new MailMessage)
                    ->subject('Your Booking is Confirmed - Sheba.xyz')
                    ->line("Hello {$bookingName},")
                    ->line("Your booking for service {$bookingService} on {$formattedDate} has been confirmed.")
                    ->line('Thank you for using our platform!')
                    ->salutation('Best regards, Sheba Platform Ltd.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
