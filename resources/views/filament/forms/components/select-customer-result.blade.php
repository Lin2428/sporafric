<div class="rounded-md relative">
    <div class="flex">

        <img src="{{asset($customer->logo ? "storage/$customer->logo" : "storage/customer.png")}}" class="img overflow-hidden rounded-3xl" alt="">

        <div class="flex flex-col ml-3 text-xs">
            <span class="font-medium">{{$customer->name}}</span>
            <div class="">{{ $customer->contact_c_phone }}<br>{{ $customer->contact_c_email }}
            </div>
            {{-- <span class="text-primary-500"> {{ $customer->city->name }}</span> --}}
        </div>
    </div>
</div>

<style>
    .img{
        width: 70px;
        height: 70px;
        overflow: hidden;
        object-fit: cover;
    }

    .ml-3{
        margin-left: 0.75rem;
    }

    .font-medium {
        font-weight: 500;
    }
</style>