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
    <table class="mb-8 w-full">
      <tbody>
        <tr>
          <td class="p-0 align-top">
            <p class="mb-4 text-sm">
              <strong> {{ __('invoices::invoice.description') }} </strong>
            </p>
          </td>
        </tr>
        <tr>
          <td class="p-0 align-top">
            <div class="text-sm">{!! $proposal->description !!}</div>
          </td>
        </tr>
      </tbody>
    </table>
  @endif

  <table class="mb-8 w-full">
    <tbody>
      <tr>
        <td class="p-0 align-top">
          <p class="mb-5 text-sm">
              <strong> {{ __('Sub total') }} </strong>
          </p>
        </td>
        <td class="p-0 align-top">
          <p class="mb-5 text-sm">
              <strong> {{ __('Tax amount') }} </strong>
          </p>
        </td>
        <td class="p-0 align-top">
          <p class="mb-5 text-sm">
              <strong> {{ __('Total') }} </strong>
          </p>
        </td>
      </tr>
      <tr>
        <td class="p-0 align-top">
          <p class="mb-5 text-sm">
            {{ $proposal->currency }}
            {{ $proposal->subtotal_amount }}
          </p>
        </td>
        <td class="p-0 align-top">
          <p class="mb-5 text-sm">
            {{ $proposal->tax_amount }}
          </p>
        </td>
        <td class="p-0 align-top">
          <p class="mb-5 text-sm">
            {{ $proposal->currency }}
            {{ $proposal->total_amount }}
          </p>
        </td>
      </tr>
    </tbody>
  </table>

  @if ($proposal->expires_at)
    <table class="mb-8 w-full">
      <tbody>
        <tr>
          <td class="p-0 align-top">
            <p class="mb-4 text-sm">
              <strong> {{ __('This proposal will expire on:') }} {{ date_format($proposal->expires_at, 'F d Y') }}</strong>
            </p>
          </td>
        </tr>
      </tbody>
    </table>
  @endif

</div>
