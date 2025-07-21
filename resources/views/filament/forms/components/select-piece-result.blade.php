<div class="rounded-md w-full">
    <div class="flex w-full">

        <img src="{{asset('storage/' . $piece->image)}}" class="img overflow-hidden" alt="">

        <div class=" ml-10 justify-center text-xs w-full">
            <div class="font-medium pb-1">{{ $piece->reference}} - {{ $piece->designation }}</div>
            <!-- <span class="inline-flex ">
                {{ $piece->duree_vie}} h</span> -->
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

    .ml-10 {
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
        border: yellow 1px solid;
    }

    .ring-blue-600\/10 {
        border: blue 1px solid;
    }

    .ring-red-600\/10 {
        border: red 1px solid;
    }
</style>