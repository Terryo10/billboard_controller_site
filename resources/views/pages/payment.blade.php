<x-public-layout>
    <x-slot:title>Complete Payment — Billboard Controller</x-slot:title>

    <div class="max-w-lg mx-auto px-4 py-10"
        x-data="{ method: {{ $errors->has('phone') ? "'ecocash'" : 'null' }} }"
    >
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Complete Payment</h1>

        {{-- Booking summary --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="space-y-2 text-sm text-gray-700">
                <div class="flex justify-between"><span>Station:</span><span class="font-semibold">{{ $booking->station->name }}</span></div>
                <div class="flex justify-between"><span>Product:</span><span class="font-semibold">{{ $booking->advert?->title ?? '—' }}</span></div>
                <div class="flex justify-between"><span>Media Type:</span><span class="font-semibold uppercase">{{ $booking->advert?->file_type ?? '—' }}</span></div>
                <div class="flex justify-between"><span>Slots:</span><span class="font-semibold">{{ $booking->slot_count }}</span></div>
                @if($booking->discount_amount > 0)
                    <div class="flex justify-between text-green-600"><span>Discount:</span><span>-${{ number_format($booking->discount_amount, 2) }}</span></div>
                @endif
                <div class="flex justify-between font-bold text-lg border-t pt-2">
                    <span>Total:</span>
                    <span class="text-blue-600">${{ number_format($booking->total_price, 2) }}</span>
                </div>
            </div>

            @if($booking->advert)
                <div class="mt-4 border-t pt-4">
                    @if($booking->advert->file_type === 'image' && $booking->advert->file_path)
                        <img src="{{ Storage::disk('public')->url($booking->advert->file_path) }}"
                            alt="{{ $booking->advert->title }}"
                            class="w-full rounded-xl border border-gray-100 object-cover max-h-64">
                    @elseif($booking->advert->file_type === 'video')
                        <a href="{{ $booking->advert->file_url }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex items-center text-sm font-medium text-blue-600 hover:underline">
                            Open uploaded video
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Payment method selector --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">

            {{-- Method picker (shown when no method chosen yet) --}}
            <div x-show="!method">
                <p class="text-sm font-semibold text-gray-700 mb-4">Choose a payment method</p>
                <div class="grid grid-cols-2 gap-4">

                    <button @click="method = 'stripe'"
                        class="flex flex-col items-center gap-3 p-5 border-2 border-gray-200 rounded-2xl hover:border-indigo-500 hover:bg-indigo-50 transition group">
                        <svg class="w-9 h-9 text-indigo-400 group-hover:text-indigo-600" viewBox="0 0 38 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="38" height="24" rx="4" fill="#EEF2FF"/>
                            <path d="M14 15.5c0 .83-.67 1.5-1.5 1.5h-5C6.67 17 6 16.33 6 15.5v-7C6 7.67 6.67 7 7.5 7h5c.83 0 1.5.67 1.5 1.5v7zM32 10h-9v4h9v-4z" fill="#6366F1"/>
                            <circle cx="27" cy="14" r="3" fill="#818CF8"/>
                            <circle cx="23" cy="14" r="3" fill="#4F46E5"/>
                        </svg>
                        <div class="text-center">
                            <p class="font-bold text-gray-900 text-sm">Pay with Card</p>
                            <p class="text-xs text-gray-400 mt-0.5">Visa, Mastercard, Amex</p>
                        </div>
                    </button>

                    <button @click="method = 'ecocash'"
                        class="flex flex-col items-center gap-3 p-5 border-2 border-gray-200 rounded-2xl hover:border-green-500 hover:bg-green-50 transition group">
                        <svg class="w-9 h-9 text-green-400 group-hover:text-green-600" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="40" height="40" rx="8" fill="#DCFCE7"/>
                            <path d="M20 10c-5.52 0-10 4.48-10 10s4.48 10 10 10 10-4.48 10-10S25.52 10 20 10zm1 17.93V26h-2v1.93C14.06 27.48 11 24.08 11 20c0-4.08 3.06-7.48 7-7.93V14h2v-1.93C24.94 12.52 28 15.92 28 20c0 4.08-3.06 7.48-7 7.93zM20 16c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z" fill="#16A34A"/>
                        </svg>
                        <div class="text-center">
                            <p class="font-bold text-gray-900 text-sm">Pay with EcoCash</p>
                            <p class="text-xs text-gray-400 mt-0.5">Zimbabwe mobile money</p>
                        </div>
                    </button>

                </div>
            </div>

            {{-- Stripe card form --}}
            <div x-show="method === 'stripe'" style="display:none">
                <div class="flex items-center gap-2 mb-5">
                    <button @click="method = null" class="text-gray-400 hover:text-gray-600 text-sm transition">← Back</button>
                    <span class="text-gray-300">|</span>
                    <span class="text-sm font-semibold text-gray-700">Pay by Card</span>
                </div>

                <form id="payment-form">
                    <div id="payment-element" class="mb-5"></div>
                    <div id="payment-message" class="text-red-500 text-sm bg-red-50 border border-red-100 rounded-lg px-4 py-2 mb-4 hidden"></div>
                    <button id="stripe-submit" type="submit"
                        class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
                        <span id="button-text">Pay ${{ number_format($booking->total_price, 2) }}</span>
                        <span id="spinner" class="hidden items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </form>
            </div>

            {{-- EcoCash form --}}
            <div x-show="method === 'ecocash'" style="display:none">
                <div class="flex items-center gap-2 mb-5">
                    <button @click="method = null" class="text-gray-400 hover:text-gray-600 text-sm transition">← Back</button>
                    <span class="text-gray-300">|</span>
                    <span class="text-sm font-semibold text-gray-700">Pay with EcoCash</span>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5 text-sm text-green-800 space-y-1">
                    <p class="font-semibold">How EcoCash payment works:</p>
                    <ol class="list-decimal list-inside space-y-1 mt-2">
                        <li>Enter your EcoCash number below and submit.</li>
                        <li>You'll receive a USSD prompt on your phone — approve the payment.</li>
                        <li>Our team will confirm receipt and activate your booking.</li>
                    </ol>
                </div>

                <form action="{{ route('booking.ecocash', $booking) }}" method="POST">
                    @csrf
                    @error('phone')
                        <div class="text-red-500 text-sm bg-red-50 border border-red-100 rounded-lg px-4 py-2 mb-4">{{ $message }}</div>
                    @enderror

                    <label class="block text-sm font-medium text-gray-700 mb-1">Your EcoCash Number *</label>
                    <div class="flex mb-4">
                        <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-l-lg">+263</span>
                        <input name="phone" type="tel" value="{{ old('phone') }}"
                            placeholder="771234567"
                            class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    </div>
                    <p class="text-xs text-gray-400 mb-5">e.g. 0771234567 or +263771234567</p>

                    <button type="submit"
                        class="w-full bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 transition">
                        Submit EcoCash Payment — ${{ number_format($booking->total_price, 2) }}
                    </button>
                </form>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Mount Stripe eagerly so the iframe is ready before the user picks "Card".
        // x-show uses display:none — the element exists in the DOM, Stripe can mount to it.
        const stripe = Stripe(@js($stripeKey));
        const elements = stripe.elements({ clientSecret: @js($clientSecret) });
        elements.create('payment').mount('#payment-element');

        document.getElementById('payment-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn    = document.getElementById('stripe-submit');
            const btnTxt = document.getElementById('button-text');
            const spin   = document.getElementById('spinner');
            const msg    = document.getElementById('payment-message');

            btn.disabled = true;
            btnTxt.classList.add('hidden');
            spin.classList.remove('hidden');
            spin.classList.add('flex');
            msg.classList.add('hidden');

            // redirect:'if_required' returns the paymentIntent inline for non-3DS cards;
            // for 3DS cards Stripe redirects to return_url automatically.
            const { error, paymentIntent } = await stripe.confirmPayment({
                elements,
                confirmParams: {
                    return_url: @js(route('booking.payment.success', $booking)),
                },
                redirect: 'if_required',
            });

            if (error) {
                msg.textContent = error.message;
                msg.classList.remove('hidden');
                btn.disabled = false;
                btnTxt.classList.remove('hidden');
                spin.classList.add('hidden');
                spin.classList.remove('flex');
                return;
            }

            // Inline success (no redirect needed) — forward to success route so the
            // server can record the payment before landing on My Adverts.
            if (paymentIntent?.status === 'succeeded') {
                window.location.href =
                    @js(route('booking.payment.success', $booking)) +
                    '?payment_intent=' + paymentIntent.id;
            }
        });
    </script>
    @endpush
</x-public-layout>
