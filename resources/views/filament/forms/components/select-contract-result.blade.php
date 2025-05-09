<div class="rounded-md relative">
    <div class="flex">

        <img src="{{asset('storage/'.$contract->customer->logo)}} " class=" img overflow-hidden rounded-3xl" alt="">

        <div class="flex flex-col ml-3 text-xs">
            <span class="font-medium">Client: {{$contract->customer->name}}</span>
            <span class="font-medium">Site: {{$contract->site}}</span>
            <div class="">Contact: {{ $contract->customer->contact_c_phone }}<br>{{ $contract->customer->contact_c_email
                }}
            </div>
            {{-- <span class="text-primary-500"> {{ $customer->city->name }}</span> --}}
        </div>
    </div>
</div>

<style>
    .img {
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