<div class="rounded-md relative">
    <div class="flex">

        <img src="{{asset($contract->customer?->logo ?'storage/'.$contract->customer?->logo: "storage/contrat.png
        ")}} " class=" img-co overflow-hidden rounded-3xl" alt="">

        <div class="flex flex-col ml-3 text-xs">
            <span class="font-medium">N°: {{$contract->number}}</span>
            <span class="font-medium">Client: {{$contract->customer?->name}} {{$contract->customer_name}}</span>
            <div class="">Contact: {{ $contract->customer?->contact_c_phone }}<br>{{ $contract->customer?->contact_c_email
                }}
            </div>
            {{-- <span class="text-primary-500"> {{ $customer->city->name }}</span> --}}
        </div>
    </div>
</div>

<style>
    .img-co {
        width: 70px;
        height: 70px;
        overflow: hidden;
        object-fit: cover;
    }

    .ml-3 {
        margin-left: 0.75rem;
    }

    .font-medium {
        font-weight: 500;
    }
</style>