<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class PaymentController extends Controller
{
    public function show(Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->paid_at !== null, 422, 'This booking is already paid.');
        $booking->loadMissing(['station', 'advert']);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount'   => (int) ($booking->total_price * 100),
            'currency' => 'usd',
            'metadata' => ['booking_id' => $booking->id],
        ]);

        return view('pages.payment', [
            'booking'       => $booking,
            'clientSecret'  => $intent->client_secret,
            'stripeKey'     => config('services.stripe.key'),
        ]);
    }

    public function success(Request $request, Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);

        $paymentIntentId = $request->query('payment_intent');

        if ($paymentIntentId && ! $booking->paid_at) {
            Stripe::setApiKey(config('services.stripe.secret'));
            $intent = PaymentIntent::retrieve($paymentIntentId);

            if ($intent->status === 'succeeded') {
                $booking->update(['paid_at' => now()]);

                Payment::firstOrCreate(
                    ['transaction_id' => $intent->id],
                    [
                        'booking_id'        => $booking->id,
                        'provider'          => 'stripe',
                        'payment_intent_id' => $intent->id,
                        'amount'            => $intent->amount / 100,
                        'currency'          => strtoupper($intent->currency),
                        'status'            => 'completed',
                        'processed_at'      => now(),
                        'metadata'          => (array) $intent->metadata,
                    ]
                );
            }
        }

        return redirect()->route('my-adverts')
            ->with('status', 'Payment successful! Your booking is under review.');
    }

    public function ecocash(Request $request, Booking $booking)
    {
        abort_if($booking->user_id !== Auth::id(), 403);
        abort_if($booking->paid_at !== null, 422, 'This booking is already paid.');

        $request->validate([
            'phone' => ['required', 'regex:/^(\+?263|0)7[1-9]\d{7}$/'],
        ], [
            'phone.required' => 'Please enter your EcoCash number.',
            'phone.regex'    => 'Please enter a valid Zimbabwe mobile number (e.g. 0771234567).',
        ]);

        if (! $booking->payment) {
            Payment::create([
                'booking_id'     => $booking->id,
                'provider'       => 'ecocash',
                'transaction_id' => 'ECO-' . strtoupper(uniqid()),
                'amount'         => $booking->total_price,
                'currency'       => 'USD',
                'status'         => 'pending',
                'metadata'       => ['phone' => $request->phone],
            ]);
        }

        return redirect()->route('my-adverts')
            ->with('status', 'EcoCash payment submitted. We will confirm receipt and activate your booking shortly.');
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (SignatureVerificationException) {
            return response('Invalid signature', 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            $bookingId = $intent->metadata->booking_id ?? null;

            if ($bookingId) {
                $booking = Booking::query()->find((int) $bookingId);
                if ($booking instanceof Booking && ! $booking->paid_at) {
                    $booking->paid_at = now();
                    $booking->save();

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
                }
            }
        }

        return response('OK', 200);
    }
}
