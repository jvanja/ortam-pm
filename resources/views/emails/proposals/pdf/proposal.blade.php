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

    @if ($proposal->description)
        <p class="mb-2 text-sm">
            <strong> {{ __('invoices::invoice.description') }} </strong>
        </p>
        <p class="whitespace-pre-line text-xs">{!! $proposal->description !!}</p>
    @endif

</div>
