<div>
    <table class="mb-8 w-full">
        <tbody>
            <tr>
                <td class="p-0 align-top">
                    <h1 class="mb-1 text-2xl">
                        <strong>Proposal</strong>
                    </h1>
                    <p class="mb-5 text-sm">
                        {{ $proposal->state }}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="mb-6 w-full">
        <tbody>
            <tr>
                <td class="p-0 align-top" width="33%">
                    <p class="mb-1 pb-1 text-xs text-gray-500">{{ __('invoices::invoice.from') }}</p>

                    @include('invoices::default.includes.party', [
                        'party' => $proposal->seller,
                    ])
                </td>
                <td class="p-0 align-top" width="33%">
                    <p class="mb-1 pb-1 text-xs text-gray-500">{{ __('invoices::invoice.to') }}</p>

                    @include('invoices::default.includes.party', [
                        'party' => $proposal->buyer,
                    ])
                </td>

                @if ($proposal->buyer->shipping_address)
                    <td class="p-0 align-top" width="33%">

                        <p class="mb-1 whitespace-nowrap pb-1 text-xs text-gray-500">
                            {{ __('invoices::invoice.shipping_to') }}
                        </p>

                        @if ($proposal->buyer->shipping_address)
                            @include('invoices::default.includes.address', [
                                'address' => $proposal->buyer->shipping_address,
                            ])
                        @endif
                    </td>
                @endif

            </tr>
        </tbody>
    </table>
    @if ($proposal->description)
        <p class="mb-2 text-sm">
            <strong> {{ __('invoices::invoice.description') }} </strong>
        </p>
        <p class="whitespace-pre-line text-xs">{!! $proposal->description !!}</p>
    @endif

</div>
