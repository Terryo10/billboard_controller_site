<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Progress Bar --}}
    <div class="mb-10">
        <div class="flex items-center justify-between mb-2">
            @php $steps = ['Select Station', 'Choose Slots', 'Upload Media', 'Review', 'Payment', 'Confirmation']; @endphp
            @foreach($steps as $i => $label)
                <div class="flex flex-col items-center flex-1">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold
                        {{ $step > ($i+1) ? 'bg-green-500 text-white' : ($step == ($i+1) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500') }}">
                        {{ $step > ($i+1) ? '✓' : ($i+1) }}
                    </div>
                    <span class="text-xs mt-1 hidden sm:block {{ $step == ($i+1) ? 'text-blue-600 font-semibold' : 'text-gray-400' }}">{{ $label }}</span>
                </div>
                @if($i < count($steps) - 1)
                    <div class="flex-1 h-1 {{ $step > ($i+1) ? 'bg-green-400' : 'bg-gray-200' }} mx-1 rounded mt-[-18px]"></div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Step Content --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

        {{-- Step 1: Select Station --}}
        @if($step === 1)
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Step 1: Select a Station</h2>
        @error('selectedStationId') <p class="text-red-500 text-sm mb-4">{{ $message }}</p> @enderror
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($stations as $s)
            <button wire:click="$set('selectedStationId', {{ $s->id }})"
                class="text-left p-4 border-2 rounded-xl transition
                    {{ $selectedStationId == $s->id ? 'border-blue-600 bg-blue-50' : 'border-gray-200 hover:border-blue-300' }}">
                @if($s->photo)
                    <img src="{{ asset('storage/'.$s->photo) }}" class="w-full h-32 object-cover rounded-lg mb-3">
                @endif
                <p class="font-bold text-gray-900">{{ $s->name }}</p>
                <p class="text-sm text-gray-500">{{ $s->location_name }}</p>
                @if($s->timeSlotTemplates->isNotEmpty())
                    <p class="text-blue-600 text-sm font-medium mt-1">From ${{ number_format($s->timeSlotTemplates->min('price'), 2) }}/slot</p>
                @endif
            </button>
            @endforeach
        </div>
        @endif

        {{-- Step 2: Choose Slots --}}
        @if($step === 2)
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Step 2: Choose Time Slots</h2>
        <p class="text-gray-500 text-sm mb-6">Select the dates and times you want your advert to play.</p>
        @error('selectedSlots') <p class="text-red-500 text-sm mb-4">{{ $message }}</p> @enderror

        <div class="flex items-center justify-between mb-4">
            <button wire:click="prevMonth" class="p-2 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <h3 class="font-semibold text-gray-900">
                {{ \Carbon\Carbon::createFromDate($calendarYear, $calendarMonth, 1)->format('F Y') }}
            </h3>
            <button wire:click="nextMonth" class="p-2 rounded-lg hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        @if(empty($slotTemplates))
            <p class="text-gray-500 text-sm">No time slots configured for this station.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="text-left p-2 font-semibold text-gray-600">Time Slot</th>
                            @foreach($calendarDays as $day)
                                <th class="p-1 text-center font-medium min-w-[40px]
                                    {{ $day->isToday() ? 'text-blue-600' : ($day->isPast() ? 'text-gray-300' : 'text-gray-600') }}">
                                    <div>{{ $day->format('D') }}</div>
                                    <div>{{ $day->format('j') }}</div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($slotTemplates as $template)
                        <tr class="border-t border-gray-100">
                            <td class="p-2 font-medium text-gray-700 whitespace-nowrap">
                                {{ substr($template['start_time'], 0, 5) }} – {{ substr($template['end_time'], 0, 5) }}
                                <span class="block text-xs text-blue-600">${{ number_format($template['price'], 2) }}</span>
                            </td>
                            @foreach($calendarDays as $day)
                                @php
                                    $isBooked = $this->isSlotBooked($template['id'], $day->format('Y-m-d'));
                                    $isSelected = $this->isSlotSelected($template['id'], $day->format('Y-m-d'));
                                    $isPast = $day->isPast() && !$day->isToday();
                                    $wrongDay = $day->dayOfWeek !== $template['day_of_week'];
                                @endphp
                                <td class="p-1 text-center">
                                    @if($wrongDay || $isPast)
                                        <span class="block w-7 h-7 mx-auto rounded opacity-20 bg-gray-200"></span>
                                    @elseif($isBooked)
                                        <span class="flex w-7 h-7 mx-auto rounded bg-red-100 text-red-400 text-xs items-center justify-center" title="Booked">×</span>
                                    @else
                                        <button wire:click="toggleSlot({{ $template['id'] }}, '{{ $day->format('Y-m-d') }}')"
                                            class="block w-7 h-7 mx-auto rounded transition text-xs font-bold
                                                {{ $isSelected ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-blue-100 text-gray-400' }}">
                                            {{ $isSelected ? '✓' : '' }}
                                        </button>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex gap-4 mt-4 text-xs text-gray-500">
                <span class="flex items-center gap-1"><span class="w-4 h-4 bg-blue-600 rounded inline-block"></span> Selected</span>
                <span class="flex items-center gap-1"><span class="w-4 h-4 bg-red-100 rounded inline-block"></span> Booked</span>
                <span class="flex items-center gap-1"><span class="w-4 h-4 bg-gray-100 rounded inline-block"></span> Available</span>
            </div>

            @if(count($selectedSlots) > 0)
                <div class="mt-4 p-3 bg-blue-50 rounded-lg text-sm text-blue-700 font-medium">
                    {{ count($selectedSlots) }} slot(s) selected — Total: ${{ number_format($pricing['total'], 2) }}
                    @if($pricing['discountRate'] > 0)
                        <span class="text-green-600">({{ ($pricing['discountRate'] * 100) }}% bulk discount applied!)</span>
                    @endif
                </div>
            @endif
        @endif
        @endif

        {{-- Step 3: Upload Media --}}
        @if($step === 3)
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Step 3: Upload Your Advert</h2>
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Advert Title *</label>
                <input wire:model="advertTitle" type="text" placeholder="e.g. Summer Sale Promo"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('advertTitle') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Media File *</label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition">
                    <input wire:model="mediaFile" type="file" id="mediaFile" accept=".mp4,.mov,.jpg,.jpeg,.png,.webp" class="hidden">
                    <label for="mediaFile" class="cursor-pointer">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        @if($mediaFile)
                            <p class="text-green-600 font-medium">{{ $mediaFile->getClientOriginalName() }}</p>
                            <p class="text-gray-400 text-xs mt-1">{{ number_format($mediaFile->getSize() / 1048576, 2) }} MB</p>
                        @else
                            <p class="text-gray-500">Click to upload or drag and drop</p>
                            <p class="text-gray-400 text-xs mt-1">MP4, MOV (max 100MB) · JPG, PNG, WebP (max 5MB)</p>
                        @endif
                    </label>
                </div>
                @error('mediaFile') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        @endif

        {{-- Step 4: Review --}}
        @if($step === 4)
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Step 4: Review Your Order</h2>
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Station</p>
                    <p class="font-bold text-gray-900">{{ $station?->name }}</p>
                    <p class="text-sm text-gray-500">{{ $station?->location_name }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Advert</p>
                    <p class="font-bold text-gray-900">{{ $advertTitle }}</p>
                    @if($mediaFile)
                        <p class="text-sm text-gray-500">{{ $mediaFile->getClientOriginalName() }}</p>
                    @endif
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-3">Selected Slots ({{ count($selectedSlots) }})</p>
                <div class="space-y-1 max-h-40 overflow-y-auto">
                    @foreach($selectedSlots as $slot)
                        @php $tpl = \App\Models\TimeSlotTemplate::find($slot['template_id']); @endphp
                        <div class="flex justify-between text-sm">
                            <span>{{ \Carbon\Carbon::parse($slot['air_date'])->format('D, M j Y') }}</span>
                            <span class="text-gray-600">{{ $tpl ? substr($tpl->start_time, 0, 5).' – '.substr($tpl->end_time, 0, 5) : '' }}</span>
                            <span class="font-medium text-blue-600">${{ $tpl ? number_format($tpl->price, 2) : '—' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 space-y-2">
                <div class="flex justify-between text-sm"><span class="text-gray-600">Subtotal</span><span>${{ number_format($pricing['base'], 2) }}</span></div>
                @if($pricing['discount'] > 0)
                    <div class="flex justify-between text-sm text-green-600">
                        <span>Bulk Discount ({{ ($pricing['discountRate'] * 100) }}%)</span>
                        <span>-${{ number_format($pricing['discount'], 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-lg border-t pt-2">
                    <span>Total</span>
                    <span class="text-blue-600">${{ number_format($pricing['total'], 2) }}</span>
                </div>
            </div>

            @guest
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
                    ⚠️ You need to <a href="{{ route('login') }}" class="underline font-semibold">log in</a> or <a href="{{ route('register') }}" class="underline font-semibold">register</a> to complete your booking.
                </div>
            @endguest
        </div>
        @endif

        {{-- Step 5: Payment --}}
        @if($step === 5)
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Step 5: Payment</h2>
        <p class="text-gray-500 text-sm mb-6">Choose how you'd like to pay for your booking.</p>

        {{-- Amount summary --}}
        @if($booking)
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 flex justify-between items-center">
            <div>
                <p class="text-sm text-blue-700 font-medium">Booking #{{ $booking->id }}</p>
                <p class="text-xs text-blue-500">{{ $booking->slot_count }} slot(s) · {{ $booking->station->name }}</p>
            </div>
            <p class="text-2xl font-bold text-blue-700">${{ number_format($booking->total_price, 2) }}</p>
        </div>
        @endif

        @error('payment') <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4 text-red-600 text-sm">{{ $message }}</div> @enderror

        {{-- Payment method selector --}}
        @if(! $paymentMethod)
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Stripe card --}}
            <button wire:click="selectPaymentMethod('stripe')"
                class="flex flex-col items-center gap-3 p-6 border-2 border-gray-200 rounded-2xl hover:border-blue-500 hover:bg-blue-50 transition group">
                <svg class="w-10 h-10 text-indigo-500 group-hover:text-blue-600" viewBox="0 0 38 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="38" height="24" rx="4" fill="#F0F4FF"/>
                    <path d="M14 15.5c0 .83-.67 1.5-1.5 1.5h-5C6.67 17 6 16.33 6 15.5v-7C6 7.67 6.67 7 7.5 7h5c.83 0 1.5.67 1.5 1.5v7zM32 10h-9v4h9v-4z" fill="#6366F1"/>
                    <circle cx="27" cy="14" r="3" fill="#818CF8"/>
                    <circle cx="23" cy="14" r="3" fill="#4F46E5"/>
                </svg>
                <div class="text-center">
                    <p class="font-bold text-gray-900">Pay with Card</p>
                    <p class="text-xs text-gray-500 mt-1">Visa, Mastercard, Amex via Stripe</p>
                </div>
            </button>

            {{-- EcoCash --}}
            <button wire:click="selectPaymentMethod('ecocash')"
                class="flex flex-col items-center gap-3 p-6 border-2 border-gray-200 rounded-2xl hover:border-green-500 hover:bg-green-50 transition group">
                <svg class="w-10 h-10 text-green-500" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="40" rx="8" fill="#DCFCE7"/>
                    <path d="M20 10c-5.52 0-10 4.48-10 10s4.48 10 10 10 10-4.48 10-10S25.52 10 20 10zm1 17.93V26h-2v1.93C14.06 27.48 11 24.08 11 20c0-4.08 3.06-7.48 7-7.93V14h2v-1.93C24.94 12.52 28 15.92 28 20c0 4.08-3.06 7.48-7 7.93zM20 16c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4z" fill="#16A34A"/>
                </svg>
                <div class="text-center">
                    <p class="font-bold text-gray-900">Pay with EcoCash</p>
                    <p class="text-xs text-gray-500 mt-1">Zimbabwe mobile money</p>
                </div>
            </button>

        </div>
        @endif

        {{-- Stripe Elements form --}}
        @if($paymentMethod === 'stripe' && $stripeClientSecret)
        <div
            x-data="stripePayment(@js($stripeClientSecret), @js($stripeKey))"
            x-init="init()"
        >
            <div class="flex items-center gap-2 mb-4">
                <button wire:click="$set('paymentMethod', null)" class="text-gray-400 hover:text-gray-600 transition">
                    ← Change method
                </button>
                <span class="text-gray-300">|</span>
                <span class="text-sm font-medium text-gray-700">Pay by Card</span>
            </div>

            <div id="stripe-payment-element" class="border border-gray-200 rounded-xl p-4 mb-4"></div>

            <div x-show="errorMessage" x-text="errorMessage"
                class="text-red-600 text-sm bg-red-50 border border-red-200 rounded-lg px-4 py-2 mb-4"></div>

            <button
                @click="pay($wire)"
                :disabled="loading"
                class="w-full bg-indigo-600 text-white font-bold py-3 rounded-xl hover:bg-indigo-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
                <span x-show="!loading">
                    Pay ${{ $booking ? number_format($booking->total_price, 2) : '0.00' }}
                </span>
                <span x-show="loading" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Processing...
                </span>
            </button>
        </div>
        @endif

        {{-- Stripe loading (intent being created) --}}
        @if($paymentMethod === 'stripe' && ! $stripeClientSecret)
        <div class="flex items-center justify-center py-10 gap-3 text-gray-500">
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            Preparing payment...
        </div>
        @endif

        {{-- EcoCash form --}}
        @if($paymentMethod === 'ecocash')
        <div>
            <div class="flex items-center gap-2 mb-4">
                <button wire:click="$set('paymentMethod', null)" class="text-gray-400 hover:text-gray-600 transition">
                    ← Change method
                </button>
                <span class="text-gray-300">|</span>
                <span class="text-sm font-medium text-gray-700">Pay with EcoCash</span>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-5 text-sm text-green-800 space-y-1">
                <p class="font-semibold">How EcoCash payment works:</p>
                <ol class="list-decimal list-inside space-y-1 mt-2">
                    <li>Enter your EcoCash number below and submit.</li>
                    <li>You'll receive a USSD prompt on your phone — approve the payment.</li>
                    <li>Our team will confirm receipt and activate your booking.</li>
                </ol>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Your EcoCash Number *</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm rounded-l-lg">
                        +263
                    </span>
                    <input wire:model="ecocashPhone"
                        type="tel"
                        placeholder="771234567"
                        class="flex-1 border border-gray-300 rounded-r-lg px-4 py-2.5 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
                @error('ecocashPhone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p class="text-xs text-gray-400 mt-1">e.g. 0771234567 or +263771234567</p>
            </div>

            <button wire:click="submitEcocashPayment" wire:loading.attr="disabled"
                class="w-full bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="submitEcocashPayment">
                    Submit EcoCash Payment — ${{ $booking ? number_format($booking->total_price, 2) : '0.00' }}
                </span>
                <span wire:loading wire:target="submitEcocashPayment" class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    Submitting...
                </span>
            </button>
        </div>
        @endif

        @endif

        {{-- Step 6: Confirmation --}}
        @if($step === 6)
        <div class="text-center py-8">
            @if($booking?->payment?->status === 'completed')
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h2>
                <p class="text-gray-500 mb-6">Your payment has been confirmed. Your booking is under review and you'll receive an email once your advert is approved.</p>
            @else
                <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Pending</h2>
                <p class="text-gray-500 mb-6">We've received your EcoCash payment request. Once confirmed by our team, your booking will be activated.</p>
            @endif

            @if($booking)
                <div class="bg-gray-50 rounded-xl p-4 text-sm text-left max-w-sm mx-auto mb-6 space-y-1">
                    <p><span class="font-medium">Booking ID:</span> #{{ $booking->id }}</p>
                    <p><span class="font-medium">Station:</span> {{ $booking->station->name }}</p>
                    <p><span class="font-medium">Total:</span> ${{ number_format($booking->total_price, 2) }}</p>
                    <p><span class="font-medium">Payment:</span>
                        @if($booking->payment?->status === 'completed')
                            <span class="text-green-600 font-semibold">Paid via {{ ucfirst($booking->payment->provider) }}</span>
                        @else
                            <span class="text-yellow-600 font-semibold">Pending (EcoCash)</span>
                        @endif
                    </p>
                    <p><span class="font-medium">Status:</span> <span class="text-yellow-600 font-semibold">{{ ucfirst($booking->status) }}</span></p>
                </div>
            @endif

            <a href="{{ route('my-adverts') }}" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition">
                View My Adverts
            </a>
        </div>
        @endif

    </div>

    {{-- Navigation Buttons --}}
    @if($step < 6)
    <div class="flex justify-between mt-6">
        <div>
            @if($step > 1 && $step < 5)
            <button wire:click="prevStep" class="border border-gray-300 text-gray-700 px-6 py-2.5 rounded-xl font-medium hover:bg-gray-50 transition">
                ← Back
            </button>
            @endif
        </div>
        <div>
            @if($step < 4)
            <button wire:click="nextStep" class="bg-blue-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-blue-700 transition">
                Next →
            </button>
            @elseif($step === 4)
            @auth
            <button wire:click="confirmBooking" wire:loading.attr="disabled" class="bg-green-600 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-green-700 transition">
                <span wire:loading.remove>Confirm & Proceed to Payment →</span>
                <span wire:loading>Submitting...</span>
            </button>
            @else
            <a href="{{ route('login') }}" class="bg-blue-600 text-white px-8 py-2.5 rounded-xl font-bold hover:bg-blue-700 transition">
                Login to Book
            </a>
            @endauth
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
function stripePayment(clientSecret, publishableKey) {
    return {
        stripe: null,
        elements: null,
        paymentElement: null,
        loading: false,
        errorMessage: '',

        init() {
            this.stripe = Stripe(publishableKey);
            this.elements = this.stripe.elements({ clientSecret });
            this.paymentElement = this.elements.create('payment');
            this.paymentElement.mount('#stripe-payment-element');
        },

        async pay(wire) {
            this.loading = true;
            this.errorMessage = '';

            const { error, paymentIntent } = await this.stripe.confirmPayment({
                elements: this.elements,
                redirect: 'if_required',
            });

            if (error) {
                this.errorMessage = error.message;
                this.loading = false;
                return;
            }

            if (paymentIntent && paymentIntent.status === 'succeeded') {
                await wire.recordStripePayment(paymentIntent.id);
            } else {
                this.errorMessage = 'Payment did not complete. Please try again.';
                this.loading = false;
            }
        },
    };
}
</script>
@endpush
