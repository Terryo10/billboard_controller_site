<?php

namespace App\Livewire;

use App\Models\Advert;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\Payment;
use App\Models\Station;
use App\Models\TimeSlotTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class BookingWizard extends Component
{
    use WithFileUploads;

    public int $step = 1;
    public int $totalSteps = 6;

    // Step 1: Station selection
    public ?int $selectedStationId = null;

    // Step 2: Slot selection
    public array $selectedSlots = [];
    public int $calendarMonth;
    public int $calendarYear;

    // Step 3: Media upload
    public ?string $advertTitle = null;
    public $mediaFile = null;

    // Step 5: Payment
    public ?string $paymentMethod = null; // 'stripe' | 'ecocash'
    public ?string $stripeClientSecret = null;
    public ?string $ecocashPhone = null;

    // State
    public ?int $bookingId = null;

    public function mount(?int $station = null): void
    {
        $this->calendarMonth = (int) now()->format('m');
        $this->calendarYear = now()->year;

        if ($station) {
            $this->selectedStationId = $station;
            $this->step = 2;
        }
    }

    // --- Step navigation ---

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->step++;
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    private function validateCurrentStep(): void
    {
        match ($this->step) {
            1 => $this->validate(['selectedStationId' => 'required|exists:stations,id']),
            2 => $this->validate(['selectedSlots' => 'required|array|min:1']),
            3 => $this->validate([
                'advertTitle' => 'required|string|max:255',
                'mediaFile'   => 'required|file|max:102400|mimes:mp4,mov,jpg,jpeg,png,webp',
            ]),
            default => null,
        };
    }

    // --- Step 2: Calendar helpers ---

    public function getCalendarDaysProperty(): array
    {
        $start = now()->setYear($this->calendarYear)->setMonth($this->calendarMonth)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $days = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $days[] = $date->copy();
        }

        return $days;
    }

    public function getSlotsForDateProperty(): array
    {
        if (! $this->selectedStationId) {
            return [];
        }

        return TimeSlotTemplate::where('station_id', $this->selectedStationId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->toArray();
    }

    public function toggleSlot(int $templateId, string $airDate): void
    {
        if ($this->isSlotBooked($templateId, $airDate)) {
            return;
        }

        $existingIndex = null;
        foreach ($this->selectedSlots as $i => $slot) {
            if ($slot['template_id'] === $templateId && $slot['air_date'] === $airDate) {
                $existingIndex = $i;
                break;
            }
        }

        if ($existingIndex !== null) {
            array_splice($this->selectedSlots, $existingIndex, 1);
        } else {
            $this->selectedSlots[] = ['template_id' => $templateId, 'air_date' => $airDate];
        }
    }

    public function isSlotSelected(int $templateId, string $airDate): bool
    {
        foreach ($this->selectedSlots as $slot) {
            if ($slot['template_id'] === $templateId && $slot['air_date'] === $airDate) {
                return true;
            }
        }
        return false;
    }

    public function isSlotBooked(int $templateId, string $airDate): bool
    {
        return BookingSlot::where('slot_template_id', $templateId)
            ->where('air_date', $airDate)
            ->whereHas('booking', fn ($q) => $q->whereIn('status', ['pending', 'approved']))
            ->exists();
    }

    public function prevMonth(): void
    {
        $date = now()->setYear($this->calendarYear)->setMonth($this->calendarMonth)->subMonth();
        if ($date->gte(now()->startOfMonth())) {
            $this->calendarMonth = (int) $date->format('m');
            $this->calendarYear = $date->year;
        }
    }

    public function nextMonth(): void
    {
        $date = now()->setYear($this->calendarYear)->setMonth($this->calendarMonth)->addMonth();
        $this->calendarMonth = (int) $date->format('m');
        $this->calendarYear = $date->year;
    }

    // --- Computed pricing ---

    public function getPricingProperty(): array
    {
        $templateIds = array_column($this->selectedSlots, 'template_id');
        $templates = TimeSlotTemplate::whereIn('id', $templateIds)->get()->keyBy('id');

        $base = 0;
        foreach ($this->selectedSlots as $slot) {
            $base += (float) ($templates[$slot['template_id']]->price ?? 0);
        }

        $count = count($this->selectedSlots);
        $discountRate = match (true) {
            $count >= 10 => 0.20,
            $count >= 5  => 0.10,
            default      => 0.00,
        };

        $discount = $base * $discountRate;
        $total = $base - $discount;

        return compact('base', 'discount', 'discountRate', 'total', 'count');
    }

    // --- Step 4: Confirm & create booking (moves to payment step) ---

    public function confirmBooking(): void
    {
        if (! Auth::check()) {
            $this->redirectRoute('login');
            return;
        }

        DB::transaction(function () {
            $pricing = $this->pricing;

            $booking = Booking::create([
                'user_id'         => Auth::id(),
                'station_id'      => $this->selectedStationId,
                'status'          => 'pending',
                'total_price'     => $pricing['total'],
                'discount_amount' => $pricing['discount'],
                'slot_count'      => $pricing['count'],
            ]);

            foreach ($this->selectedSlots as $slotData) {
                $template = TimeSlotTemplate::find($slotData['template_id']);
                BookingSlot::create([
                    'booking_id'       => $booking->id,
                    'slot_template_id' => $slotData['template_id'],
                    'air_date'         => $slotData['air_date'],
                    'start_time'       => $template->start_time,
                    'end_time'         => $template->end_time,
                ]);
            }

            // Capture all file metadata before store() moves the temp file
            $file = $this->mediaFile;
            $extension = strtolower($file->getClientOriginalExtension());
            $fileType = in_array($extension, ['mp4', 'mov']) ? 'video' : 'image';
            $originalFilename = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $checksum = hash_file('sha256', $file->getRealPath());
            $path = $file->store('adverts', 'public');

            Advert::create([
                'booking_id'        => $booking->id,
                'title'             => $this->advertTitle,
                'file_path'         => $path,
                'file_type'         => $fileType,
                'original_filename' => $originalFilename,
                'file_size'         => $fileSize,
                'checksum'          => $checksum,
                'status'            => 'pending_review',
            ]);

            $this->bookingId = $booking->id;
        });

        $this->step = 5; // Payment step
    }

    // --- Step 5: Payment ---

    public function selectPaymentMethod(string $method): void
    {
        $this->paymentMethod = $method;

        if ($method === 'stripe' && ! $this->stripeClientSecret) {
            $booking = Booking::findOrFail($this->bookingId);
            Stripe::setApiKey(config('services.stripe.secret'));

            $intent = PaymentIntent::create([
                'amount'   => (int) ($booking->total_price * 100),
                'currency' => 'usd',
                'metadata' => ['booking_id' => $booking->id],
            ]);

            $this->stripeClientSecret = $intent->client_secret;
        }
    }

    // Called from Alpine.js after Stripe.js confirms payment successfully
    public function recordStripePayment(string $paymentIntentId): void
    {
        $booking = Booking::findOrFail($this->bookingId);

        if ($booking->paid_at) {
            $this->step = 6;
            return;
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $intent = PaymentIntent::retrieve($paymentIntentId);

        if ($intent->status !== 'succeeded') {
            $this->addError('payment', 'Payment was not successful. Please try again.');
            return;
        }

        $booking->update(['paid_at' => now()]);

        Payment::create([
            'booking_id'        => $booking->id,
            'provider'          => 'stripe',
            'transaction_id'    => $intent->id,
            'payment_intent_id' => $intent->id,
            'amount'            => $intent->amount / 100,
            'currency'          => strtoupper($intent->currency),
            'status'            => 'completed',
            'processed_at'      => now(),
            'metadata'          => (array) $intent->metadata,
        ]);

        $this->step = 6;
    }

    public function submitEcocashPayment(): void
    {
        $this->validate([
            'ecocashPhone' => ['required', 'regex:/^(\+?263|0)7[1-9]\d{7}$/'],
        ], [
            'ecocashPhone.required' => 'Please enter your EcoCash number.',
            'ecocashPhone.regex'    => 'Please enter a valid Zimbabwe mobile number (e.g. 0771234567).',
        ]);

        $booking = Booking::findOrFail($this->bookingId);

        if (! $booking->payment) {
            Payment::create([
                'booking_id'     => $booking->id,
                'provider'       => 'ecocash',
                'transaction_id' => 'ECO-' . strtoupper(uniqid()),
                'amount'         => $booking->total_price,
                'currency'       => 'USD',
                'status'         => 'pending',
                'metadata'       => ['phone' => $this->ecocashPhone],
            ]);
        }

        $this->step = 6;
    }

    // --- Computed properties ---

    public function getSelectedStationProperty(): ?Station
    {
        return $this->selectedStationId ? Station::with('timeSlotTemplates')->find($this->selectedStationId) : null;
    }

    public function render()
    {
        return view('livewire.booking-wizard', [
            'stations'      => Station::active()->with('timeSlotTemplates')->get(),
            'station'       => $this->selectedStation,
            'calendarDays'  => $this->calendarDays,
            'slotTemplates' => $this->slotsForDate,
            'pricing'       => $this->pricing,
            'booking'       => $this->bookingId ? Booking::with('payment')->find($this->bookingId) : null,
            'stripeKey'     => config('services.stripe.key'),
        ])->layout('layouts.public');
    }
}
