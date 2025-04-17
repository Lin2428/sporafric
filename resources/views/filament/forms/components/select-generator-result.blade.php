<?php
$bg = match($generator->status) {
    0 => 'bg-green-50',
    1 => 'bg-yellow-50',
    2 => 'bg-blue-50',
    default => 'bg-red-50',
};

$text = match($generator->status) {
    0 => 'text-green-700',
    1 => 'text-yellow-700',
    2 => 'text-blue-700',
    default => 'text-red-700',
};

$ring = match($generator->status) {
    0 => 'ring-green-600/10',
    1 => 'ring-yellow-600/10',
    2 => 'ring-blue-600/10',
    default => 'ring-red-600/10',
};
?>

<div class="rounded-md w-full">
    <div class="flex w-full">
   
        <img src="{{asset('storage/' . $generator->image)}}" class="img overflow-hidden" alt="">

        <div class=" ml-10 justify-center text-xs w-full">
            <div class="font-medium pb-1">{{ $generator->name}} - {{ $generator->modele }}</div>
            <div class="flex items-center">
                {{$generator->power}}KVA
            </div>

            <span class="inline-flex items-center rounded-md {{$bg}} px-2 py-1 text-xs font-medium {{$text}} ring-1 {{$ring}} ring-inset"> {{ \App\Enum\GeneratorStatus::from($generator->status)->label() }}</span>
        </div>
    </div>
</div>

<style>
    .img {
        width: 70%;
        height: 70px;
        overflow: hidden;
        object-fit: cover;
    }

    .ml-10{
        margin-left: 1rem;
    }

    .font-medium {
        font-weight: 500;
    }

    /* .text-xs {
        font-size: 0.75rem;
    } */

    .bg-green-50 {
        background-color: #f0fdf4;
    }
    .bg-yellow-50 {
        background-color: #fefcbf;
    }
    .bg-blue-50 {
        background-color: #eff6ff;
    }
    .bg-red-50 {
        background-color: #fee2e2;
    }
    .text-green-700 {
        color: #047857;
    }
    .text-yellow-700 {
        color: #ca8a04;
    }
    .text-blue-700 {
        color: #1d4ed8;
    }
    .text-red-700 {
        color: #b91c1c;
    }
    .ring-green-600\/10 {
        border: green 1px solid;
    }
    .ring-yellow-600\/10 {
        border: yellow  1px solid;
    }
    .ring-blue-600\/10 {
        border: blue  1px solid;
    }
    .ring-red-600\/10 {
        border: red  1px solid;
    }

</style>